<?php

namespace HRHub\Service;

use HRHub\Entity\Review;
use HRHub\Repository\ReviewRepository;

class ReviewService extends AbstractService {

	/**
	 * Get object.
	 *
	 * @return Review
	 */
	protected function get_entity_object() {
		return new Review();
	}

	public function list( ?array $args = [] ): array {
		return [];
	}

	public function get_entity_class_name(): string {
		return Review::class;
	}
}
