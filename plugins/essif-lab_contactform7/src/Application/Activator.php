<?php

namespace TNO\ContactForm7\Application;

use TNO\ContactForm7\Implementation\Logic;

class Activator {
    public function activate() {

        $logic = new Logic();
        $hook = $logic->getHook();

        /**
         *  Activate the hook
         */
        $usedHook = apply_filters("essif-lab_select_hook", []);
        // Temp placeholder to prevent errors
        $usedHook = [];
        if (in_array($hook, $usedHook)) {
            do_action("essif-lab_insert_hook", $hook);
        }

        /**
         *  Activate the targets
         */
        $targets = apply_filters("essif-lab_select_target", $hook);
        // Temp placeholder to prevent errors
        $targets = [];
        foreach ($logic->getAllTargets() as $target) {
            if (!in_array($target, $targets)) {
                do_action("essif-lab_insert_target", $target, $hook);
            }
        }

        /**
         *  Activate the inputs
         */
        foreach ($logic->getAllInputs() as $input) {
            $target = $input[0];
            $input = $input[1];
            $inputs = apply_filters("essif-lab_select_input", $target);
            // Temp placeholder to prevent errors
            $inputs = [];
            foreach ($input as $inp) {
                if (!in_array($inp, $inputs)) {
                    do_action("essif-lab_insert_input", $inp, $target);
                }
            }
        }

    }

}