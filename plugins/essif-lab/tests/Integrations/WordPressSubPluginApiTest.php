<?php

namespace TNO\EssifLab\Tests\Integrations;

use TNO\EssifLab\Integrations\WordPressSubPluginApi;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\WP;

class WordPressSubPluginApiTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new WordPressSubPluginApi($this->application, $this->manager, $this->renderer, $this->utility);
		$this->subject->install();
	}

	/** @test */
	function does_add_action_to_insert_hook() {
		$history = $this->utility->getHistoryByFuncName(WP::ADD_ACTION);
		$insert_hook = array_filter($history, function ($h) {
			return $h->getParams()[0] === WordPressSubPluginApi::TRIGGER_INSERT_PRE.'hook';
		});

		$this->assertCount(1, $insert_hook);
	}

	/** @test */
	function does_add_action_to_insert_hook_and_inserts() {
		$this->does_add_action_to_insert_hook();

		$inserts = $this->manager->getHistoryByFuncName('insert');
		$this->assertCount(1, $inserts);

		$hook = $inserts[0]->getParams()[0];
		$this->assertTrue($hook instanceof Hook);
	}

	/** @test */
	function does_add_action_to_delete_hook() {
		$history = $this->utility->getHistoryByFuncName(WP::ADD_ACTION);
		$insert_hook = array_filter($history, function ($h) {
			return $h->getParams()[0] === WordPressSubPluginApi::TRIGGER_DELETE_PRE.'hook';
		});

		$this->assertCount(1, $insert_hook);
	}

	/** @test */
	function does_add_action_to_delete_hook_and_deletes() {
		$this->does_add_action_to_delete_hook();

		$deletes = $this->manager->getHistoryByFuncName('delete');
		$this->assertCount(1, $deletes);
	}

	/** @test */
	function does_add_filter_to_select_hooks() {
		$history = $this->utility->getHistoryByFuncName(WP::ADD_FILTER);
		$insert_hook = array_filter($history, function ($h) {
			return $h->getParams()[0] === WordPressSubPluginApi::TRIGGER_SELECT_PRE.'hook';
		});

		$this->assertCount(1, $insert_hook);
	}

	/** @test */
	function does_add_filter_to_select_hooks_and_selects() {
		$this->does_add_filter_to_select_hooks();

		$selects = $this->manager->getHistoryByFuncName('select');

		$this->assertNotEmpty($selects);
	}
}