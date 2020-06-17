<?php

namespace TNO\ContactForm7\Utilities\Helpers;

use TNO\ContactForm7\Utilities\WP;

class CF7Helper
{
    private function extractInputsFromForm($post)
    {
        $uniqueFields = [];
        $uniqueKeys = [];
        if ($post->post_content != null) {
            $post_content = $post->post_content;
            $post_content = (string)strstr($post_content, 'TNO', true);
//            var_dump($post_content);
            //$post_content = "Je naam (verplicht) [text* your-test] Je e-mailadres (verplicht) [email* your-tester] Onderwerp [text your-testerer] Je bericht [textarea your-testererer] [submit \"Verzenden\"] 1 Postcode [text* postalCode] Adres [email* streetAddress] Onderwerp [text your-subject] Je bericht [textarea your-message] [submit \"Verzenden\"] [essif_lab] 1";
            $re = '/([^][\s]+(?:\h+[^][\s]+)*)?\h+\[(?:\w+\*?\h+)?([^][]+)]/';
            preg_match_all($re, $post_content, $fields);
            $uniqueFields = array_unique($fields[2]);
            $uniqueKeys = array_unique($fields[1]);
            var_dump($uniqueKeys);

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
        if (!in_array($hook, $usedHook)) {
            $wp->insertHook();
        }

        /**
         *  Insert the targets
         */
        $targets = $wp->selectTarget();
        foreach ($this->getAllTargets() as $key => $target) {
            if (!in_array($target, $targets)) {
                $wp->insertTarget($key, $target);
            }
        }

        /**
         *  Insert the inputs
         */
        foreach ($this->getAllInputs() as $input) {
            $target = $input[0];
            $input = $input[1];
            $inputs = $wp->selectInput([$target[0] => $target[1]]);
            //apply_filters("essif-lab_select_input", $target);
            foreach ($input as $inp) {
                if (!in_array($inp, $inputs)) {
                    //$wp->insertInput($slug, $title, $target[0]);
                    //$wp->insertInput($inp[0], $inp[1], $target[0]);
                    //do_action("essif-lab_insert_input", $inp, $target);
                }
            }
        }
    }

    function deleteAllOnDeactivate()
    {
        $hook = [
            "contact-form-7" => "Contact Form 7"
        ];

        $wp = new WP();

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
        $targets = $wp->selectTarget();
        if (!empty($targets)) {
            foreach ($this->getAllTargets() as $key => $target) {
                if (in_array($target, $targets)) {
                    $wp->deleteTarget($key, $target);
                }
            }
        }

        /**
         *  Delete the hook
         */
        $usedHook = $wp->selectHook();
        if (in_array($hook, $usedHook)) {
            $wp->deleteHook();
        }

    }
}