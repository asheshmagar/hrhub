<?php

namespace HRHub\Controller\V1;

use HRHub\Repository\PositionRepository;
use HRHub\Service\PositionService;

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
	public function __construct( protected PositionService $entity_service ) {
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
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_item' ],
					'permission_callback' => [ $this, 'update_item_permissions_check' ],
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
				],
				[
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => [ $this, 'delete_item' ],
					'permission_callback' => [ $this, 'delete_item_permissions_check' ],
					'args'                => [
						'id' => [
							'description'       => __( 'Unique identifier for the object.' ),
							'type'              => 'integer',
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_numeric( $param );
							},
						],
					],
				],
			]
		);
	}

	public function get_items_permissions_check( $request ) {
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
}
