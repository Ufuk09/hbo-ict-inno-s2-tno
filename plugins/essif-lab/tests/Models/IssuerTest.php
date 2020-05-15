<?php

namespace TNO\EssifLab\Tests\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Issuer;
use TNO\EssifLab\Tests\TestCase;

class IssuerTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new Issuer();
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
	function should_have_no_relations(): void {
		$actual = $this->subject->getRelations();

		$expected = [];

		$this->assertEquals($expected, $actual);
	}

	/** @test */
	function should_have_fields(): void {
		$actual = $this->subject->getFields();

		$expected = array_merge(Constants::TYPE_DEFAULT_FIELDS, [
			Constants::FIELD_TYPE_SIGNATURE,
		]);

		$this->assertEquals($expected, $actual);
	}
}