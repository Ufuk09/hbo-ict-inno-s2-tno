<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/admin
 * @author     Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen <ruben.sikkens@student.hu.nl>
 */
class Essif_Lab_contactform7_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/essif-lab_contactform7-admin.js', array( 'jquery' ), $this->version, false );

	}

}
