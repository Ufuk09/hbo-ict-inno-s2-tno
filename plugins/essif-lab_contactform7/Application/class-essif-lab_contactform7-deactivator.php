<?php

class Essif_Lab_contactform7_Deactivator {
	public function deactivate() {

        require_once plugin_dir_path( __FILE__ ) . '../Implementation/class-essif-lab_contactform7-logic.php';
        $logic = new Essif_Lab_contactform7_Logic();
        $hook = $logic->getHook();

        /**
         *  Deactivate the inputs
         */
        foreach ($logic->getAllInputs() as $input) {
            $target = $input[0];
            $input = $input[1];
            $inputs = apply_filters("essif-lab_select_input", $target);
            foreach ($input as $inp) {
                if (in_array($inp, $inputs)) {
                    do_action("essif-lab_delete_input", $inp, $target);
                }
            }
        }

        /**
         *  Deactivate the targets
         */
        require_once plugin_dir_path( __FILE__ ) . '../Implementation/class-essif-lab_contactform7-logic.php';
        $targets = apply_filters("essif-lab_select_target", $hook);
        if (!empty($targets)) {
            foreach ($targets as $target) {
                apply_filters("essif-lab_delete_target", $target, $hook);
            }
        }

	    /**
	     *  Deactivate the hook
	     */
        $hook = "['contact-form-7' => 'Contact Form 7']";
        $usedHook = apply_filters("essif-lab_select_hook", []);
        if (in_array($hook, $usedHook)) {
            do_action("essif-lab_delete_hook", $hook);
        }

    }

}
