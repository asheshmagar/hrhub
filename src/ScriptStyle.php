<?php
/**
 * ScriptStyle class.
 */
namespace HRHub;

use HRHub\AssetManager\AssetManager;
use HRHub\AssetManager\Enums\Location;
use HRHub\AssetManager\Enums\Media;
use HRHub\AssetManager\Script;
use HRHub\AssetManager\Style;
use HRHub\Traits\Hook;

/**
 * ScriptStyle class.
 */
class ScriptStyle {

	use Hook;

	/**
	 * Is dev flag.
	 *
	 * @var boolean
	 */
	public $is_dev = false;

	/**
	 * Constructor.
	 *
	 * @param AssetManager $asset_manager
	 */
	public function __construct( private AssetManager $asset_manager ) {
		$this->asset_manager = $asset_manager;
		$this->is_dev        = defined( 'HRHUB_IS_DEVELOPMENT' ) && HRHUB_IS_DEVELOPMENT;
	}

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
	private function init_hooks() {
		$this->add_action( 'init', [ $this, 'after_wp_init' ], 0 );
	}

	/**
	 * After wp init.
	 * @return void
	 */
	public function after_wp_init() {
		$this->register_scripts();
		$this->action( 'register:scripts', $this );

		$this->register_styles();
		$this->action( 'register:styles', $this );
	}

	private function register_scripts() {
		$dashboard_asset  = $this->get_asset_file( 'dashboard' );
		$dashboard_script = new Script(
			'hrhub-dashboard',
			$this->get_asset_url( 'dashboard.js', $this->is_dev ),
			$dashboard_asset['dependencies'],
			$dashboard_asset['version'],
			true,
			Location::BACKEND,
			null,
			true
		);

		$this->asset_manager::add( $dashboard_script );
	}

	/**
	 * Register styles.
	 */
	private function register_styles() {
		$dashboard_style = new Style(
			'hrhub-dashboard',
			$this->get_asset_url( 'dashboard.css', $this->is_dev ),
			[],
			HRHUB_VERSION,
			Media::ALL,
			Location::BACKEND,
			null,
			true
		);

		$this->asset_manager::add( $dashboard_style );
	}

	/**
	 * Get asset file.
	 *
	 * @param string $prefix
	 * @return array
	 */
	public function get_asset_file( string $prefix ): array {
		$asset_file = HRHUB_PLUGIN_DIR . "assets/build/$prefix.asset.php";
		return $this->filter(
			"assets/file/$prefix",
			file_exists( $asset_file )
			? include $asset_file
			: array(
				'dependencies' => [],
				'version'      => HRHUB_VERSION,
			)
		);
	}

	/**
	 * Get asset url.
	 *
	 * @param string $path
	 * @param boolean $is_dev
	 * @return string
	 */
	public function get_asset_url( string $path, bool $is_dev ): string {
		if ( $is_dev ) {
			return "http://localhost:8887/$path";
		}
		return plugins_url( "assets/build/$path", HRHUB_PLUGIN_FILE );
	}
}
