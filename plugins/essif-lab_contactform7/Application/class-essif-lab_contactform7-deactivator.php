<?php

class Essif_Lab_contactform7_Deactivator {
	public function deactivate() {
	    /**
	     *  Deactivate the hook
	     */
        $hook = "['contact-form-7' => 'Contact Form 7']";
        $usedHook = apply_filters("essif-lab_select_hook", []);
        // Temp placeholder to prevent errors
        $usedHook = [];
        if (in_array($hook, $usedHook)) {
            do_action("essif-lab_delete_hook", $hook);
        }

        /**
         *  Unload the targets
         */
        require_once plugin_dir_path( __FILE__ ) . '../Implementation/class-essif-lab_contactform7-logic.php';
        $targets = apply_filters("essif-lab_select_target", $hook);
        // Temp placeholder to prevent errors
        $targets = [];
        if (!empty($targets)) {
            foreach ($targets as $target) {
                apply_filters("essif-lab_delete_target", $target, $hook);
            }
        }
    }

}
