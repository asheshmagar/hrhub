<?php

namespace HRHub\Controller\V1;

use HRHub\Entity\Department;

class DepartmentsController extends AbstractEntitiesController {

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
	protected $rest_base = 'departments';

	/**
	 * Entity.
	 *
	 * @var string
	 */
	protected $entity = Department::class;

	/**
	 * Prepare a single department entity for response.
	 *
	 * @param Department $department
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function prepare_item_for_response( $department, $request ) {
		$data = $this->serializer->toArray( $department );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $department ) );

		return $this->filter( 'hrhub:rest:prepare:department', $response, $department, $request );
	}

	/**
	 * Prepares a single department for create or update.
	 *
	 * @param \WP_REST_Request $request
	 * @return Department|\WP_Error
	 */
	protected function prepare_item_for_database( $request ) {
		$id   = $request['id'] ?? 0;
		$name = sanitize_text_field( wp_unslash( $request['name'] ) );

		$existing = $this->existing_department( $name, $id );

		if ( $existing ) {
			return new \WP_Error( 'department_exists', sprintf( 'Department %s already exists.', $name ), [ 'status' => 400 ] );
		}

		if ( ! $id ) {
			$position = new Department();
		} else {
			/**
			 * @var Department
			 */
			$position = $this->entity_service->em->find( Department::class, $id );
		}

		$position->set_name( $name );

		if ( isset( $request['description'] ) ) {
			$position->set_description( sanitize_textarea_field( wp_unslash( $request['description'] ) ) );
		}

		return $this->filter( 'hrhub:rest:department:pre-insert', $position, $request );
	}

	/**
	 * Check existing department.
	 *
	 * @param string $name
	 * @param integer $id
	 * @return boolean
	 */
	protected function existing_department( $name, $id = 0 ) {
		$criteria = [ 'name' => $name ];
		if ( $id ) {
			$criteria['id'] = [ '<>' => $id ];
		}
		$query_builder = $this->entity_service->em->getRepository( Department::class )->createQueryBuilder( 'd' );
		$query_builder->where( 'd.name = :name' )
					->setParameter( 'name', $name );
		if ( $id ) {
			$query_builder->andWhere( 'd.id <> :id' )
						->setParameter( 'id', $id );
		}
		$query_builder->setMaxResults( 1 );
		$entity = $query_builder->getQuery()->getOneOrNullResult();
		return (bool) $entity;
	}
}
