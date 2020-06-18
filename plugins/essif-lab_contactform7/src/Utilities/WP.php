<?php

namespace TNO\ContactForm7\Utilities;

use TNO\ContactForm7\Utilities\Contracts\BaseUtility;
use TNO\ContactForm7\Views\Button;
use TNO\ContactForm7\Utilities\Helpers\CF7Helper;

class WP extends BaseUtility
{
    private $cf7helper;
    private $button;

    public function __construct(CF7Helper $cf7helper, Button $button)
    {
        $this->cf7helper = $cf7helper;
        $this->button = $button;
    }

    CONST ACTION_PREFIX = "essif-lab_";

    function getAllForms()
    {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        $cf7Forms = get_posts($args);
        return $cf7Forms;
    }

    function getTargetsFromForms(array $cf7Forms, string $post_title, string $id)
    {
        $targets = wp_list_pluck($cf7Forms, 'post_title', 'ID');
        return $targets;
    }

    function insertHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->insert("hook", [$slug => $title]);
    }

    function insertTarget(int $id, string $title, string $hookSlug = self::SLUG)
    {
        $this->insert("target", [$id => $title], $hookSlug);
    }

    function insertInput(string $slug, string $title, int $targetId)
    {
        $this->insert("input", [$slug => $title], $targetId);
    }

    private function insert($suffix, ...$params)
    {
        do_action(self::ACTION_PREFIX . "insert_" . $suffix, ... $params);
    }

    function deleteHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->delete("hook", [$slug => $title]);
    }

    function deleteTarget(int $id, string $title, string $hookSlug = self::SLUG)
    {
        $this->delete("target", [$id => $title], $hookSlug);
    }

    function deleteInput(string $slug, string $title, int $targetId)
    {
        $this->delete("input", [$slug => $title], $targetId);
    }

    private function delete($suffix, ...$params)
    {
        do_action(self::ACTION_PREFIX . "delete_" . $suffix, ... $params);
    }

    function selectHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        return $this->select("hook", [$slug => $title]);
    }

    function selectTarget(array $items = [], string $hookSlug = self::SLUG)
    {
        return $this->select("target", $items, $hookSlug);
    }

    function selectInput(array $items = [], string $hookSlug = self::SLUG)
    {
        return $this->select("input", $items, $hookSlug);
    }

    private function select($suffix, ...$params)
    {
        apply_filters(self::ACTION_PREFIX . "select_" . $suffix, ... $params);
    }

    function addEssifLabButton () {
        add_action('wpcf7_init', array( $this, 'custom_add_form_tag_essif_lab' ) );
    }

    function custom_add_form_tag_essif_lab()
    {
        $this->addFormTag();
    }

    function addFormTag()
    {
        wpcf7_add_form_tag('essif_lab', array ( $this->button, 'custom_essif_lab_form_tag_handler' ) );
    }

    function loadCustomJs () {
        wp_enqueue_script( "EssifLab_ContactForm7-CustomJs", plugin_dir_url( __FILE__ ) . '../js/script.js', array( 'jquery' ), "", false );
    }

    function loadCustomScripts() {
        add_action( 'wp_enqueue_scripts', array( $this , 'loadCustomJs' ) );
    }

    function addActivateHook()
    {
        register_deactivation_hook( __FILE__, array( $this->cf7helper, 'addAllOnActivate' ) );
    }

    function addDeactivateHook()
    {
        register_deactivation_hook( __FILE__, array( $this->cf7helper, 'deleteAllOnDeactivate' ) );
    }
}