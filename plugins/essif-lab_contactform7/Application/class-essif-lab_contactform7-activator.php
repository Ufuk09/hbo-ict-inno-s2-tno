<?php

class Essif_Lab_contactform7_Activator {
	public function activate() {
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

        /**
         *  Load the targets
         */
        require_once plugin_dir_path( __FILE__ ) . '../Implementation/class-essif-lab_contactform7-logic.php';
        $logic = new Essif_Lab_contactform7_Logic();
        $targets = apply_filters("essif-lab_select_target", $hook);
        // Temp placeholder to prevent errors
        $targets = [];
        foreach ($logic->getAllTargets() as $target) {
            if (!in_array($target, $targets)) {
                apply_filters("essif-lab_insert_target", $target, $hook);
            }
        }
    }

}