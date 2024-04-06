<?php
/**
 * App.
 */

namespace HRHub;

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
		$this->add_action( 'init', [ $this, 'after_wp_init' ], 0 );
	}

	/**
	 * After wp init.
	 *
	 * @return void
	 */
	public function after_wp_init() {
		$this->action( 'init' );
	}
}
