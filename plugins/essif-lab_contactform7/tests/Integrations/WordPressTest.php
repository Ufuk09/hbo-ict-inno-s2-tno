<?php

namespace TNO\ContactForm7\Tests\Integrations;

use TNO\ContactForm7\Integrations\WordPress;
use TNO\ContactForm7\Tests\TestCase;
use WP_Post;

class WordPressTest extends TestCase {
    protected function setUp(): void
    {
        parent::setUp();
        $integration = new WordPress($this->application, $this->utility);
        $integration->install();
    }

    /**
     * @test
     */
    function should_insert_hook_when_activated() {
        $hook = $this->utility->getHistoryByFuncName("insertHook");
        self::assertCount(1, $hook);
        $target = $this->utility->getHistoryByFuncName("insertTarget");
        self::assertCount(1, $target);
        $input = $this->utility->getHistoryByFuncName("insertInput");
        self::assertCount(1, $input);
    }

    /**
     * @test
     */
    function should_delete_hook_when_deactivated() {
        $hook = $this->utility->getHistoryByFuncName("deleteHook");
        self::assertCount(1, $hook);
        $target = $this->utility->getHistoryByFuncName("deleteTarget");
        self::assertCount(1, $target);
        $input = $this->utility->getHistoryByFuncName("deleteInput");
        self::assertCount(1, $input);
    }

    /**
     * @test
     */
    function should_select_when_called() {
        $hook = $this->utility->getHistoryByFuncName("selectHook");
        self::assertCount(1, $hook);
        $target = $this->utility->getHistoryByFuncName("selectTarget");
        self::assertCount(1, $target);
        $input = $this->utility->getHistoryByFuncName("selectInput");
        self::assertCount(1, $input);
    }

    /**
     * @test
     */
    function should_select_hook_with_right_parameters() {
        $hook = $this->utility->getHistoryByFuncName("selectHook");
        $entry = current($hook);
        $params = $entry->getParams()[0];
        $expected = ['contact-form-7' => 'Contact Form 7'];
        $this->assertEquals($expected, $params);
    }

    /**
     * @test
     */
    function should_select_target_with_right_parameters() {
        $hook = $this->utility->getHistoryByFuncName("selectTarget");
        $entry = current($hook);
        $params = $entry->getParams()[0];
        $expected = ['contact-form-7' => 'Contact Form 7'];
        $this->assertEquals($expected, $params);
    }

    /**
     * @test
     */
    function should_select_input_with_right_parameters() {

    }

    /**
     * @test
     */
    function should_add_button_when_activated() {
        $button = $this->utility->getHistoryByFuncName("addEssifLabButton");
        self::assertCount(1, $button);
    }

    /**
     * @test
     */
    function should_add_js_when_activated() {
        $button = $this->utility->getHistoryByFuncName("loadCustomJs");
        self::assertCount(1, $button);
    }

}