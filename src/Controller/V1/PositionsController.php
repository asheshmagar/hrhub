<?php

namespace HRHub\Controller\V1;

use HRHub\Entity\Position;
use HRHub\Service\PositionService;
use HRHub\Traits\Hook;
use JMS\Serializer\Serializer;

class PositionsController extends AbstractEntitiesController {

	use Hook;

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
	protected $rest_base = 'positions';

	/**
	 * Entity.
	 *
	 * @var string
	 */
	protected $entity = Position::class;

	/**
	 * Prepare a single position entity for response.
	 *
	 * @param Position $position
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function prepare_item_for_response( $position, $request ) {
		$data = $this->serializer->toArray( $position );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $position ) );

		return $this->filter( 'rest:prepare:position', $response, $position, $request );
	}

	/**
	 * Prepares a single position for create or update.
	 *
	 * @param \WP_REST_Request $request
	 * @return Position|\WP_Error
	 */
	protected function prepare_item_for_database( $request ) {
		$id   = $request['id'] ?? 0;
		$name = $request['name'];

		$existing = $this->existing_position( $name, $id );

		if ( $existing ) {
			return new \WP_Error( 'position_exists', sprintf( 'Position %s already exists.', $name ), [ 'status' => 400 ] );
		}

		if ( ! $id ) {
			$position = new Position();
		} else {
			/**
			 * @var Position
			 */
			$position = $this->entity_service->em->find( Position::class, $id );
		}

		$position->set_name( $name );

		if ( isset( $request['description'] ) ) {
			$position->set_description( sanitize_textarea_field( wp_unslash( $request['description'] ) ) );
		}

		return $this->filter( 'rest:position:pre-insert', $position, $request );
	}

	/**
	 * Check existing position.
	 *
	 * @param string $name
	 * @param integer $id
	 * @return boolean
	 */
	protected function existing_position( $name, $id = 0 ) {
		$criteria = [ 'name' => $name ];
		if ( $id ) {
			$criteria['id'] = [ '<>' => $id ];
		}
		$query_builder = $this->entity_service->em->getRepository( Position::class )->createQueryBuilder( 'p' );
		$query_builder->where( 'p.name = :name' )
					->setParameter( 'name', $name );
		if ( $id ) {
			$query_builder->andWhere( 'p.id <> :id' )
						->setParameter( 'id', $id );
		}
		$query_builder->setMaxResults( 1 );
		$entity = $query_builder->getQuery()->getOneOrNullResult();
		return (bool) $entity;
	}
}
