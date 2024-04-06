<?php
/**
 * Script file.
 */

namespace HRHub\AssetManager;

use HRHub\AssetManager\Abstracts\Asset;
use HRHub\AssetManager\Enums\Location;

class Script extends Asset {
	/**
	 * Asset type.
	 *
	 * @var string
	 */
	protected $type = 'script';

	/**
	 * Script data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'in_footer' => false,
	);

	/**
	 * Constructor.
	 *
	 * @param string $handle        Name of the script.
	 * @param string|callable $src  Full URL of the script or path of the script relative to the WP root directory or a callable function which will return src.
	 * @param array $dependencies   An array of registered script handles this script depends on.
	 * @param string $version       String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version.
	 * @param boolean $in_footer    Whether to enqueue the script before </body> instead of in the <head>.
	 * @param string $location      Where to load the asset backend/frontend/both. Default both.
	 * @param callable $callback   Callback to determine whether to enqueue the asset or not.
	 * @param bool $register_only Whether to register only asset.
	 */
	public function __construct( $handle, $src = '', $dependencies = array(), $version = '', $in_footer = false, $location = Location::ALL, $callback = null, $register = false ) {
		$this->extra_data = array(
			'in_footer' => $in_footer,
		);

		parent::__construct( $handle, $src, $dependencies, $version, $location, $callback, $register );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return whether to enqueue the script before </body> instead of in the <head>
	 *
	 * @return string
	 */
	public function get_in_footer() {
		return $this->__get( 'in_footer' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set in footer  of the script.
	 *
	 * @param string $in_footer  Name of the script.
	 *
	 * @return \HRHub\AssetManager\Script
	 */
	public function set_in_footer( $in_footer ) {
		$this->__set( 'in_footer', $in_footer );
	}
}
