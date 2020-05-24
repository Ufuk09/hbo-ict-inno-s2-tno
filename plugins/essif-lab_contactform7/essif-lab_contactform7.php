<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also Application all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name: eSSIF-Lab-ContactForm7
 * Plugin URI: https://github.com/LSVH/hbo-ict-inno-s2-tno
 * Description: Subplugin to support ContactForm7 in the eSSIF-Lab plugin.
 * Version: 1.0
 * Author: Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen
 * Text Domain: essif-lab_contactform7
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ESSIF_LAB_CONTACTFORM7_VERSION', '1.0.0' );

function activate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'Application/class-essif-lab_contactform7-activator.php';
    $activator = new Essif_Lab_contactform7_Activator();
    $activator->activate();
}

function deactivate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'Application/class-essif-lab_contactform7-deactivator.php';
    $deactivator = new Essif_Lab_contactform7_Deactivator();
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_essif_lab_contactform7' );
register_deactivation_hook( __FILE__, 'deactivate_essif_lab_contactform7' );

require plugin_dir_path( __FILE__ ) . 'Application/class-essif-lab_contactform7.php';

function run_essif_lab_contactform7() {

	$plugin = new Essif_Lab_contactform7();
	$plugin->run();

}
run_essif_lab_contactform7();
