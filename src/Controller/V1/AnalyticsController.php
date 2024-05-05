<?php

namespace HRHub\Controller\V1;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use HRHub\Entity\Department;
use HRHub\Entity\Employee;
use HRHub\Entity\Position;
use HRHub\Service\DepartmentService;
use HRHub\Service\PositionService;
use JMS\Serializer\Serializer;

class AnalyticsController extends \WP_REST_Controller {

	protected $namespace = 'hrhub/v1';

	protected $rest_base = 'analytics';

	public function __construct(
		private EntityManagerInterface $em,
		private Serializer $serializer
	) {}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_analytics' ],
					'permission_callback' => '__return_true',
				],
			]
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/employees/(?P<type>(salary|department|position))',
			[
				'args' => [
					'type' => [
						'description' => __( 'Type of analytics', 'hrhub' ),
						'type'        => 'string',
						'required'    => true,
						'enum'        => [ 'salary', 'department', 'position' ],
					],
				],
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_employees' ],
					'permission_callback' => '__return_true',
				],
			]
		);
	}

	public function get_employees( $request ) {
		$type     = $request['type'];
		$response = [];

		$response = match ( $type ) {
			'salary' => $this->get_employees_by_salary(),
			'department' => $this->get_employees_by_department(),
			'position' => $this->get_employees_by_position(),
			default => [],
		};

		return rest_ensure_response( $response, 200 );
	}

	public function get_analytics() {
		$employees_by_salary = $this->get_employees_by_salary();
		return rest_ensure_response( $employees_by_salary, 200 );
	}

	protected function get_employees_by_salary() {
		$salary_ranges = require __DIR__ . '/salary-range.php';
		$qb            = $this->em->createQueryBuilder();
		$qb->select( 'e' )
			->from( Employee::class, 'e' );

		$employees_by_range = [];
		foreach ( $salary_ranges as $range ) {
			$range_string = $range['min'] . '-' . $range['max'];
			$qb->resetDQLPart( 'where' );
			$qb->andWhere( $qb->expr()->between( 'e.salary', $range['min'], $range['max'] ) );
			$employees                           = $qb->getQuery()->getResult( AbstractQuery::HYDRATE_ARRAY );
			$employees_by_range[ $range_string ] = $employees;
		}
		return $employees_by_range;
	}

	protected function get_employees_by_position() {
		$position_service = hrhub( PositionService::class );
		$positions        = $position_service->list(
			[
				'per_page' => 99,
			],
			AbstractQuery::HYDRATE_ARRAY
		);
		return $positions['entities'] ?? [];
	}

	protected function get_employees_by_department() {
		$department_service = hrhub( DepartmentService::class );
		$department         = $department_service->list(
			[
				'per_page' => 99,
			],
			AbstractQuery::HYDRATE_ARRAY
		);
		return $department['entities'] ?? [];
	}
}
