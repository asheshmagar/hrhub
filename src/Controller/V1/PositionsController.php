<?php

namespace HRHub\Controller\V1;

use HRHub\Entity\Position;
use HRHub\Service\PositionService;
use JMS\Serializer\Serializer;

class PositionsController extends \WP_REST_Controller {

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
	 * Constructor.
	 *
	 * @param PositionService $entity_service
	 */
	public function __construct( protected PositionService $entity_service, protected Serializer $serializer ) {
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
			'/' . $this->rest_base . '/(?P<id>\d+)',
			[
				'args' => [
					'id' => [
						'description' => __( 'Unique identifier for the position.' ),
						'type'        => 'integer',
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
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

	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function create_item_permissions_check( $request ) {
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
	 * Create item.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function create_item( $request ) {
		$data = $request->get_params();
		$name = $data['name'];

		if ( $this->entity_service->em->getRepository( Position::class )->findBy(
			[
				'name' => $name,
			]
		) ) {
			return new \WP_Error( 'position_exists', 'Position already exists.', [ 'status' => 400 ] );
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
	 * Delete item.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function delete_item( $request ) {
		$id     = $request['id'];
		$entity = $this->entity_service->delete( $id );

		if ( is_wp_error( $entity ) ) {
			return $entity;
		}

		return rest_ensure_response( $entity );
	}

	/**
	 * Update item.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$id = $request['id'];

		$item = $this->entity_service->em->find( Position::class, $id );
		if ( ! $item ) {
			return new \WP_Error( 'not_found', 'Position not found.', [ 'status' => 404 ] );
		}
		$data = $request->get_params();
		unset( $data['id'] );
		$item = $this->entity_service->update( $item, $data );
		return rest_ensure_response( $this->serializer->toArray( $item ) );
	}

	/**
	 * Get items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		$id   = $request['id'];
		$item = $this->entity_service->em->find( Position::class, $id );

		if ( ! $item ) {
			return new \WP_Error( 'not_found', 'Position not found.', [ 'status' => 404 ] );
		}
		return rest_ensure_response( $this->serializer->toArray( $item ) );
	}
}
