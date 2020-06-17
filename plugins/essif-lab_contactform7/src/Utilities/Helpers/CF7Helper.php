<?php

namespace TNO\ContactForm7\Utilities\Helpers;

use TNO\ContactForm7\Utilities\WP;

class CF7Helper
{
    private function extractInputsFromForm($post)
    {
        $uniqueFields = [];
        if ($post->post_content != null) {
            $post_content = $post->post_content;
            $post_content = strstr($post_content, 'TNO', true);
            $re = '/\[(?:\w+\*?\s+)?([^][]+)]/';
            preg_match_all($re, $post_content, $fields);
            $uniqueFields = array_unique($fields[1]);
            print_r($uniqueFields);
        }
        return $uniqueFields;
    }

    function getAllTargets()
    {
        $wp = new WP();
        $cf7Forms = $wp->getAllForms();
        $targets = $wp->getTargetsFromForms($cf7Forms, 'post_title', 'ID');
        return $targets;
    }

    function getAllInputs()
    {
        $wp = new WP();
        $cf7Forms = $wp->getAllForms();
        $arrayForms = array();
        foreach ($cf7Forms as $form) {
            array_push($arrayForms, array(array($form->ID, $form->post_title), $this->extractInputsFromForm($form)));
        }
        return $arrayForms;
    }

    function addAllOnActivate()
    {
            $hook = [
                "contact-form-7" => "Contact Form 7"
            ];

            $wp = new WP();

            /**
             *  Insert the hook
             */
            $usedHook = $wp->selectHook();
            if (in_array($hook, $usedHook)) {
                do_action("essif-lab_insert_hook", $hook);
            }

            /**
             *  Insert the targets
             */
            $targets = apply_filters("essif-lab_select_target", $hook);
            foreach ($this->getAllTargets() as $target) {
                if (!in_array($target, $targets)) {
                    do_action("essif-lab_insert_target", $target, $hook);
                }
            }

            /**
             *  Insert the inputs
             */
            foreach ($this->getAllInputs() as $input) {
                $target = $input[0];
                $input = $input[1];
                $inputs = apply_filters("essif-lab_select_input", $target);
                foreach ($input as $inp) {
                    if (!in_array($inp, $inputs)) {
                        do_action("essif-lab_insert_input", $inp, $target);
                    }
                }
            }
        }

    function deleteAllOnDeactivate()
    {
        $hook = [
            "contact-form-7" => "Contact Form 7"
        ];

        /**
         *  Delete the inputs
         */
        foreach ($this->getAllInputs() as $input) {
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
         *  Delete the targets
         */
        $targets = apply_filters("essif-lab_select_target", $hook);
        if (!empty($targets)) {
            foreach ($targets as $target) {
                apply_filters("essif-lab_delete_target", $target, $hook);
            }
        }

        /**
         *  Delete the hook
         */
        $hook = "['contact-form-7' => 'Contact Form 7']";
        $usedHook = apply_filters("essif-lab_select_hook", []);
        if (in_array($hook, $usedHook)) {
            do_action("essif-lab_delete_hook", $hook);
        }

    }
}