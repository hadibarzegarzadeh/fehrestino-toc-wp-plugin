<?php
class Fehrestino_Public {
	private $plugin_name;
	private $version;
    private $options;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->options = get_option('fehrestino_options', array(
            'template' => 'minimal',
            'auto_insert' => true,
            'heading_text' => 'فهرست مطالب'
        ));

        add_shortcode('fehrestino', array($this, 'generate_toc_shortcode'));
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fehrestino-public.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fehrestino-public.js', array( 'jquery' ), $this->version, false );
	}

    public function inject_toc($content) {
        if (!is_singular() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $auto_insert = isset($this->options['auto_insert']) ? $this->options['auto_insert'] : 1;

        if ($auto_insert) {
            $toc = $this->create_toc($content);
            if ($toc) {

                $content = $toc . $content;
            }
        }

        return $this->add_ids_to_headings($content);
    }

    public function generate_toc_shortcode($atts) {
        $content = get_the_content();
        return $this->create_toc($content);
    }

    private function add_ids_to_headings($content) {
        $pattern = '/<h([2-6])([^>]*)>(.*?)<\/h\1>/i';
        $content = preg_replace_callback($pattern, function($matches) {
            $level = $matches[1];
            $attrs = $matches[2];
            $text = strip_tags($matches[3]);
            $id = sanitize_title($text);

            if (strpos($attrs, 'id=') === false) {
                $attrs .= ' id="' . $id . '"';
            }

            return "<h{$level}{$attrs}>{$matches[3]}</h{$level}>";
        }, $content);

        return $content;
    }

    private function create_toc($content) {
        $pattern = '/<h([2-6])([^>]*)>(.*?)<\/h\1>/i';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        if (empty($matches) || count($matches) < 2) {
            return '';
        }

        $toc_items = array();
        foreach ($matches as $match) {
            $level = $match[1];
            $text = strip_tags($match[3]);
            $id = sanitize_title($text);
            $toc_items[] = array('level' => $level, 'text' => $text, 'id' => $id);
        }

        $template = isset($this->options['template']) ? $this->options['template'] : 'minimal';
        $heading_text = isset($this->options['heading_text']) ? $this->options['heading_text'] : 'فهرست مطالب';

        ob_start();
        $template_file = plugin_dir_path(__FILE__) . "partials/templates/template-{$template}.php";

        if (file_exists($template_file)) {
            include $template_file;
        } else {
            include plugin_dir_path(__FILE__) . "partials/templates/template-minimal.php";
        }

        return ob_get_clean();
    }
}
