<?php
/**
 * Abstract shortcode class.
 */
namespace HRHub\Shortcode;

use HRHub\Traits\Hook;

abstract class AbstractShortcode {

	use Hook;

	/**
	 * Shortcode tag.
	 *
	 * @var string
	 */
	protected string $tag = '';

	/**
	 * Shortcode attributes.
	 *
	 * @var array
	 */
	protected array $attributes = [];

	/**
	 * Shortcode default attributes.
	 *
	 * @var array
	 */
	protected array $default_attributes = [];

	/**
	 * Get attributes.
	 *
	 * @return array
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * Set attributes.
	 *
	 * @param array $attributes
	 * @return self
	 */
	public function set_attributes( array $attributes ): self {
		$this->attributes = $this->parse_attributes( $attributes );
		return $this;
	}

	/**
	 * Get tag.
	 *
	 * @return string
	 */
	public function get_tag(): string {
		return $this->tag;
	}

	/**
	 * Parse attributes.
	 *
	 * @param array $attributes
	 * @return array
	 */
	protected function parse_attributes( array $attributes ): array {
		return shortcode_atts(
			$this->default_attributes,
			$attributes,
			$this->get_tag()
		);
	}

	/**
	 * Register shortcode.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( $this->get_tag(), [ $this, 'shortcode_callback' ] );
	}

	/**
	 * Shortcode callback.
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function shortcode_callback( array $attributes = [] ): string {
		$this->set_attributes( $attributes );
		return $this->filter( 'shortcode:content', $this->get_content(), $this->get_tag(), $this->get_attributes() );
	}

	/**
	 * Get shortcode content.
	 *
	 * @return string
	 */
	abstract public function get_content(): string;
}
