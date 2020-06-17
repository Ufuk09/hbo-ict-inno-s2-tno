<?php

namespace TNO\ContactForm7\Tests\Integrations;

use TNO\ContactForm7\Integrations\WordPress;
use TNO\ContactForm7\Tests\TestCase;

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
    function should_call_get_posts() {
        $hook = $this->utility->getHistoryByFuncName("getAllForms");
        self::assertCount(1, $hook);
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
        $target = $this->utility->getHistoryByFuncName("selectTarget");
        $entry = current($target);
        $params = $entry->getParams()[2];
        $expected = [
            12 => "Contactform 1",
            13 => "Contactform 2",
            14 => "Contactform 3",
            15 => "Contactform 4",
            16 => "Contactform 5"
        ];
        $this->assertEquals($expected, $params);
    }

    /**
     * @test
     */
    function should_select_input_with_right_parameters() {
        $input = $this->utility->getHistoryByFuncName("selectInput");
        $entry = current($input);
        $params = $entry->getParams()[2];
        $expected = [
            12 => "Contactform 1",
            ["my-email" => "Email",
                "my-name" => "Name",
                "my-message" => "Message"]
        ];
        $this->assertEquals($expected, $params);
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
    function should_load_scripts_when_activated() {
        $button = $this->utility->getHistoryByFuncName("loadCustomScripts");
        self::assertCount(1, $button);
    }

    /**
     * @test
     */
    function should_add_activate_hook_when_activated() {
        $button = $this->utility->getHistoryByFuncName("addActivateHook");
        self::assertCount(1, $button);
    }

    /**
     * @test
     */
    function should_add_deactivate_hook_when_activated() {
        $button = $this->utility->getHistoryByFuncName("addDeactivateHook");
        self::assertCount(1, $button);
    }


}