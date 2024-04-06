<?php

namespace HRHub\Controller\V1;

use DateTime;
use Doctrine\ORM\EntityManager;
use HRHub\Entity\Employee;
use HRHub\Entity\WPUser;
use HRHub\Service\EmployeeService;
use JMS\Serializer\Serializer;

class EmployeesController extends \WP_REST_Controller {

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
	 * Constructor
	 *
	 * @param EmployeeService $entity_service
	 * @param Serializer $serializer
	 */
	public function __construct(
		protected EmployeeService $entity_service,
		protected Serializer $serializer
	) {
		$this->entity_service = $entity_service;
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'create_item' ],
					'permission_callback' => [ $this, 'create_item_permissions_check' ],
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			[
				'args' => [
					'id' => [
						'description' => __( 'Unique identifier for the employee.' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'delete_item_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Create item.
	 *
	 * @param \WP_REST_Request $request
	 * @return void
	 */
	public function create_item( $request ) {
		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$item = $this->entity_service->create( $data );
		if ( is_wp_error( $item ) ) {
			return $item;
		}
		$response = rest_ensure_response( $this->serializer->toArray( $item ) );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $item->get_id() ) ) );
		return $response;
	}

	/**
	 * Prepare items form database.
	 *
	 * @param \WP_REST_Request $request
	 * @return array|\WP_Error
	 */
	protected function prepare_item_for_database( $request ) {
		$data = $request->get_params();

		if ( isset( $data['date_of_birth'] ) ) {
			$timestamp             = strtotime( $data['date_of_birth'] );
			$data['date_of_birth'] = ( new DateTime( "@$timestamp" ) );
		}

		if ( isset( $data['date_of_employment'] ) ) {
			$timestamp                  = strtotime( $data['date_of_employment'] );
			$data['date_of_employment'] = ( new DateTime( "@$timestamp" ) );
		}

		if ( ! isset( $data['employment_type'] ) ) {
			$data['employment_type'] = 'full-time';
		}

		$wp_user_id = $this->create_wp_user( $data['email'] );

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
		$data['wp_user_id'] = $user;

		return $data;
	}

	/**
	 * Create WP user.
	 *
	 * @param string $email
	 * @return int|\WP_Error
	 */
	protected function create_wp_user( $email ) {
		$user_id = email_exists( $email );
		if ( ! $user_id ) {
			$username = $this->generate_username_from_email( $email );
			$user_id  = wp_create_user( $username, wp_generate_password(), $email );
		}

		return $user_id;
	}

	protected function generate_username_from_email( $email, $suffix = '' ) {
		$email_parts    = explode( '@', $email );
		$email_username = $email_parts[0];

		if ( in_array(
			$email_username,
			array(
				'sales',
				'hello',
				'mail',
				'contact',
				'info',
			),
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

	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function create_item_permissions_check( $request ) {
		return true;
	}

	public function delete_item_permissions_check( $request ) {
		return true;
	}

	public function get_item_permissions_check( $request ) {
		return true;
	}

	public function update_item_permissions_check( $request ) {
		return true;
	}

	/**
	 * Get items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {
		$query_args = $request->get_query_params();
		$employees  = $this->entity_service->list( $query_args );
		return rest_ensure_response( $employees );
	}

	/**
	 * Update item.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$id = $request['id'];

		$employee = $this->entity_service->em->find( Employee::class, $id );
		if ( ! $employee ) {
			return new \WP_Error( 'not_found', 'Employee not found.', [ 'status' => 404 ] );
		}
		$data = $this->prepare_item_for_database( $request );
		unset( $data['id'] );
		$employee = $this->entity_service->update( $employee, $data );
		return rest_ensure_response( $this->serializer->toArray( $employee ) );
	}

	/**
	 * Get items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		$id   = $request['id'];
		$item = $this->entity_service->em->find( Employee::class, $id );
		if ( ! $item ) {
			return new \WP_Error( 'not_found', 'Employee not found.', [ 'status' => 404 ] );
		}
		return rest_ensure_response( $this->serializer->toArray( $item ) );
	}

	/**
	 * Delete item.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function delete_item( $request ) {
		$id   = $request['id'];
		$item = $this->entity_service->delete( $id );

		if ( is_wp_error( $item ) ) {
			return $item;
		}

		return rest_ensure_response( $this->serializer->toArray( $item ) );
	}
}
