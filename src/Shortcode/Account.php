<?php

namespace HRHub\Shortcode;

use HRHub\AssetManager\AssetManager;

use function HRHub\Helper\template_loader;

class Account extends AbstractShortcode {

	/**
	 * Shortcode tag.
	 *
	 * @var string
	 */
	protected string $tag = 'hrhub:account';

	public function get_content(): string {
		if ( ! is_user_logged_in() ) {
			hrhub( AssetManager::class )->enqueue_all_styles();
			return template_loader()->get_template_html( 'login' );
		}
		return '';
	}
}
