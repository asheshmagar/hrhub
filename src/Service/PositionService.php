<?php

namespace HRHub\Service;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use HRHub\Entity\Position;
use HRHub\Repository\PositionRepository;

class PositionService extends AbstractService {

	/**
	 * Get object.
	 *
	 * @return Position
	 */
	protected function get_entity_object() {
		return new Position();
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
						->select( 'p', 'e' )
						->from( Position::class, 'p' )
						->leftJoin( 'p.employees', 'e' );

		if ( $query_args['search'] ) {
			$qb->where( 'p.name LIKE :search' )
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
			'positions' => $employees,
			'total'     => $total,
			'current'   => $query_args['page'],
			'pages'     => ceil( $total / $limit ),
		];
	}

	public function get_entity_class_name(): string {
		return Position::class;
	}
}
