<?php

namespace HRHub\Email;

	/**
	 * Class EmailRecipient
	 */
class EmailRecipient {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * Instantiates a new email recipient object
	 *
	 * @param string|object|array|null $recipient
	 */
	public function __construct( $recipient = null ) {
		if ( null !== $recipient ) {
			$this->set( $recipient );
		}
	}

	/**
	 * Setup the email recipient properties
	 *
	 * @param string|object|array $recipient
	 */
	public function set( $recipient ): void {
		if ( is_string( $recipient ) ) {
			$this->parse_recipient( $recipient );
		} elseif ( is_object( $recipient ) ) {
			$recipient = (array) $recipient;
			$this->set_from_array( $recipient );
		} elseif ( is_array( $recipient ) ) {
			$this->set_from_array( $recipient );
		}
	}

	/**
	 * Parse a text recipient
	 *
	 * @param string $text
	 */
	protected function parse_recipient( string $text ): void {
		$parts       = explode( ' ', $text );
		$this->email = trim( array_pop( $parts ), '<>' );
		$this->name  = implode( ' ', $parts );
	}

	/**
	 * Set the recipient properties from an array
	 *
	 * @param array $recipient
	 */
	protected function set_from_array( array $recipient ): void {
		$this->name  = $recipient['name'] ?? '';
		$this->email = $recipient['email'] ?? '';
	}

	/**
	 * Validate the recipient email
	 *
	 * @return bool
	 */
	public function validate(): bool {
		return is_email( $this->email );
	}

	/**
	 * Convert the object into a string
	 *
	 * @return string
	 */
	public function __toString(): string {
		return empty( $this->name ) ? $this->email : "{$this->name} <{$this->email}>";
	}
}
