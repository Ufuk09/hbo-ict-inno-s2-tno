<?php

/**
 * The core plugin class.
 */

//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Implementation/essif-lab_contactform7-test.php';

namespace TNO\ContactForm7\Application;

use TNO\ContactForm7\Implementation\Hooks;

class Plugin {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'ESSIF_LAB_CONTACTFORM7_VERSION' ) ) {
			$this->version = ESSIF_LAB_CONTACTFORM7_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'essif-lab_contactform7';

		$this->load_dependencies();
        $this->define_hooks();
	}

	private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Implementation/Button.php';

        $this->loader = new Loader();
	}

    private function define_hooks() {
        $plugin_hooks = new Hooks( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action('wp_enqueue_scripts', $plugin_hooks, 'enqueue_scripts');
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
