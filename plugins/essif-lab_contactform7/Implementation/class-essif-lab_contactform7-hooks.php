<?php

class Essif_Lab_contactform7_Hooks
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../js/essif-lab_contactform7-public.js', array( 'jquery' ), $this->version, false );
    }

}
