<?php

add_action('wpcf7_init', 'custom_add_form_tag_essif_lab');

function custom_add_form_tag_essif_lab()
{
    wpcf7_add_form_tag('essif_lab', 'custom_essif_lab_form_tag_handler');
}

function custom_essif_lab_form_tag_handler($tag)
{
    return "<br /><input type=\"submit\" value=\"Gegevens inladen\" id=\"essif-lab\" class=\"wpcf7-form-control wpcf7-submit\">";
}

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
