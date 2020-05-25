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

use TNO\ContactForm7\Application\Activator;
use TNO\ContactForm7\Application\Deactivator;
use TNO\ContactForm7\Application\Plugin;

function activate_essif_lab_contactform7() {
    $activator = new Activator();
    $activator->activate();
}

function deactivate_essif_lab_contactform7() {
    $deactivator = new Deactivator();
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_essif_lab_contactform7' );
register_deactivation_hook( __FILE__, 'deactivate_essif_lab_contactform7' );

function run_essif_lab_contactform7() {
	$plugin = new Plugin();
	$plugin->run();
}
run_essif_lab_contactform7();