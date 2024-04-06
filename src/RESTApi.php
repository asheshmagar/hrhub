<?php
/**
 * RESTApi class.
 */
namespace HRHub;

use HRHub\Controller\V1\LeavesController;
use HRHub\Controller\V1\ReviewsController;
use HRHub\Controller\V1\EmployeesController;
use HRHub\Controller\V1\PositionsController;
use HRHub\Controller\V1\AttendancesController;
use HRHub\Controller\V1\DepartmentsController;

/**
 * RESTApi class.
 */
class RESTApi {

	/**
	 * Constructor.
	 *
	 * @param EmployeesController $employees_controller
	 * @param AttendancesController $attendances_controller
	 * @param DepartmentsController $departments_controller
	 * @param LeavesController $leaves_controller
	 * @param PositionsController $positions_controller
	 * @param ReviewsController $reviews_controller
	 */
	public function __construct(
		private EmployeesController $employees_controller,
		private AttendancesController $attendances_controller,
		private DepartmentsController $departments_controller,
		private LeavesController $leaves_controller,
		private PositionsController $positions_controller,
		private ReviewsController $reviews_controller,
	) {}

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Register rest routes.
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		$this->employees_controller->register_routes();
		$this->attendances_controller->register_routes();
		$this->departments_controller->register_routes();
		$this->leaves_controller->register_routes();
		$this->positions_controller->register_routes();
		$this->reviews_controller->register_routes();
	}
}
