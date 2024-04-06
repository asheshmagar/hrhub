<?php

namespace HRHub\Controller\V1;

use HRHub\Entity\Department;
use HRHub\Repository\DepartmentRepository;
use HRHub\Service\DepartmentService;
use JMS\Serializer\Serializer;

class DepartmentsController extends \WP_REST_Controller {

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
	 * Constructor.
	 *
	 * @param DepartmentService $entity_service
	 */
	public function __construct(
		protected DepartmentService $entity_service,
		protected Serializer $serializer
	) {
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
						'description' => __( 'Unique identifier for the department.' ),
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

		$item = $this->entity_service->em->find( Department::class, $id );
		if ( ! $item ) {
			return new \WP_Error( 'not_found', 'Department not found.', [ 'status' => 404 ] );
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
		$item = $this->entity_service->em->find( Department::class, $id );

		if ( ! $item ) {
			return new \WP_Error( 'not_found', 'Department not found.', [ 'status' => 404 ] );
		}
		return rest_ensure_response( $this->serializer->toArray( $item ) );
	}
}
