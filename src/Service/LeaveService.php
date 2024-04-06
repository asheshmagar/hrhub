<?php

namespace HRHub\Service;

use HRHub\Entity\Leave;
use HRHub\Repository\LeaveRepository;

class LeaveService extends AbstractService {

	/**
	 * Get object.
	 *
	 * @return Leave
	 */
	protected function get_entity_object() {
		return new Leave();
	}

	public function list( ?array $args = [] ): array {
		return [];
	}

	public function get_entity_class_name(): string {
		return Leave::class;
	}
}
