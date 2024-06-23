<?php
/**
 * Plugin Name: HR Hub
 * Plugin URI: https://asheshthapa.com.np/
 * Description: Human resource management system.
 * Author: Ashesh
 * Author URI: https://asheshthapa.com.np/
 * Text Domain: hrhub
 * Domain Path: /languages
 * Version: 0.1.0
 *
 * @package HRHub
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/bootstrap/bootstrap.php';

use DI\DependencyException;
use DI\NotFoundException;
use HRHub\App;

/**
 * Main function.
 *
 * @template T
 * @param class-string<T> $class_name
 * @throws DependencyException
 * @throws NotFoundException
 * @return T|mixed
 */
function hrhub( $class_name ) {
	/** @var \DI\Container $hrhub */
	global $hrhub;
	return $hrhub->get( $class_name );
}

// Init app.
hrhub( App::class )->init();
