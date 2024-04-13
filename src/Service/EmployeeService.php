<?php

namespace HRHub\Service;

use Doctrine\ORM\QueryBuilder;
use HRHub\Entity\Employee;

class EmployeeService extends AbstractService {

	protected $entity = Employee::class;

	/**
	 * Get object.
	 *
	 * @return Employee
	 */
	protected function get_entity_object() {
		return new Employee();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
							->select( 'e', 'd', 'p', 'l', 'r', 'a' )
							->from( $this->entity, 'e' )
							->leftJoin( 'e.department', 'd' )
							->leftJoin( 'e.position', 'p' )
							->leftJoin( 'e.leaves', 'l' )
							->leftJoin( 'e.reviews', 'r' )
							->leftJoin( 'e.attendances', 'a' );
		return $query_builder;
	}
}
