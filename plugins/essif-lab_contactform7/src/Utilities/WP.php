<?php

namespace TNO\ContactForm7\Utilities;

use TNO\ContactForm7\Utilities\Contracts\BaseUtility;
use TNO\ContactForm7\Views\Button;
use TNO\ContactForm7\Utilities\Helpers\CF7Helper;

class WP extends BaseUtility
{
    CONST ACTION_PREFIX = "essif-lab_";

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
        $this->select("hook", [$slug => $title]);
    }

    function selectTarget(array $items = [], string $hookSlug = self::SLUG)
    {
        $this->select("target", $items, $hookSlug);
    }

    function selectInput(array $items = [], string $hookSlug = self::SLUG)
    {
        $this->select("input", $items, $hookSlug);
    }

    private function select($suffix, ...$params)
    {
        do_action(self::ACTION_PREFIX . "select_" . $suffix, ... $params);
    }

    function addEssifLabButton () {
        $view = new Button();
        add_action('wpcf7_init', array( $view, 'custom_add_form_tag_essif_lab' ) );
    }

    function loadCustomJs () {
        wp_enqueue_script( "EssifLab_ContactForm7-CustomJs", plugin_dir_url( __FILE__ ) . '../js/script.js', array( 'jquery' ), "", false );
    }

    function loadCustomScripts() {
        add_action( 'wp_enqueue_scripts', array( $this , 'loadCustomJs' ) );
    }

    function addActivateHook()
    {
        $cf7helper = new CF7Helper();
        register_deactivation_hook( __FILE__, array( $cf7helper, 'addAllOnActivate' ) );
    }

    function addDeactivateHook()
    {
        $cf7helper = new CF7Helper();
        register_deactivation_hook( __FILE__, array( $cf7helper, 'deleteAllOnDeactivate' ) );
    }
}