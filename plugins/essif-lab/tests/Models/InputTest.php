<?php

namespace TNO\EssifLab\Tests\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Input;
use TNO\EssifLab\Tests\TestCase;

class InputTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new Input();
	}

	/** @test */
	function should_hide_from_nav(): void {
		$actual = $this->subject->getTypeArgs();

		$this->assertIsArray($actual);
		$this->assertTrue($actual[Constants::TYPE_ARG_HIDE_FROM_NAV]);
	}

	/** @test */
	function should_have_attribute_names(): void {
		$actual = $this->subject->getAttributeNames();

		$expected = array_merge(Constants::TYPE_DEFAULT_ATTRIBUTE_NAMES, [Constants::TYPE_INSTANCE_SLUG_ATTR]);

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_have_no_relations(): void {
		$actual = $this->subject->getRelations();

		$expected = [];

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_have_fields(): void {
		$actual = $this->subject->getFields();

		$expected = Constants::TYPE_DEFAULT_FIELDS;

		$this->assertEquals($expected, $actual);
	}
}