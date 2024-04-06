<?php

namespace HRHub\Service;

use HRHub\Entity\Attendance;

class AttendanceService extends AbstractService {

	/**
	 * Get object.
	 *
	 * @return Attendance
	 */
	protected function get_entity_object() {
		return new Attendance();
	}

	public function list( ?array $args = [] ): array {
		return [];
	}

	public function get_entity_class_name(): string {
		return Attendance::class;
	}
}
