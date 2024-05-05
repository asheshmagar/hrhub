<?php

namespace HRHub\Controller\V1;

use DateTime;
use HRHub\Entity\Department;
use HRHub\Entity\Employee;
use HRHub\Entity\Position;
use HRHub\Entity\WPUser;

class EmployeesController extends AbstractEntitiesController {

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'hrhub/v1';

	/**
	 * Rest base.
	 *
	 * @var string
	 */
	protected $rest_base = 'employees';

	/**
	 * Entity class.
	 *
	 * @var string
	 */
	protected $entity = Employee::class;

	/**
	 * Prepares a single employee for create or update.
	 *
	 * @param \WP_REST_Request $request
	 * @return Employee|\WP_Error
	 */
	protected function prepare_item_for_database( $request ) {
		$id = $request['id'] ?? 0;

		if ( ! $id ) {
			$employee = new Employee();
		} else {
			/**
			 * @var Employee
			 */
			$employee = $this->entity_service->em->find( Employee::class, $id );
		}

		if ( isset( $request['name'] ) ) {
			$employee->set_name( wp_filter_post_kses( $request['name'] ) );
		}

		if ( isset( $request['address'] ) ) {
			$employee->set_address( wp_filter_post_kses( $request['address'] ) );
		}

		if ( isset( $request['email'] ) ) {
			$employee->set_email( sanitize_email( wp_unslash( $request['email'] ) ) );
		}

		if ( isset( $request['documents'] ) ) {
			$employee->set_documents( $request['documents'] );
		}

		if ( isset( $request['salary'] ) ) {
			$employee->set_salary( $request['salary'] );
		}

		if ( isset( $request['phone_number'] ) ) {
			$employee->set_phone_number( preg_replace( '/[^0-9]/', '', wp_unslash( $request['phone_number'] ) ) );
		}

		if ( isset( $request['date_of_birth'] ) ) {
			$timestamp = strtotime( $request['date_of_birth'] );
			$employee->set_date_of_birth( new DateTime( "@$timestamp" ) );
		}

		if ( isset( $request['date_of_employment'] ) ) {
			$timestamp = strtotime( $request['date_of_employment'] );
			$employee->set_date_of_employment( new DateTime( "@$timestamp" ) );
		}

		if ( isset( $request['position'] ) ) {
			$position = $this->entity_service->em->find( Position::class, $request['position'] );

			if ( ! $position ) {
				return new \WP_Error(
					'rest_invalid_position',
					__( 'Invalid position.' ),
					[ 'status' => 400 ]
				);
			}
			$employee->set_position( $position );
		}

		if ( isset( $request['department'] ) ) {
			$department = $this->entity_service->em->find( Department::class, $request['department'] );

			if ( ! $department ) {
				return new \WP_Error(
					'rest_invalid_department',
					__( 'Invalid department.' ),
					[ 'status' => 400 ]
				);
			}
			$employee->set_department( $department );
		}

		$employment_type = $request['employment_type'] ?? 'full-time';
		$employee->set_employment_type( $employment_type );

		$status = $request['status'] ?? 'inactive';
		$employee->set_status( $status );

		$wp_user_id = $this->create_wp_user( $request['email'] );

		if ( is_wp_error( $wp_user_id ) ) {
			return $wp_user_id;
		}

		$user = $this->entity_service->em->find( WPUser::class, $wp_user_id );

		if ( ! $user ) {
			return new \WP_Error(
				'rest_invalid_wp_user',
				__( 'Invalid user.' ),
				[ 'status' => 400 ]
			);
		}

		$employee->set_wp_user_id( $user );

		return $this->filter( 'rest:employees:pre-insert', $employee, $request );
	}

	/**
	 * Create WP user.
	 *
	 * @param string $email
	 * @return int|\WP_Error
	 */
	protected function create_wp_user( $email ) {
		$employee_id = email_exists( $email );
		if ( ! $employee_id ) {
			$username          = $this->generate_username_from_email( $email );
			$password          = wp_generate_password();
			$new_employee_data = [
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => 'hrhub_employee',
			];
			$employee_id       = wp_insert_user( $new_employee_data );

			if ( is_wp_error( $employee_id ) ) {
				return $employee_id;
			}

			$this->action( 'employee:created', $employee_id, $new_employee_data, );
		}

		return $employee_id;
	}

	/**
	 * Generate username from email.
	 *
	 * @param string $email
	 * @param string $suffix
	 * @return string
	 */
	protected function generate_username_from_email( $email, $suffix = '' ) {
		$email_parts    = explode( '@', $email );
		$email_username = $email_parts[0];

		if ( in_array(
			$email_username,
			[
				'sales',
				'hello',
				'mail',
				'contact',
				'info',
			],
			true
		) ) {
			$email_username = $email_parts[1];
		}
		$username_parts[] = sanitize_user( $email_username, true );
		$username         = strtolower( implode( '.', $username_parts ) );
		if ( $suffix ) {
			$username .= $suffix;
		}
		if ( username_exists( $username ) ) {
			$suffix = '-' . zeroise( random_int( 1, 999 ), 3 );
			return $this->generate_username_from_email( $email, $suffix );
		}

		return $username;
	}


	/**
	 * Prepare a single employee output for response.
	 *
	 * @param Employee $employee.
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $employee, $request ) {
		$data = $this->serializer->toArray( $employee );

		unset( $data['wp_user_id']['user_pass'] );
		unset( $data['wp_user_id']['user_activation_key'] );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $employee ) );

		return $this->filter( 'rest:prepare:employee', $response, $employee, $request );
	}
}
