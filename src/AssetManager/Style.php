<?php

/**
 * Style file.
 */

namespace HRHub\AssetManager;

use HRHub\AssetManager\Abstracts\Asset;
use HRHub\AssetManager\Enums\Location;
use HRHub\AssetManager\Enums\Media;

class Style extends Asset {

	/**
	 * Asset type.
	 *
	 * @var string
	 */
	protected $type = 'style';

	/**
	 * Style data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'media' => Media::ALL,
	);

	/**
	 * Constructor.
	 *
	 * @param string $handle        Name of the style.
	 * @param string|callable $src  Full URL of the script or path of the script relative to the WP root directory or a callable function which will return src.
	 * @param array $dependencies   An array of registered style handles this style depends on.
	 * @param string $version       String specifying style version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version.
	 * @param string $media         The media for which this stylesheet has been defined.
	 * @param callable $callback   Callback to determine whether to enqueue the asset or not.
	 * @param bool $register_only Whether to register only asset.
	 */
	public function __construct( $handle, $src = '', $dependencies = array(), $version = '', $media = Media::ALL, $location = Location::ALL, $callback = null, $register = false ) {
		$this->extra_data = array(
			'media' => $media,
		);

		parent::__construct( $handle, $src, $dependencies, $version, $location, $callback, $register );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return style media.
	 *
	 * @return string
	 */
	public function get_media() {
		return $this->__get( 'media ' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set media  of the style.
	 *
	 * @param string $media Style media
	 */
	public function set_media( $media ) {
		$this->__set( 'media', $media );
	}
}
