<?php

namespace HRHub\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

abstract class AbstractService {

	/**
	 * Constructor.
	 *
	 * @param EntityManagerInterface $em
	 */
	public function __construct( public EntityManagerInterface $em ) {
		$this->em = $em;
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
		return $entity;
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
	public function update( $entity, array $data ) {
		try {
			$entity = $this->hydrate_entity( $entity, $data );
			$this->em->persist( $entity );
			$this->em->flush();
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
			$entity = is_object( $entity ) ? $entity : $this->em->find( $this->get_entity_class_name(), $entity );
			if ( ! $entity ) {
				throw new ORMException( sprintf( 'Entity %s not found', $this->get_entity_class_name() ) );
			}
			$this->em->remove( $entity );
			$this->em->flush();
		} catch ( ORMException $e ) {
			return new \WP_Error( 'entity_delete_error', $e->getMessage() );
		}
		return $entity;
	}

	/**
	 * List entity.
	 *
	 * @return array
	 */
	abstract public function list( ?array $args = [] ): array;

	/**
	 * Get entity class name.
	 *
	 * @return string
	 */
	abstract public function get_entity_class_name(): string;
}
