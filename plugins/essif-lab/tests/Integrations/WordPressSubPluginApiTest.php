<?php

namespace TNO\EssifLab\Tests\Integrations;

use TNO\EssifLab\Integrations\WordPressSubPluginApi;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Models\Input;
use TNO\EssifLab\Models\Target;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\WP;

class WordPressSubPluginApiTest extends TestCase {
	const TRIGGER_INSERT_PRE = WordPressSubPluginApi::TRIGGER_PRE.'insert_';

	const TRIGGER_DELETE_PRE = WordPressSubPluginApi::TRIGGER_PRE.'delete_';

	const TRIGGER_SELECT_PRE = WordPressSubPluginApi::TRIGGER_PRE.'select_';

	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new WordPressSubPluginApi($this->application, $this->manager, $this->renderer, $this->utility);
		$this->subject->install();
	}

	function provider() {
		return [
			'Hook' => [
				'hook',
				function ($value) {
					return $value instanceof Hook;
				},
			],
			'Target' => [
				'target',
				function ($value) {
					return $value instanceof Target;
				},
			],
			'Input' => [
				'input',
				function ($value) {
					return $value instanceof Input;
				},
			],
		];
	}

	/**
	 * @test
	 * @dataProvider provider
	 * @param string $subject
	 * @param callable $instanceOf
	 */
	function adds_insert_action(string $subject, callable $instanceOf) {
		$this->assertAddedActionCount(1, self::TRIGGER_INSERT_PRE.$subject);
		$this->assertManagerExecutions('insert', $instanceOf);
	}

	/**
	 * @test
	 * @dataProvider provider
	 * @param string $subject
	 * @param callable $instanceOf
	 */
	function adds_delete_action(string $subject, callable $instanceOf) {
		$this->assertAddedActionCount(1, self::TRIGGER_DELETE_PRE.$subject);
		$this->assertManagerExecutions('delete', $instanceOf);
	}

	/**
	 * @test
	 * @dataProvider provider
	 * @param string $subject
	 * @param callable $instanceOf
	 */
	function adds_select_filter(string $subject, callable $instanceOf) {
		$this->assertAddedFilterCount(1, self::TRIGGER_SELECT_PRE.$subject);
		if ($subject === 'hook') {
			$this->assertManagerExecutions('select', $instanceOf);
		} else {
			$this->assertManagerExecutions('selectAllRelations', $instanceOf, 1);
		}
	}

	private function assertAddedActionCount(int $expected, string $triggerName) {
		$actions = $this->utility->getHistoryByFuncName(WP::ADD_ACTION);
		$filteredActions = array_filter($actions, function ($history) use ($triggerName) {
			return $history->getParams()[0] === $triggerName;
		});

		$this->assertCount($expected, $filteredActions);
	}

	private function assertAddedFilterCount(int $expected, string $triggerName) {
		$actions = $this->utility->getHistoryByFuncName(WP::ADD_FILTER);
		$filteredActions = array_filter($actions, function ($history) use ($triggerName) {
			return $history->getParams()[0] === $triggerName;
		});

		$this->assertCount($expected, $filteredActions);
	}

	private function assertManagerExecutions(string $funcName, callable $with, int $param = 0) {
		$executions = $this->manager->getHistoryByFuncName($funcName);
		$filteredExecutions = array_filter($executions, function ($history) use ($with, $param) {
			return $with($history->getParams()[$param]);
		});

		$this->assertNotEmpty($filteredExecutions);
	}
}