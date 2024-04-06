<?php
/**
 * Is dashboard page.
 *
 * @return bool
 */
function hrhub_is_dashboard_page() {
	return is_admin() && ( get_current_screen()->id === 'toplevel_page_hrhub' );
}
