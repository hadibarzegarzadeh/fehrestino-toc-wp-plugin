<?php
class Fehrestino {
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'FEHRESTINO_VERSION' ) ) {
			$this->version = FEHRESTINO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fehrestino';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once FEHRESTINO_PLUGIN_DIR . 'includes/class-fehrestino-loader.php';
		require_once FEHRESTINO_PLUGIN_DIR . 'includes/class-fehrestino-i18n.php';
		require_once FEHRESTINO_PLUGIN_DIR . 'admin/class-fehrestino-admin.php';
		require_once FEHRESTINO_PLUGIN_DIR . 'public/class-fehrestino-public.php';

		$this->loader = new Fehrestino_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Fehrestino_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Fehrestino_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	}

	private function define_public_hooks() {
		$plugin_public = new Fehrestino_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'inject_toc' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
