<?php

namespace TNO\ContactForm7\Utilities;

use TNO\ContactForm7\Utilities\Contracts\BaseUtility;
use TNO\ContactForm7\Utilities\Helpers\CF7Helper;
use TNO\ContactForm7\Views\Button;

class WP extends BaseUtility
{
    private const TARGET = "target";
    private const INPUT = "input";
    private $cf7helper;

    public function __construct(CF7Helper $cf7helper)
    {
        $this->cf7helper = $cf7helper;
    }

    CONST ACTION_PREFIX = "essif-lab_";

    function getAllForms()
    {
        $args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
        return get_posts($args);
    }

    function getTargetsFromForms(array $cf7Forms, string $post_title, string $id)
    {
        return wp_list_pluck($cf7Forms, 'post_title', 'ID');
    }

    function insertHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->insert("hook", [$slug => $title]);
    }

    function insertTarget(int $id, string $title, string $hookSlug = self::SLUG)
    {
        $this->insert(self::TARGET, [$id => $title], $hookSlug);
    }

    function insertInput(string $slug, string $title, int $targetId)
    {
        $this->insert(self::INPUT, [$slug => $title], $targetId);
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
        $this->delete(self::TARGET, [$id => $title], $hookSlug);
    }

    function deleteInput(string $slug, string $title, int $targetId)
    {
        $this->delete(self::INPUT, [$slug => $title], $targetId);
    }

    private function delete($suffix, ...$params)
    {
        do_action(self::ACTION_PREFIX . "delete_" . $suffix, ... $params);
    }

    function selectHook(string $slug = self::SLUG, string $title = self::TITLE) : array  {
        return $this->select("hook", [$slug => $title]);
    }

    function selectTarget(array $items = [], string $hookSlug = self::SLUG) : array  {
        return $this->select(self::TARGET, $items, $hookSlug);
    }

    function selectInput(array $items = [], string $hookSlug = self::SLUG) : array {
        return $this->select(self::INPUT, $items, $hookSlug);
    }

    private function select($suffix, ...$params) : array {
        return apply_filters(self::ACTION_PREFIX . "select_" . $suffix, ... $params);
    }

    function addEssifLabButton () {
        add_action('wpcf7_init', array( $this, 'addFormTag' ) );
    }

    function addFormTag()
    {
        wpcf7_add_form_tag('essif_lab', array ( 'Button', 'custom_essif_lab_form_tag_handler' ) );
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