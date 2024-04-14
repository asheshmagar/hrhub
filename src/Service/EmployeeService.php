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
							->select( 'da', 'd', 'p', 'l', 'r', 'a' )
							->from( $this->entity, 'da' )
							->leftJoin( 'da.department', 'd' )
							->leftJoin( 'da.position', 'p' )
							->leftJoin( 'da.leaves', 'l' )
							->leftJoin( 'da.reviews', 'r' )
							->leftJoin( 'da.attendances', 'a' );
		return $query_builder;
	}
}
