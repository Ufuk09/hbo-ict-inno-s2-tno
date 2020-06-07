<?php

namespace TNO\ContactForm7\Utilities\Helpers;

class CF7Helper {
    private function getAllForms() {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $cf7Forms = get_posts( $args );
        return $cf7Forms;
    }

    private function extractInputsFromForm($post) {
        $post_content = $post->post_content;
        $post_content = strstr($post_content, 'TNO', true);
        $re = '/\[(?:\w+\*?\s+)?([^][]+)]/';
        preg_match_all($re, $post_content, $fields);
        $uniqueFields = array_unique($fields[1]);
        return $uniqueFields;
    }

    function getAllTargets() {
        $cf7Forms = $this->getAllForms();
        $post_ids = wp_list_pluck( $cf7Forms , 'post_title', 'ID' );
        $res = $post_ids;
        return $res;
    }

    function getAllInputs()
    {
        $forms = $this->getAllForms();
        $arrayForms = array();
        foreach ($forms as $form) {
            array_push($arrayForms, array(array($form->ID ,$form->post_title), $this->extractInputsFromForm($form)));
        }
        return $arrayForms;
    }
}