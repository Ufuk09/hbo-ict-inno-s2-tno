<?php
/**
 * @wordpress-plugin
 * Plugin Name: eSSIF-Lab-ContactForm7
 * Plugin URI: https://github.com/LSVH/hbo-ict-inno-s2-tno
 * Description: Subplugin to support ContactForm7 in the eSSIF-Lab plugin.
 * Version: 1.0
 * Author: Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen
 * Text Domain: essif-lab_contactform7
 */

if ( ! defined( 'WPINC' ) || !defined('ABSPATH') ) {
	die;
}

$classAutoloader = __DIR__.'/vendor/autoload.php';
if (file_exists($classAutoloader)) {
    require_once($classAutoloader);
}

use TNO\ContactForm7\Application\Essif_Lab_contactform7_Activator;
use TNO\ContactForm7\Application\Essif_Lab_contactform7_Deactivator;
use TNO\ContactForm7\Application\Essif_Lab_contactform7;

function activate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'src/Application/activator.php';
    $activator = new Essif_Lab_contactform7_Activator();
    $activator->activate();
}

function deactivate_essif_lab_contactform7() {
	require_once plugin_dir_path( __FILE__ ) . 'src/Application/deactivator.php';
    $deactivator = new Essif_Lab_contactform7_Deactivator();
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_essif_lab_contactform7' );
register_deactivation_hook( __FILE__, 'deactivate_essif_lab_contactform7' );

require plugin_dir_path( __FILE__ ) . 'src/Application/essif-lab_contactform7.php';

function run_essif_lab_contactform7() {
	$plugin = new Essif_Lab_contactform7();
	$plugin->run();
}
run_essif_lab_contactform7();
