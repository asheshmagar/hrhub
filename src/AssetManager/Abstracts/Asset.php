<?php
/**
 * Script file.
 */

namespace HRHub\AssetManager\Abstracts;

use HRHub\AssetManager\Enums\Location;

abstract class Asset {
	/**
	 * Asset type.
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Script data.
	 *
	 * @var array
	 */
	protected $data = array(
		'handle'       => '',
		'src'          => '',
		'dependencies' => array(),
		'version'      => '',
		'callback'     => null,
		'register'     => false,
	);

	/**
	 * Script extra data.
	 *
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Constructor.
	 *
	 * @param string $handle        Name of the asset.
	 * @param string|callable $src  Full URL of the script or path of the script relative to the WP root directory or a callable function which will return src.
	 * @param array $dependencies   An array of registered script handles this script depends on.
	 * @param string $version       String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version.
	 * @param string $location      Where to load the asset backend/frontend/both. Default both.
	 * @param callable $callback    Callback to determine whether to enqueue the asset or not.
	 * @param bool $register_only   Whether to register only asset.
	 */
	public function __construct( $handle, $src = '', $dependencies = array(), $version = '', $location = Location::ALL, $callback = null, $register = false ) {
		$this->data = array(
			'handle'       => $handle,
			'src'          => $src,
			'dependencies' => $dependencies,
			'version'      => $version,
			'location'     => $location,
			'callback'     => $callback,
			'register'     => $register,
		);

		$this->data = array_merge( $this->data, $this->extra_data );
	}

	/*
	|--------------------------------------------------------------------------
	| Magic methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Magic method set
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set( $name, $value ) {
		$this->data[ $name ] = $value;
	}

	/**
	 * Magic method get
	 *
	 * @param string $name
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->data[ $name ];
		}
	}

	/**
	 * Magic method isset
	 *
	 * @param string $name
	 */
	public function __isset( $name ) {
		return isset( $this->data[ $name ] );
	}

	/**
	 * Magic method unset
	 *
	 * @param string $name
	 */
	public function __unset( $name ) {
		if ( isset( $this->data[ $name ] ) ) {
			unset( $this->data[ $name ] );
		}
	}

	/**
	 * Magic method toString
	 *
	 * @return string
	 */
	public function __toString() {
		return array_reduce(
			array_keys( $this->data ),
			function ( $carry, $key ) {
				return $carry . ' ' . $key . '="' . htmlspecialchars( $this->data[ $key ] ) . '"';
			}
		);
	}

	/**
	 * Magic method debugInfo
	 *
	 * @return string
	 */
	public function __debugInfo() {
		return $this->data;
	}

	/*
	|--------------------------------------------------------------------------
	| Non-CRUD
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return asset type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return name/handle of the asset.
	 *
	 * @return string
	 */
	public function get_handle() {
		return $this->__get( 'handle' );
	}

	/**
	 * Return src of the asset.
	 *
	 * @return string|callable
	 */
	public function get_src() {
		return $this->__get( 'src' );
	}

	/**
	 * Return dependencies of the asset.
	 *
	 * @return string
	 */
	public function get_dependencies() {
		return $this->__get( 'dependencies' );
	}

	/**
	 * Return version of the asset.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->__get( 'version' );
	}

	/**
	 * Return location of the asset.
	 *
	 * @return string
	 */
	public function get_location() {
		return $this->__get( 'location' );
	}

	/**
	 * Return callback of the asset.
	 *
	 * @return callable|null
	 */
	public function get_callback() {
		return $this->__get( 'callback' );
	}

	/**
	 * Return register of the asset.
	 *
	 * @return boolean
	 */
	public function get_register() {
		return $this->__get( 'register' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set name/handle of the asset.
	 *
	 * @param string $handle Name of the asset.
	 */
	public function set_handle( $handle ) {
		$this->__set( 'handle', $handle );
	}

	/**
	 * Set src of the asset.
	 *
	 * @param string|callable $src SRC of the asset.
	 */
	public function set_src( $src ) {
		$this->__set( 'src', $src );
	}

	/**
	 * Set dependencies of the asset.
	 *
	 * @param string $dependencies Dependencies of the asset.
	 */
	public function set_dependencies( $dependencies ) {
		$this->__set( 'dependencies', $dependencies );
	}

	/**
	 * Set version of the asset.
	 *
	 * @param string $version Version of the asset.
	 */
	public function set_version( $version ) {
		$this->__set( 'version', $version );
	}

	/**
	 * Set location of the asset.
	 *
	 * @param string $location Location of the asset.
	 */
	public function set_location( $location ) {
		$this->__set( 'location', $location );
	}

	/**
	 * Set callback of the asset.
	 *
	 * @param string $callback Callback of the asset.
	 */
	public function set_callback( $callback ) {
		$this->__set( 'callback', $callback );
	}

	/**
	 * Set register of the asset.
	 *
	 * @param string $register Whether to register the asset
	 */
	public function set_register( $register ) {
		$this->__set( 'register', $register );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return true if the asset should be register.

	 * @return boolean
	 */
	public function should_register() {
		return $this->get_register();
	}
}
