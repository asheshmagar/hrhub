<?php
/**
 * AbstractEntitiesController.
 */

namespace HRHub\Controller\V1;

use HRHub\Service\AbstractService;
use HRHub\Traits\Hook;
use JMS\Serializer\Serializer;

/**
 * Abstract entities controller.
 */
abstract class AbstractEntitiesController extends \WP_REST_Controller {

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
	protected $rest_base = '';

	/**
	 * Constructor.
	 *
	 * @var string
	 */
	protected $entity = '';

	/**
	 * Constructor.
	 *
	 * @param AbstractService $entity_service
	 * @param Serializer $serializer
	 */
	public function __construct(
		protected AbstractService $entity_service,
		protected Serializer $serializer
	) {
		if ( empty( $this->entity ) ) {
			_doing_it_wrong( __CLASS__, 'Property entity cannot be empty', '0.1.0' );
		}
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
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

	/**
	 * Prepare links form entity.
	 *
	 * @param object $entity
	 * @return array
	 */
	protected function prepare_links( $entity ) {
		$links = [
			'self'       => [
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $entity->get_id() ) ),
			],
			'collection' => [
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			],
		];

		return $links;
	}

	/**
	 * Update item.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		$id = $request['id'];

		$entity = $this->entity_service->em->find( $this->entity, $id );
		if ( ! $entity ) {
			return new \WP_Error( 'not_found', 'Entity not found.', [ 'status' => 404 ] );
		}
		$entity = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $entity ) ) {
			return $entity;
		}
		$entity   = $this->entity_service->create_or_update( $entity );
		$response = $this->prepare_item_for_response( $entity, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Get a single entity item.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		$id         = $request['id'];
		$entity_obj = $this->entity_service->em->find( $this->entity, $id );

		if ( ! $entity_obj ) {
			return new \WP_Error( 'not_found', 'Entity not found.', [ 'status' => 404 ] );
		}

		$entity   = $this->prepare_item_for_response( $entity_obj, $request );
		$response = rest_ensure_response( $entity );

		return $response;
	}

	/**
	 * Delete a single entity item.
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

		$response = $this->prepare_item_for_response( $entity, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 204 );
		return $response;
	}

	/**
	 * Get entity items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function get_items( $request ) {
		$query_args = $request->get_query_params();
		$results    = $this->entity_service->list( $query_args );

		$entities = [];

		foreach ( $results['entities'] as $entity ) {
			$data       = $this->prepare_item_for_response( $entity, $request );
			$entities[] = $this->prepare_response_for_collection( $data );
		}

		$page      = $request['page'] ?? 1;
		$max_pages = $results['pages'];
		$total     = $results['total'];
		$response  = rest_ensure_response( $entities );

		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}


	/**
	 * Checks if a given request has access to get items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		$entity_name = $this->get_entity_name();
		if ( ! current_user_can( 'read_' . $entity_name ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to view the resource.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		return true;
	}

	/**
	 * Checks if a given request has access to create items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		$entity_name = $this->get_entity_name();
		if ( ! current_user_can( 'create_' . $entity_name ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to create this resource.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		return true;
	}

	/**
	 * Checks if a given request has access to delete a specific item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$entity_name = $this->get_entity_name();
		if ( ! current_user_can( 'delete_' . $entity_name ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to delete this resource.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		return true;
	}

	/**
	 * Checks if a given request has access to get specific item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$entity_name = $this->get_entity_name();
		if ( ! current_user_can( 'read_' . $entity_name ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to view this resource.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		return true;
	}

	/**
	 * Checks if a given request has access to update a specific item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$entity_name = $this->get_entity_name();
		if ( ! current_user_can( 'edit_' . $entity_name ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to update this resource.' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		return true;
	}

	/**
	 * Get entity name.
	 *
	 * @return string
	 */
	protected function get_entity_name() {
		return str_replace( 'hrhub\entity\\', '', strtolower( $this->entity ) );
	}

	/**
	 * Create item.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_item( $request ) {
		$entity = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $entity ) ) {
			return $entity;
		}
		$entity = $this->entity_service->create_or_update( $entity );
		if ( is_wp_error( $entity ) ) {
			return $entity;
		}
		$response = $this->prepare_item_for_response( $entity, $request );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $entity->get_id() ) ) );
		return $response;
	}
}
