<?php

namespace HRHub\Email;

/**
 * Class Email
 */
class Email {
	/**
	 * @var string
	 */
	protected $from = '';

	/**
	 * @var string
	 */
	protected $from_name = '';

	/**
	 * @var array
	 */
	protected $to = [];

	/**
	 * @var string
	 */
	protected $subject = '';

	/**
	 * @var string
	 */
	protected $message = '';

	/**
	 * @var array
	 */
	protected $headers = [];

	/**
	 * @var array
	 */
	protected $attachments = [];

	/**
	 * @var string
	 */
	protected $content_type = 'text/html';

	/**
	 * @var string
	 */
	protected $charset;

	/**
	 * Instantiates a new email object
	 *
	 * @param string|object|array|null $data
	 */
	public function __construct( $data = null ) {
		if ( null !== $data ) {
			$this->setup( $data );
		}
	}

	/**
	 * Sets up the email
	 *
	 * @param string|object|array $data
	 */
	public function setup( $data ) {
		$this->set_from( $data['from'] ?? null );
		$this->set_to( $data['to'] ?? null );
		$this->set_cc( $data['cc'] ?? null );
		$this->set_bcc( $data['bcc'] ?? null );
		$this->set_subject( $data['subject'] ?? '' );
		$this->set_message( $data['message'] ?? '' );
		$this->set_headers( $data['headers'] ?? [] );
		$this->set_attachments( $data['attachments'] ?? [] );
		$this->set_content_type( $data['content_type'] ?? 'text/html' );
		$this->set_charset( $data['charset'] ?? null );
	}

	/**
	 * Set the from contact on the email
	 *
	 * @param string|object|array $recipient
	 */
	protected function set_from( $recipient ) {
		$from = new EmailRecipient( $recipient );
		if ( $from->validate() ) {
			$this->from      = $from->email;
			$this->from_name = $from->name;
		}
	}

	/**
	 * Set the recipient(s) of the email
	 *
	 * @param string|object|array $recipients
	 */
	protected function set_to( $recipients ) {
		$this->to = $this->parse_recipients( $recipients );
	}

	/**
	 * Set the CC recipient(s) of the email
	 *
	 * @param string|object|array $recipients
	 */
	protected function set_cc( $recipients ) {
		$this->add_css_recipients( $this->parse_recipients( $recipients ) );
	}

	/**
	 * Set the BCC recipient(s) of the email
	 *
	 * @param string|object|array $recipients
	 */
	protected function set_bcc( $recipients ) {
		$this->add_bcc_recipients( $this->parse_recipients( $recipients ) );
	}

	/**
	 * Set the email subject
	 *
	 * @param string $subject
	 */
	protected function set_subject( $subject ) {
		$this->subject = html_entity_decode( sanitize_text_field( $subject ) );
	}

	/**
	 * Set the email copy
	 *
	 * @param string $message
	 */
	protected function set_message( $message ) {
		$this->message = $message;
	}

	/**
	 * Set the email headers
	 *
	 * @param array $headers
	 */
	protected function set_headers( $headers ) {
		$this->headers = $headers;
	}

	/**
	 * Set the email attachments
	 *
	 * @param array $attachments
	 */
	protected function set_attachments( $attachments ) {
		$this->attachments = $this->parse_attachments( $attachments );
	}

	/**
	 * Set the email content type
	 *
	 * @param string $content_type
	 */
	protected function set_content_type( $content_type ) {
		$this->content_type = $content_type;
	}

	/**
	 * Set the email character set
	 *
	 * @param string $charset
	 */
	protected function set_charset( $charset ) {
		$this->charset = $charset;
	}

	/**
	 * Sends the email
	 *
	 * @return bool
	 */
	public function send() {
		$this->before_wp_mail();
		$sent = wp_mail( $this->to, $this->subject, $this->message, $this->headers, $this->attachments );
		$this->after_wp_mail();
		return $sent;
	}

	/**
	 * Before WP mail.
	 */
	protected function before_wp_mail() {
		add_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
		add_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'ge_content_type' ] );
		add_filter( 'wp_mail_charset', [ $this, 'get_charset' ] );
	}

	/**
	 * After WP mail.
	 */
	protected function after_wp_mail() {
		remove_filter( 'wp_mail_from', [ $this, 'get_from_email' ] );
		remove_filter( 'wp_mail_from_name', [ $this, 'get_from_name' ] );
		remove_filter( 'wp_mail_content_type', [ $this, 'ge_content_type' ] );
		remove_filter( 'wp_mail_charset', [ $this, 'get_charset' ] );
	}

	/**
	 * WordPress callback for setting the from email
	 *
	 * @param string $email
	 * @return string
	 */
	public function get_from_email( $email ) {
		return ! empty( $this->from ) && is_email( $this->from ) ? $this->from : $email;
	}

	/**
	 * WordPress callback for setting the from name
	 *
	 * @param string $name
	 * @return string
	 */
	public function get_from_name( $name ) {
		return ! empty( $this->from_name ) ? html_entity_decode( sanitize_text_field( $this->from_name ) ) : $name;
	}

	/**
	 * WordPress callback for setting the content type
	 *
	 * @param string $content_type
	 * @return string
	 */
	public function ge_content_type( $content_type ) {
		return ! empty( $this->content_type ) ? $this->content_type : $content_type;
	}

	/**
	 * WordPress callback for setting the charset
	 *
	 * @param string $charset
	 * @return string
	 */
	public function get_charset( $charset ) {
		return ! empty( $this->charset ) ? $this->charset : $charset;
	}

	/**
	 * Parse recipients from a string, object or array
	 *
	 * @param string|object|array $recipients
	 * @return array
	 */
	protected function parse_recipients( $recipients ): array {
		$parsed = [];
		if ( ! is_array( $recipients ) ) {
			$recipients = [ $recipients ];
		}
		foreach ( $recipients as $recipient ) {
			$email_recipient = new EmailRecipient( $recipient );
			if ( $email_recipient->validate() ) {
				$parsed[] = (string) $email_recipient;
			}
		}
		return $parsed;
	}

	/**
	 * Add a CC recipient to the email
	 *
	 * @param array $recipients
	 */
	protected function add_css_recipients( array $recipients ): void {
		foreach ( $recipients as $recipient ) {
			$this->add_header( "Cc: $recipient" );
		}
	}

	/**
	 * Add a BCC recipient to the email
	 *
	 * @param array $recipients
	 */
	protected function add_bcc_recipients( array $recipients ): void {
		foreach ( $recipients as $recipient ) {
			$this->add_header( "Bcc: $recipient" );
		}
	}

	/**
	 * Parse attachments from a string or array
	 *
	 * @param string|array $attachments
	 * @return array
	 */
	protected function parse_attachments( $attachments ): array {
		$parsed = [];
		if ( ! is_array( $attachments ) ) {
			$attachments = [ $attachments ];
		}
		foreach ( $attachments as $attachment ) {
			if ( file_exists( $attachment ) ) {
				$parsed[] = $attachment;
			}
		}
		return $parsed;
	}

	/**
	 * Add a header
	 *
	 * @param string $header
	 */
	protected function add_header( string $header ): void {
		$this->headers[] = $header;
	}
}
