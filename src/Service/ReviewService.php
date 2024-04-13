<?php

namespace HRHub\Service;

use Doctrine\ORM\QueryBuilder;
use HRHub\Entity\Review;
use HRHub\Repository\ReviewRepository;

class ReviewService extends AbstractService {

	protected $entity = Review::class;

	/**
	 * Get object.
	 *
	 * @return Review
	 */
	protected function get_entity_object() {
		return new Review();
	}

	/**
	 * Create query builder.
	 *
	 * @return QueryBuilder
	 */
	protected function create_query_builder(): QueryBuilder {
		$query_builder = $this->em->createQueryBuilder()
						->select( 'r' )
						->from( $this->entity, 'r' );
		return $query_builder;
	}
}
