<?php

namespace TNO\EssifLab\Tests\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Credential;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Models\ValidationPolicy;
use TNO\EssifLab\Tests\TestCase;

class ValidationPolicyTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new ValidationPolicy();
	}

	/** @test */
	function does_generate_type_name_correctly(): void {
		$actual = $this->subject->getTypeName();

		$expected = 'validation-policy';

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_not_hide_from_nav(): void {
		$actual = $this->subject->getTypeArgs();

		$this->assertIsArray($actual);
		$this->assertFalse($actual[Constants::TYPE_ARG_HIDE_FROM_NAV]);
	}

	/** @test */
	function should_have_attribute_names(): void {
		$actual = $this->subject->getAttributeNames();

		$expected = Constants::TYPE_DEFAULT_ATTRIBUTE_NAMES;

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_have_relations(): void {
		$actual = $this->subject->getRelations();

		$expected = [
			Hook::class,
			Credential::class
		];

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_have_fields(): void {
		$actual = $this->subject->getFields();

		$expected = Constants::TYPE_DEFAULT_FIELDS;

		$this->assertEquals($expected, $actual);
	}
}