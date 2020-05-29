<?php

namespace TNO\EssifLab\Tests\Integrations;

use TNO\EssifLab\Integrations\WordPressSubPluginApi;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Tests\TestCase;

class WordPressSubPluginApiTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new WordPressSubPluginApi($this->application, $this->manager, $this->renderer, $this->utility);
		$this->subject->install();
	}

	/** @test */
	function does_insert_hook_via_custom_action() {
		$history = $this->utility->getHistoryByFuncName('add_action');
		$insert_hook = array_filter($history, function ($h) {
			return $h->getParams()[0] === 'essif-lab_insert_hook';
		});

		$this->assertCount(1, $insert_hook);
	}

	/** @test */
	function does_insert_hook_via_custom_action_and_inserts() {
		$this->does_insert_hook_via_custom_action();

		$inserts = $this->manager->getHistoryByFuncName('insert');
		$this->assertCount(1, $inserts);

		$hook = $inserts[0]->getParams()[0];
		$this->assertTrue($hook instanceof Hook);
	}

	/** @test */
	function does_delete_hook_via_custom_action() {
		$history = $this->utility->getHistoryByFuncName('add_action');
		$insert_hook = array_filter($history, function ($h) {
			return $h->getParams()[0] === 'essif-lab_delete_hook';
		});

		$this->assertCount(1, $insert_hook);
	}

	/** @test */
	function does_delete_hook_via_custom_action_and_deletes() {
		$this->does_delete_hook_via_custom_action();

		$deletes = $this->manager->getHistoryByFuncName('delete');
		$this->assertCount(1, $deletes);
	}
}