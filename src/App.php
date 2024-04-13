<?php
/**
 * App.
 */

namespace HRHub;

use HRHub\Email\EmailHooks;
use HRHub\Traits\Hook;

/**
 * App class.
 */
class App {

	use Hook;

	/**
	 * Constructor.
	 */
	public function __construct() {
		hrhub( Migrator::class )->migrate();
	}

	/**
	 * Constructor.
	 */
	public function init() {
		hrhub( Activate::class )->init();
		hrhub( ScriptStyle::class )->init();
		hrhub( Admin::class )->init();
		hrhub( RESTApi::class )->init();
		hrhub( EmailHooks::class )->init();
		$this->add_action( 'init', [ $this, 'after_wp_init' ], 0 );
	}

	/**
	 * After wp init.
	 *
	 * @return void
	 */
	public function after_wp_init() {
		$this->version_check();
		$this->load_textdomain();
		$this->action( 'init' );
	}

	/**
	 * Version check.
	 *
	 * @return void
	 */
	private function version_check() {
		$current_version = get_option( '_hrhub_version' );
		if ( empty( $current_version ) ) {
			update_option( '_hrhub_version', HRHUB_VERSION );
			return;
		}
		if ( version_compare( $current_version, HRHUB_VERSION, '<' ) ) {
			$this->action( 'version:update', HRHUB_VERSION, $current_version );
			update_option( '_hrhub_version', HRHUB_VERSION );
		}
	}

	/**
	 * Load text domain.
	 *
	 * @return void
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'hrhub', false, HRHUB_PLUGIN_DIR . '/translations' );
	}
}
