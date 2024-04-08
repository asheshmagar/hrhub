<?php
/**
 * Admin class.
 */
namespace HRHub;

use HRHub\AssetManager\AssetManager;
use HRHub\Traits\Hook;

/**
 * Admin class.
 */
class Admin {

	use Hook;

	/**
	 * Init.
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Init.
	 *
	 * @return void
	 */
	private function init_hooks() {
		$this->add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		$this->add_action( 'in_admin_header', [ $this, 'hide_admin_notices' ] );
		$this->add_action( 'init', [ $this, 'remove_menus' ], 1 );
	}

	public function remove_menus() {
		$user = wp_get_current_user();
		if ( ( in_array( 'administrator', $user->roles, true ) || is_super_admin( $user->ID ) ) && isset( $_GET['page'] ) && 'hrhub' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			remove_action( 'admin_init', '_wp_admin_bar_init' );
			wp_deregister_style( 'wp-admin' );
			echo '<style>#adminmenumain, #wpfooter {display: none;}</style>';
		}
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$page = add_menu_page(
			'HR Hub',
			'HR Hub',
			'manage_options',
			'hrhub',
			[ $this, 'markup' ],
			'data:image/svg+xml;base64,' . base64_encode( '<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 640 512" height="200px" width="200px" xmlns="http://www.w3.org/2000/svg"><path d="M72 88a56 56 0 1 1 112 0A56 56 0 1 1 72 88zM64 245.7C54 256.9 48 271.8 48 288s6 31.1 16 42.3V245.7zm144.4-49.3C178.7 222.7 160 261.2 160 304c0 34.3 12 65.8 32 90.5V416c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V389.2C26.2 371.2 0 332.7 0 288c0-61.9 50.1-112 112-112h32c24 0 46.2 7.5 64.4 20.3zM448 416V394.5c20-24.7 32-56.2 32-90.5c0-42.8-18.7-81.3-48.4-107.7C449.8 183.5 472 176 496 176h32c61.9 0 112 50.1 112 112c0 44.7-26.2 83.2-64 101.2V416c0 17.7-14.3 32-32 32H480c-17.7 0-32-14.3-32-32zm8-328a56 56 0 1 1 112 0A56 56 0 1 1 456 88zM576 245.7v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM320 32a64 64 0 1 1 0 128 64 64 0 1 1 0-128zM240 304c0 16.2 6 31 16 42.3V261.7c-10 11.3-16 26.1-16 42.3zm144-42.3v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM448 304c0 44.7-26.2 83.2-64 101.2V448c0 17.7-14.3 32-32 32H288c-17.7 0-32-14.3-32-32V405.2c-37.8-18-64-56.5-64-101.2c0-61.9 50.1-112 112-112h32c61.9 0 112 50.1 112 112z"></path></svg>' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			2
		);
		$this->add_action( 'load-' . $page, [ $this, 'load_hrhub_dashboard' ] );
		remove_submenu_page( 'hrhub', 'hrhub' );
	}

	/**
	 * Admin page.
	 *
	 * @return void
	 */
	public function markup() {
		echo '<div id="hrhub"></div>';
	}

	/**
	 * On dashboard load.
	 *
	 * @return void
	 */
	public function load_hrhub_dashboard() {
		wp_enqueue_media();
		$am = hrhub( AssetManager::class );
		$am->enqueue_backend_scripts();
		$am->enqueue_backend_styles();
	}

	/**
	 * Hide admin notices
	 *
	 * @return void
	 */
	public function hide_admin_notices() {
		// Bail if we're not on a BlockArt screen or page.
		if ( empty( $_REQUEST['page'] ) || false === strpos( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ), 'hrhub' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		global $wp_filter;
		$ignore_notices = $this->filter( 'hide-notices/ignore', array() );

		foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $wp_notice ) {
			if ( empty( $wp_filter[ $wp_notice ] ) ) {
				continue;
			}

			$hook_callbacks = $wp_filter[ $wp_notice ]->callbacks;

			if ( empty( $hook_callbacks ) || ! is_array( $hook_callbacks ) ) {
				continue;
			}

			foreach ( $hook_callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $callback ) {
					if ( ! empty( $name ) && in_array( $name, $ignore_notices, true ) ) {
						continue;
					}
					if (
						! empty( $callback['function'] ) &&
						! is_a( $callback['function'], '\Closure' ) &&
						isset( $callback['function'][0], $callback['function'][1] ) &&
						is_object( $callback['function'][0] ) &&
						in_array( $callback['function'][1], $ignore_notices, true )
					) {
						continue;
					}
					unset( $wp_filter[ $wp_notice ]->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
