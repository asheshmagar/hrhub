<?php

namespace HRHub\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use HRHub\Entity\Employee;
use HRHub\Traits\Hook;

abstract class AbstractService {

	use Hook;

	protected $entity = '';

	/**
	 * Constructor.
	 *
	 * @param EntityManagerInterface $em
	 */
	public function __construct( public EntityManagerInterface $em ) {
		$this->em = $em;
		if ( empty( $this->entity ) ) {
			_doing_it_wrong( __CLASS__, 'Entity prop cannot be empty', '0.1.0' );
		}
	}

	/**
	 * Hydrate entity.
	 *
	 * @param object $entity
	 * @param array $data
	 * @return object
	 */
	final public function hydrate_entity( $entity, array $data ) {
		foreach ( $data as $prop => $value ) {
			$method = "set_{$prop}";
			if ( method_exists( $entity, $method ) ) {
				$entity->$method( $value );
			}
		}
		return $this->filter( 'hrhub:hydrated:entity', $entity, $data );
	}

	/**
	 * Get entity object.
	 *
	 * @return object
	 */
	abstract protected function get_entity_object();

	/**
	 * Create entity.
	 *
	 * @param array $data
	 * @return object|\WP_Error
	 */
	public function create( array $data ) {
		try {
			$entity = $this->get_entity_object();
			$entity = $this->hydrate_entity( $entity, $data );
			$this->em->persist( $entity );
			$this->em->flush();
			$this->action( 'hrhub:entity:created', $entity, $data );
		} catch ( ORMException $e ) {
			return new \WP_Error( 'entity_create_error', $e->getMessage() );
		}
		return $entity;
	}

	/**
	 * Update entity.
	 *
	 * @param object $entity
	 * @param array $data
	 * @return object|\WP_Error
	 */
	public function update( $entity, array $data = [] ) {
		try {
			$entity = $this->hydrate_entity( $entity, $data );
			$this->em->persist( $entity );
			$this->em->flush();
			$this->action( 'hrhub:entity:updated', $entity, $data );
		} catch ( ORMException $e ) {
			return new \WP_Error( 'entity_update_error', $e->getMessage() );
		}
		return $entity;
	}

	/**
	 * Create or update entity.
	 *
	 * @param object $entity
	 * @param array $data
	 * @return object|\WP_Error
	 */
	public function create_or_update( $entity, $data = [] ) {
		try {
			$hook   = ! $entity->get_id() ? 'hrhub:entity:created' : 'hrhub:entity:updated';
			$entity = $this->hydrate_entity( $entity, $data );
			$this->em->persist( $entity );
			$this->em->flush();
			$this->action( $hook, $entity, $data );
		} catch ( ORMException $e ) {
			return new \WP_Error( 'entity_update_error', $e->getMessage() );
		}
		return $entity;
	}

	/**
	 * Delete entity.
	 *
	 * @param object|int $entity
	 * @return object|\WP_Error
	 */
	public function delete( $entity ) {
		try {
			$entity = is_object( $entity ) ? $entity : $this->em->find( $this->entity, $entity );
			if ( ! $entity ) {
				throw new ORMException( sprintf( 'Entity %s not found', $this->entity ) );
			}
			$this->em->remove( $entity );
			$this->em->flush();
			$this->action( 'hrhub:entity:deleted', $entity );
		} catch ( ORMException $e ) {
			return new \WP_Error( 'entity_delete_error', $e->getMessage() );
		}
		return $entity;
	}

	/**
	 * List entities with pagination.
	 *
	 * @param null|array $query_args
	 * @return array
	 */
	public function list( ?array $query_args = [] ): array {
		$defaults      = [
			'search'   => null,
			'page'     => 1,
			'per_page' => 10,
		];
		$query_args    = wp_parse_args( $query_args, $defaults );
		$query_builder = $this->create_query_builder();

		if ( $query_args['search'] ) {
			$query_builder->where( 'da.name LIKE :search' )
				->setParameter( 'search', '%' . $query_args['search'] . '%' );
		}

		$limit = absint( $query_args['per_page'] ?? 10 );
		$page  = absint( $query_args['page'] ?? 1 );

		$paginator   = new Paginator( $query_builder );
		$total       = count( $paginator );
		$departments = $paginator->getQuery()
						->setFirstResult( $limit * ( $page - 1 ) )
						->setMaxResults( $limit )
						->getResult();

		return [
			'entities' => $departments,
			'total'    => $total,
			'current'  => $query_args['page'],
			'pages'    => ceil( $total / $limit ),
		];
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	abstract protected function create_query_builder(): QueryBuilder;
}
