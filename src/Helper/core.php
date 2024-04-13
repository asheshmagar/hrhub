<?php

namespace HRHub\Helper;

use HRHub\TemplateLoader;

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

/**
 * Template loader.
 *
 * @return TemplateLoader
 */
function template_loader() {
	return new TemplateLoader();
}

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page.
 * @param string $option Option name to store the page's ID.
 * @param string $page_title (default: '') Title for the new page.
 * @param string $page_content (default: '') Content for the new page.
 * @param int    $post_parent (default: 0) Parent for the new page.
 * @param string $post_status (default: publish) The post status of the new page.
 * @return int page ID.
 */
function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0, $post_status = 'publish' ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 ) {
		$page_object = get_post( $option_value );

		if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
			return $page_object->ID;
		}
	}

	if ( strlen( $page_content ) > 0 ) {
		$shortcode        = str_replace( array( '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ), '', $page_content );
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%" ) );
	} else {
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	}

	$valid_page_found = apply_filters( 'hrhub:create:page:id', $valid_page_found, $slug, $page_content ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	if ( $valid_page_found ) {
		if ( $option ) {
			update_option( $option, $valid_page_found );
		}
		return $valid_page_found;
	}

	if ( strlen( $page_content ) > 0 ) {
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'          => $page_id,
			'post_status' => $post_status,
		);
		wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => $post_status,
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );

		do_action( 'hrhub:page:created', $page_id, $page_data ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}
