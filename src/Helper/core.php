<?php

namespace HRHub\Helper;

/**
 * Get filesystem instance.
 *
 * @return \WP_Filesystem_Direct|null
 */
function fs(): ?\WP_Filesystem_Direct {
	/**
	 * WP_FIlesystem_Direct instance.
	 *
	 *  @var \WP_Filesystem_Direct $wp_filesystem WP_FIlesystem_Direct instance.
	 */
	global $wp_filesystem;

	if ( ! $wp_filesystem || 'direct' !== $wp_filesystem->method ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		$credentials = request_filesystem_credentials( '', 'direct' );
		WP_Filesystem( $credentials );
	}

	return $wp_filesystem;
}

/**
 * Is dashboard page.
 *
 * @return boolean
 */
function is_dashboard_page(): bool {
	return is_admin() && ( get_current_screen()->id === 'toplevel_page_hrhub' );
}
