<?php

namespace HRHub;

use HRHub\Shortcode\Account;
use HRHub\Traits\Hook;

class Shortcodes {

	use Hook;

	public function __construct(
		protected Account $account
	) {}

	public function init() {
		$this->add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	public function register_shortcodes() {
		$this->account->register();
	}
}
