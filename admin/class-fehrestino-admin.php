<?php
class Fehrestino_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles( $hook ) {
        if ( 'toplevel_page_fehrestino' !== $hook ) {
            return;
        }
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fehrestino-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts( $hook ) {
        if ( 'toplevel_page_fehrestino' !== $hook ) {
            return;
        }
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fehrestino-admin.js', array( 'jquery' ), $this->version, false );
	}

    public function add_plugin_admin_menu() {
		add_menu_page(
			'تنظیمات فهرستینو',
			'فهرستینو',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' ),
			'dashicons-list-view',
			80
		);
	}

	public function display_plugin_setup_page() {
		require_once 'partials/fehrestino-admin-display.php';
	}

    public function register_settings() {
		register_setting( 'fehrestino_options_group', 'fehrestino_options', array($this, 'sanitize_options') );
	}

    public function sanitize_options($input) {
        $sanitized = array();
        if(isset($input['template'])) {
            $sanitized['template'] = sanitize_text_field($input['template']);
        }
        if(isset($input['auto_insert'])) {
            $sanitized['auto_insert'] = (bool) $input['auto_insert'];
        }
        if(isset($input['heading_text'])) {
            $sanitized['heading_text'] = sanitize_text_field($input['heading_text']);
        }
        return $sanitized;
    }
}
