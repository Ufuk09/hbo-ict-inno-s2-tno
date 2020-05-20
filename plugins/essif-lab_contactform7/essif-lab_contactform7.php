<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name: eSSIF-Lab-ContactForm7
 * Plugin URI: https://github.com/LSVH/hbo-ict-inno-s2-tno
 * Description: Subplugin to support ContactForm7 in the eSSIF-Lab plugin.
 * Version: 1.0
 * Author: Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen
 * Text Domain:       essif-lab_contactform7
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ESSIF_LAB_CONTACTFORM7_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-essif-lab_contactform7-activator.php
 */
function activate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-essif-lab_contactform7-activator.php';
	Essif_Lab_contactform7_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-essif-lab_contactform7-deactivator.php
 */
function deactivate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-essif-lab_contactform7-deactivator.php';
	Essif_Lab_contactform7_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_essif_lab_contactform7' );
register_deactivation_hook( __FILE__, 'deactivate_essif_lab_contactform7' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-essif-lab_contactform7.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_essif_lab_contactform7() {

	$plugin = new Essif_Lab_contactform7();
	$plugin->run();

}
run_essif_lab_contactform7();
