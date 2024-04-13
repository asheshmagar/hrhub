<?php

namespace HRHub\Email;

use HRHub\Traits\Hook;

use function HRHub\Helper\template_loader;

class EmailHooks {

	use Hook;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	protected function init_hooks() {
		$this->add_action( 'hrhub:employee:created', [ $this, 'send_employee_account_created_email' ], 10, 2 );
	}

	/**
	 * Send an employee account created email.
	 *
	 * @param int $employee_id
	 * @param array $employee_data
	 * @return void
	 */
	public function send_employee_account_created_email( $employee_id, $employee_data ) {
		$message = template_loader()
		->set_template_data( $employee_data )
		->set_template_data(
			array_merge(
				$employee_data,
				[
					'user_id'   => $employee_id,
					'login_url' => site_url( 'wp-login.php' ),
				]
			),
			'user'
		)
		->get_template_html(
			'emails/employee-new-account',
		);

		$email = new Email(
			[
				'from'    => get_option( 'admin_email' ),
				'to'      => $employee_data['user_email'],
				'subject' => __( 'Welcome to HR Hub!', 'hrhub' ),
				'message' => $message,
			]
		);

		$email->send();
	}
}
