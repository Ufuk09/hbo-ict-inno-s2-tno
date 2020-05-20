<?php

/**
 * Fired during plugin activation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/includes
 * @author     Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen <ruben.sikkens@student.hu.nl>
 */

class Essif_Lab_contactform7_Activator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        /**
         *  Activate the hook
         */
        $hook = "['contact-form-7' => 'Contact Form 7']";
        $usedHook = apply_filters("essif-lab_select_hook", []);
        // Temp placeholder to prevent errors
        $usedHook = [];
        if (in_array($hook, $usedHook)) {
            do_action("essif-lab_insert_hook", $hook);
        }
    }

}