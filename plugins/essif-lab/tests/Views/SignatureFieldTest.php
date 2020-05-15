<?php

namespace TNO\EssifLab\Tests\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Tests\Stubs\Model;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Views\SignatureField;

class SignatureFieldTest extends TestCase {
	/** @test */
	function does_render_input() {
		$subject = new SignatureField($this->integration, $this->model);

		$actual = $subject->render();
		$expect = '/<input.*\/>/';

		$this->assertRegExp($expect, $actual);
	}

	/** @test */
	function does_render_with_name_attr() {
		$subject = new SignatureField($this->integration, $this->model);

		$actual = $subject->render();
		$expect = '/name="namespace\[signature]"/';

		$this->assertRegExp($expect, $actual);
	}

	/** @test */
	function does_render_with_signature_value() {
		$subject = new SignatureField($this->integration, new Model([
			Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => json_encode([
				Constants::FIELD_TYPE_SIGNATURE => 'hello world',
			]),
		]));

		$actual = $subject->render();
		$expect = '/value="hello world"/';

		$this->assertRegExp($expect, $actual);
	}
}