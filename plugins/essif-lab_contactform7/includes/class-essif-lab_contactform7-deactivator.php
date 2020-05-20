<?php

/**
 * Fired during plugin deactivation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Essif_Lab_contactform7
 * @subpackage Essif_Lab_contactform7/includes
 * @author     Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen <ruben.sikkens@student.hu.nl>
 */
class Essif_Lab_contactform7_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        $hook = "['contact-form-7' => 'Contact Form 7']";
        $usedHook = apply_filters("essif-lab_select_hook");
        if ($usedHook.contains($hook)) {
            do_action("essif-lab_delete_hook", $hook);
        }
    }

}
