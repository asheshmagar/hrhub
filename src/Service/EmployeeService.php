<?php

namespace HRHub\Service;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use HRHub\Entity\Employee;

class EmployeeService extends AbstractService {

	/**
	 * Get object.
	 *
	 * @return Employee
	 */
	protected function get_entity_object() {
		return new Employee();
	}

	/**
	 * {@inheritDoc}
	 */
	public function list( ?array $query_args = [] ): array {
		$defaults   = [
			'search'   => null,
			'page'     => 1,
			'per_page' => 10,
		];
		$query_args = wp_parse_args( $query_args, $defaults );
		$qb         = $this->em->createQueryBuilder()
						->select( 'e', 'd', 'p', 'l', 'r', 'a' )
						->from( Employee::class, 'e' )
						->leftJoin( 'e.department', 'd' )
						->leftJoin( 'e.position', 'p' )
						->leftJoin( 'e.leaves', 'l' )
						->leftJoin( 'e.reviews', 'r' )
						->leftJoin( 'e.attendances', 'a' );

		if ( $query_args['search'] ) {
			$qb->where( 'e.name LIKE :search' )
				->setParameter( 'search', '%' . $query_args['search'] . '%' );
		}

		$limit = absint( $query_args['per_page'] ?? 10 );
		$page  = absint( $query_args['page'] ?? 1 );

		$paginator = new Paginator( $qb );
		$total     = count( $paginator );
		$employees = $paginator->getQuery()
						->setFirstResult( $limit * ( $page - 1 ) )
						->setMaxResults( $limit )
						->getResult( AbstractQuery::HYDRATE_ARRAY );

		return [
			'employees' => $employees,
			'total'     => $total,
			'current'   => $query_args['page'],
			'pages'     => ceil( $total / $limit ),
		];
	}

	/**
	 * Get entity class name.
	 *
	 * @return string
	 */
	public function get_entity_class_name(): string {
		return Employee::class;
	}
}
