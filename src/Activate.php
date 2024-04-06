<?php
/**
 * Activate class.
 */
namespace HRHub;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use HRHub\Traits\Hook;

/**
 * Activate class.
 */
class Activate {

	use Hook;

	/**
	 * Init.
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Init activate.
	 *
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( HRHUB_PLUGIN_FILE, array( $this, 'on_activate' ) );
		$this->add_action( 'init', [ $this, 'version_check' ], 0 );
	}

	/**
	 * On activate.
	 */
	public function on_activate() {
		$this->activate();
	}

	/**
	 * Version check.
	 *
	 * @return void
	 */
	public function version_check() {
		$current_version = get_option( '_hrhub_version' );
		if ( empty( $current_version ) ) {
			return add_option( '_hrhub_version', HRHUB_VERSION );

		}
		if ( version_compare( $current_version, HRHUB_VERSION, '<' ) ) {
			do_action( 'hrhub_update_version', HRHUB_VERSION, $current_version );
			return update_option( '_hrhub_version', HRHUB_VERSION );
		}
		return true;
	}

	/**
	 * Activate.
	 *
	 * @return void
	 */
	private function activate() {
		if ( get_option( '_hrhub_activation_timestamp' ) ) {
			return;
		}
		update_option( '_hrhub_activation_timestamp', time() );
		update_option( '_hrhub_version', HRHUB_VERSION );
		flush_rewrite_rules();
		$this->create_schema();
		flush_rewrite_rules();
	}

	private function create_schema() {
		$em               = hrhub( EntityManager::class );
		$schema_tool      = new SchemaTool( $em );
		$metadata_factory = $em->getMetadataFactory();
		$classes          = $metadata_factory->getAllMetadata();
		$classes          = array_filter(
			$classes,
			function ( $meta ) {
				return $meta->getName() !== 'HRHub\Entity\WPUser';
			}
		);
		$schema_tool->createSchema( $classes );
		flush_rewrite_rules();
	}

	protected function create_roles() {
		add_role(
			'hrhub_employee',
			'HRHub Employee',
			array(
				'read' => true,
			)
		);
	}
}
