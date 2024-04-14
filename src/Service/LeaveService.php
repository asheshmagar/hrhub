<?php

namespace HRHub\Service;

use Doctrine\ORM\QueryBuilder;
use HRHub\Entity\Leave;
use HRHub\Repository\LeaveRepository;

class LeaveService extends AbstractService {

	protected $entity = Leave::class;

	/**
	 * Get object.
	 *
	 * @return Leave
	 */
	protected function get_entity_object() {
		return new Leave();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
							->select( 'da' )
							->from( $this->entity, 'da' );
		return $query_builder;
	}
}
