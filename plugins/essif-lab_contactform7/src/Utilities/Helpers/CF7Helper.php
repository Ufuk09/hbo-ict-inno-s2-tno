<?php

namespace TNO\ContactForm7\Utilities\Helpers;

use TNO\ContactForm7\Utilities\WP;

class CF7Helper
{
    private function extractInputsFromForm($post)
    {
        $res = [];
        if ($post->post_content != null) {
            $post_content = $post->post_content;
            $post_content = (string)strstr($post_content, 'TNO', true);
            $re = '/\[(?:\w+\*?\s+)?([^][]+)]/';
            preg_match_all($re, $post_content, $fields);
            $slugs = array_unique($fields[1]);
            $titles = str_replace("-"," ", $slugs);
            $res = [$slugs, $titles ];
        }
        return $res;
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
            array_push($arrayForms, array($form->ID, $form->post_title), $this->extractInputsFromForm($form));
            break;
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
            $targetHook = $wp->selectInput([$target[0] => $target[1]]);

            $slugs = $input[1][0];
            $titles = $input[1][1];
            $inputs = [ $slugs, $titles ];

            foreach ($inputs as $inp) {
                if (!in_array($inp, $targetHook)) {
                    $wp->insertInput($inp[0], $inp[1], $target[0]);
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
            $targetHook = $wp->selectInput([$target[0] => $target[1]]);

            $slugs = $input[1][0];
            $titles = $input[1][1];
            $inputs = [ $slugs, $titles ];

            foreach ($inputs as $inp) {
                if (in_array($inp, $targetHook)) {
                    $wp->deleteInput($inp[0], $inp[1], $target[0]);
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