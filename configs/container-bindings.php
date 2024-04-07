<?php

use HRHub\App;
use HRHub\Admin;
use HRHub\Config;
use HRHub\RESTApi;
use HRHub\Activate;
use HRHub\Migrator;
use HRHub\Connection;
use HRHub\ScriptStyle;
use HRHub\Entity\Leave;
use Doctrine\ORM\Events;
use HRHub\Entity\Review;
use Doctrine\ORM\ORMSetup;
use HRHub\Entity\Document;
use HRHub\Entity\Employee;
use HRHub\Entity\Position;
use HRHub\Entity\Attachment;
use HRHub\Entity\Attendance;
use HRHub\Entity\Department;
use JMS\Serializer\Serializer;
use Doctrine\ORM\EntityManager;
use HRHub\Service\LeaveService;
use HRHub\Service\ReviewService;
use Doctrine\Common\EventManager;
use HRHub\Service\DocumentService;
use HRHub\Service\EmployeeService;
use HRHub\Service\PositionService;
use HRHub\AssetManager\AssetManager;
use HRHub\Service\AttendanceService;
use HRHub\Service\DepartmentService;
use JMS\Serializer\SerializerBuilder;
use HRHub\Controller\V1\LeavesController;
use HRHub\Controller\V1\ReviewsController;
use HRHub\Subscriber\TablePrefixSubscriber;
use HRHub\Controller\V1\EmployeesController;
use HRHub\Controller\V1\PositionsController;
use HRHub\Controller\V1\AttendancesController;
use HRHub\Controller\V1\DepartmentsController;

use function DI\get;
use function DI\create;

return [
	Config::class                => create( Config::class ),
	Serializer::class            => function () {
		static $serializer;
		if ( ! $serializer ) {
			$serializer = SerializerBuilder::create()->build();
		}
		return $serializer;
	},
	Connection::class            => function ( Config $config ) {
		return new Connection( $config );
	},
	EntityManager::class         => function ( Connection $conn ) {
		$is_dev = defined( 'HRHUB_IS_DEVELOPMENT' ) && HRHUB_IS_DEVELOPMENT;

		$evm = new EventManager();
		$evm->addEventListener( Events::loadClassMetadata, new TablePrefixSubscriber() );

		$em_config = ORMSetup::createAttributeMetadataConfiguration(
			[
				realpath( HRHUB_PLUGIN_DIR . 'src/Entity' ),
			]
		);

		$em_config->setProxyDir( realpath( HRHUB_PLUGIN_DIR . 'src/Proxy' ) );
		$em_config->setProxyNamespace( 'HRHub\Proxy' );

		if ( $is_dev ) {
			$em_config->setAutoGenerateProxyClasses( true );
		} else {
			$em_config->setAutoGenerateProxyClasses( false );
		}

		return new EntityManager( $conn::get_connection(), $em_config, $evm );
	},
	Migrator::class              => function ( Connection $conn ) {
		return new Migrator( $conn );
	},
	AssetManager::class          => create( AssetManager::class ),
	Activate::class              => create( Activate::class ),
	App::class                   => create( App::class ),
	Admin::class                 => create( Admin::class ),
	ScriptStyle::class           => create( ScriptStyle::class )->constructor( get( AssetManager::class ) ),
	Employee::class              => create( Employee::class ),
	Leave::class                 => create( Leave::class ),
	Department::class            => create( Department::class ),
	Position::class              => create( Position::class ),
	Review::class                => create( Review::class ),
	Attendance::class            => create( Attendance::class ),
	EmployeeService::class       => create( EmployeeService::class )->constructor( get( EntityManager::class ) ),
	LeaveService::class          => create( LeaveService::class )->constructor( get( EntityManager::class ) ),
	PositionService::class       => create( PositionService::class )->constructor( get( EntityManager::class ) ),
	ReviewService::class         => create( ReviewService::class )->constructor( get( EntityManager::class ) ),
	AttendanceService::class     => create( AttendanceService::class )->constructor( get( EntityManager::class ) ),
	DepartmentService::class     => create( DepartmentService::class )->constructor( get( EntityManager::class ) ),
	EmployeesController::class   => create( EmployeesController::class )->constructor( get( EmployeeService::class ), get( Serializer::class ) ),
	AttendancesController::class => create( AttendancesController::class )->constructor( get( AttendanceService::class ) ),
	DepartmentsController::class => create( DepartmentsController::class )->constructor( get( DepartmentService::class ), get( Serializer::class ) ),
	LeavesController::class      => create( LeavesController::class )->constructor( get( LeaveService::class ) ),
	PositionsController::class   => create( PositionsController::class )->constructor( get( PositionService::class ), get( Serializer::class ) ),
	ReviewsController::class     => create( ReviewsController::class )->constructor( get( ReviewService::class ) ),
	RESTApi::class               => create( RESTApi::class )->constructor(
		get( EmployeesController::class ),
		get( AttendancesController::class ),
		get( DepartmentsController::class ),
		get( LeavesController::class ),
		get( PositionsController::class ),
		get( ReviewsController::class )
	),
];
