<?php

namespace HRHub\Service;

use Doctrine\ORM\QueryBuilder;
use HRHub\Entity\Attendance;

class AttendanceService extends AbstractService {

	protected $entity = Attendance::class;

	/**
	 * Get object.
	 *
	 * @return Attendance
	 */
	protected function get_entity_object() {
		return new Attendance();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
						->select( 'a', )
						->from( $this->entity, 'a' );
		return $query_builder;
	}
}
