<?php

namespace HRHub;

use HRHub\Traits\Hook;

/**
 * Class TemplateLoader
 */
class TemplateLoader {

	use Hook;

	public const THEME_TEMPLATE_DIR  = 'hrhub';
	public const PLUGIN_TEMPLATE_DIR = 'templates';

	/**
	 * @var array $template_data_var_names Stores the custom variable names used for template data.
	 */
	private array $template_data_var_names = [];

	/**
	 * @var array $template_path_cache Stores the cached template paths.
	 */
	private array $template_path_cache = [];

	/**
	 * Cleans up template data when the object is destroyed.
	 */
	public function __destruct() {
		$this->unset_template_data();
	}

	/**
	 * Retrieves a template part.
	 *
	 * @param string      $slug The template slug.
	 * @param string|null $name The template name.
	 * @param bool        $load Whether to load the template.
	 *
	 * @return string|null The located template path, or null if not found.
	 */
	public function get_template_part( string $slug, ?string $name = null, bool $load = true ): ?string {
		$this->action( "template:part:{$slug}", $slug, $name );
		$templates = $this->get_template_file_names( $slug, $name );
		return $this->locate_template( $templates, $load, false );
	}

	/**
	 * Sets the template data.
	 *
	 * @param array  $data     The data to be used in the template.
	 * @param string $var_name The name of the variable to store the data in.
	 *
	 * @return $this
	 */
	public function set_template_data( array $data, string $var_name = 'data' ): self {
		global $wp_query;
		$wp_query->query_vars[ $var_name ] = (object) $data;
		if ( 'data' !== $var_name ) {
			$this->template_data_var_names[] = $var_name;
		}
		return $this;
	}

	/**
	 * Unset the template data.
	 *
	 * @return $this
	 */
	public function unset_template_data(): self {
		global $wp_query;
		$custom_var_names = array_unique( $this->template_data_var_names );
		foreach ( $custom_var_names as $var ) {
			if ( isset( $wp_query->query_vars[ $var ] ) ) {
				unset( $wp_query->query_vars[ $var ] );
			}
		}
		return $this;
	}

	/**
	 * Retrieves the template file names.
	 *
	 * @param string      $slug The template slug.
	 * @param string|null $name The template name.
	 *
	 * @return array The template file names.
	 */
	protected function get_template_file_names( string $slug, ?string $name ): array {
		$templates = [];
		if ( isset( $name ) ) {
			$templates[] = "{$slug}-{$name}.php";
		}
		$templates[] = "{$slug}.php";
		return $this->filter( 'template:part', $templates, $slug, $name );
	}

	/**
	 * Locates a template.
	 *
	 * @param array|string $template_names The template names to search for.
	 * @param bool         $load           Whether to load the template.
	 * @param bool         $should_require_once   Whether to use require_once when loading the template.
	 *
	 * @return string|null The located template path, or null if not found.
	 */
	protected function locate_template( array|string $template_names, bool $load = false, bool $should_require_once = true ): ?string {
		$cache_key = is_array( $template_names ) ? $template_names[0] : $template_names;
		if ( isset( $this->template_path_cache[ $cache_key ] ) ) {
			$located = $this->template_path_cache[ $cache_key ];
		} else {
			$located        = false;
			$template_names = array_filter( (array) $template_names );
			$template_paths = $this->get_template_paths();
			foreach ( $template_names as $template_name ) {
				$template_name = ltrim( $template_name, '/' );
				foreach ( $template_paths as $template_path ) {
					if ( file_exists( $template_path . $template_name ) ) {
						$located                                 = $template_path . $template_name;
						$this->template_path_cache[ $cache_key ] = $located;
						break 2;
					}
				}
			}
		}
		if ( $load && $located ) {
			load_template( $located, $should_require_once );
		}
		return $located;
	}

	/**
	 * Retrieves the template paths.
	 *
	 * @return array The template paths.
	 */
	protected function get_template_paths(): array {
		$theme_directory = trailingslashit( self::THEME_TEMPLATE_DIR );
		$file_paths      = [
			10  => trailingslashit( get_template_directory() ) . $theme_directory,
			100 => $this->get_templates_dir(),
		];
		if ( get_stylesheet_directory() !== get_template_directory() ) {
			$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
		}
		$file_paths = $this->filter( 'template:paths', $file_paths );
		ksort( $file_paths, SORT_NUMERIC );
		return array_map( 'trailingslashit', $file_paths );
	}

	/**
	 * Retrieves the templates directory.
	 *
	 * @return string The templates directory path.
	 */
	protected function get_templates_dir(): string {
		return trailingslashit( HRHUB_PLUGIN_DIR ) . self::PLUGIN_TEMPLATE_DIR;
	}

	/**
	 * Get template html.
	 *
	 * @param string $slug
	 * @param string|null $name
	 * @return string
	 */
	public function get_template_html( string $slug, ?string $name = null ) {
		ob_start();
		$this->get_template_part( $slug, $name );
		return ob_get_clean();
	}
}
