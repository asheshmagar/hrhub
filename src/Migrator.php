<?php
/**
 * Migrator class.
 */
namespace HRHub;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\Migrations\Version\Direction;
use HRHub\Traits\Hook;

/**
 * Migrator class.
 */
class Migrator {

	use Hook;

	/**
	 * Dependency factory.
	 *
	 * @var \Doctrine\Migrations\DependencyFactory
	 */
	private $dependency_factory;

	/**
	 * Configuration.
	 *
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * Constructor.
	 *
	 * @param Connection $conn
	 */
	public function __construct( private Connection $conn ) {
		$this->setup_configuration();
		$this->setup_dependency_factory();
	}

	/**
	 * Setup configuration.
	 *
	 * @return void
	 */
	private function setup_configuration() {
		global $wpdb;
		$configuration      = new Configuration( $this->conn::get_connection() );
		$migration_versions = glob( HRHUB_PLUGIN_DIR . 'src/Migration/*.php' );

		foreach ( $migration_versions as $migration_version ) {
			$configuration->addMigrationClass( 'HRHub\Migration\\' . basename( $migration_version, '.php' ) );
		}

		$configuration->setAllOrNothing( true );
		$configuration->setCheckDatabasePlatform( true );

		$storage_config = new TableMetadataStorageConfiguration();
		$storage_config->setTableName( "{$wpdb->prefix}hrhub_migration_versions" );

		$configuration->setMetadataStorageConfiguration( $storage_config );
		$this->configuration = $configuration;
	}

	/**
	 * Set dependency factory.
	 *
	 * @return void
	 */
	private function setup_dependency_factory() {
		$this->dependency_factory = DependencyFactory::fromConnection(
			new ExistingConfiguration( $this->configuration ),
			new ExistingConnection( $this->conn::get_connection() )
		);
	}

	/**
	 * Migrate.
	 *
	 * @return void
	 */
	public function migrate() {
		$migration_versions = $this->get_migration_versions();
		$this->dependency_factory->getMetadataStorage()->ensureInitialized();

		foreach ( $migration_versions as $migration_version ) {
			$this->run_migration( $migration_version );
		}

		$this->action( 'hrhub:migrations:complete', $migration_versions );
	}

	/**
	 * Get migration versions.
	 *
	 * @return array
	 */
	protected function get_migration_versions() {
		$executed_versions  = array_map(
			function ( $i ) {
				return (string) $i->getVersion();
			},
			$this->dependency_factory->getMetadataStorage()->getExecutedMigrations()->getItems()
		);
		$migration_versions = array_filter(
			$this->configuration->getMigrationClasses(),
			function ( $migration_version ) use ( $executed_versions ) {
				return ! in_array( $migration_version, $executed_versions, true );
			}
		);

		return $this->filter( 'hrhub:migration:versions', $migration_versions );
	}

	/**
	 * Run migration.
	 *
	 * @param string $migration_version
	 * @param boolean $rollback
	 * @return void
	 */
	protected function run_migration( $migration_version, $rollback = false ) {
		$this->action( 'hrhub:migration:start', $migration_version, $rollback );
		try {
			$migration = $this->dependency_factory->getMigrationPlanCalculator()->getPlanForVersions(
				[ $migration_version ],
				$rollback ? Direction::DOWN : Direction::UP
			);
			$this->dependency_factory->getMigrator()->migrate(
				$migration,
				( new MigratorConfiguration() )->setAllOrNothing( false )
			);
			$this->action( 'hrhub:migration:complete', $migration_version, $rollback );
		} catch ( \Exception $e ) {
			error_log( 'There were problems during db-migration.' . "\n" . $e->getMessage() . "\n\n" ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
}
