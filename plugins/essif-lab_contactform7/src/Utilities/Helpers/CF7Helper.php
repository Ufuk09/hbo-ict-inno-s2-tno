<?php

namespace TNO\ContactForm7\Utilities\Helpers;

use TNO\ContactForm7\Utilities\WP;
use TNO\ContactForm7\Views\Button;

class CF7Helper extends WP
{
    public function __construct()
    {
        parent::__construct($this);
    }

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
        $cf7Forms = parent::getAllForms();
        $targets = parent::getTargetsFromForms($cf7Forms, 'post_title', 'ID');
        return $targets;
    }

    function getAllInputs()
    {
        $cf7Forms = parent::getAllForms();
        $arrayForms = array_map(null, array($cf7Forms->ID, $cf7Forms->post_title), $this->extractInputsFromForm($cf7Forms));
        return $arrayForms;
    }

    function addAllOnActivate()
    {
        $hook = [
            "contact-form-7" => "Contact Form 7"
        ];

        /**
         *  Insert the hook
         */
        $usedHook = parent::selectHook();
        if (!in_array($hook, $usedHook)) {
            parent::insertHook();
        }

        /**
         *  Insert the targets
         */
        $targets = parent::selectTarget();
        foreach ($this->getAllTargets() as $key => $target) {
            if (!in_array($target, $targets)) {
                parent::insertTarget($key, $target);
            }
        }

        /**
         *  Insert the inputs
         */
        foreach ($this->getAllInputs() as $input) {
            $target = $input[0];
            $targetHook = parent::selectInput([$target[0] => $target[1]]);

            $slugs = $input[1][0];
            $titles = $input[1][1];
            $inputs = [ $slugs, $titles ];

            foreach ($inputs as $inp) {
                if (!in_array($inp, $targetHook)) {
                    parent::insertInput($inp[0], $inp[1], $target[0]);
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
            $targetHook = parent::selectInput([$target[0] => $target[1]]);

            $slugs = $input[1][0];
            $titles = $input[1][1];
            $inputs = [ $slugs, $titles ];

            foreach ($inputs as $inp) {
                if (in_array($inp, $targetHook)) {
                    parent::deleteInput($inp[0], $inp[1], $target[0]);
                }
            }
        }

        /**
         *  Delete the targets
         */
        $targets = parent::selectTarget();
        if (!empty($targets)) {
            foreach ($this->getAllTargets() as $key => $target) {
                if (in_array($target, $targets)) {
                    parent::deleteTarget($key, $target);
                }
            }
        }

        /**
         *  Delete the hook
         */
        $usedHook = parent::selectHook();
        if (in_array($hook, $usedHook)) {
            parent::deleteHook();
        }

    }
}