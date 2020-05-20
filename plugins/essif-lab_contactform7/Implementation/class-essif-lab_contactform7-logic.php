<?php

class Essif_Lab_contactform7_Logic {

    public function getAllForms() {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $cf7Forms = get_posts( $args );
        return $cf7Forms;
    }

    public function getAllTargets() {
        $cf7Forms = $this->getAllForms();
        $post_ids = wp_list_pluck( $cf7Forms , 'post_title', 'ID' );

        $res = $post_ids;
        return $res;
    }

}