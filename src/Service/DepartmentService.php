<?php

namespace HRHub\Service;

use HRHub\Entity\Department;
use Doctrine\ORM\QueryBuilder;
use HRHub\Service\AbstractService;

class DepartmentService extends AbstractService {

	protected $entity = Department::class;

	/**
	 * Get object.
	 *
	 * @return Department
	 */
	protected function get_entity_object() {
		return new Department();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
						->select( 'd', 'e' )
						->from( $this->entity, 'd' )
						->leftJoin( 'd.employees', 'e' );
		return $query_builder;
	}
}
