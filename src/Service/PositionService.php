<?php

namespace HRHub\Service;

use Doctrine\ORM\QueryBuilder;
use HRHub\Entity\Position;

class PositionService extends AbstractService {

	protected $entity = Position::class;

	/**
	 * Get object.
	 *
	 * @return Position
	 */
	protected function get_entity_object() {
		return new Position();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
						->select( 'p', 'e' )
						->from( $this->entity, 'p' )
						->leftJoin( 'p.employees', 'e' );
		return $query_builder;
	}
}
