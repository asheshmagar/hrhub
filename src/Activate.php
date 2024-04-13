<?php
/**
 * Activate class.
 */
namespace HRHub;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use HRHub\Traits\Hook;
use WP_Roles;

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
	}

	/**
	 * On activate.
	 */
	public function on_activate() {
		$this->create_roles();
		$this->create_schema();
		! get_option( '_hrhub_activation_timestamp' ) && update_option( '_hrhub_activation_timestamp', time() );
		$this->action( 'activate' );
	}

	/**
	 * Create schema.
	 *
	 * @return void
	 */
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
		$schema_tool->updateSchema( $classes, true );
	}

	/**
	 * Create roles.
	 *
	 * @return void
	 */
	protected function create_roles() {
		global $wp_roles;

		if ( ! class_exists( WP_Roles::class ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		add_role(
			'hrhub_employee',
			'HRHub Employee',
			array(
				'read' => true,
			)
		);

		add_role(
			'hrhub_manager',
			'HRHub manager',
			[
				'read' => true,
			]
		);

		$capabilities = $this->get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'hrhub_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Get core capabilities.
	 *
	 * @return array
	 */
	public function get_core_capabilities() {
		$capabilities = [];

		$capabilities['core'] = array(
			'manage_hrhub',
			'view_hrhub_reports',
		);

		$cap_types = [ 'employee', 'department', 'position', 'leave', 'review' ];

		foreach ( $cap_types as $cap_type ) {
			$capabilities[ $cap_type ] = [
				"create_{$cap_type}",
				"edit_{$cap_type}",
				"read_{$cap_type}",
				"delete_{$cap_type}",
				"edit_{$cap_type}s",
				"edit_others_{$cap_type}s",
				"delete_{$cap_type}s",
				"delete_others_{$cap_type}s",
			];
		}
		return $capabilities;
	}
}
