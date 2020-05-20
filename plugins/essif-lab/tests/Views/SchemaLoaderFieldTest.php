<?php

namespace TNO\EssifLab\Tests\Views;

use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Views\SchemaLoaderField;

class SchemaLoaderFieldTest extends TestCase {
	/** @test */
	function does_render_with_name_attr() {
		$subject = new SchemaLoaderField($this->integration, $this->model);

		$actual = $subject->render();
		$expect = '/name="namespace\[schema_loader]/';

		$this->assertRegExp($expect, $actual);
	}

	/** @test */
	function does_render_with_json_schema_from_url_option() {
		$subject = new SchemaLoaderField($this->integration, $this->model);

		$actual = $subject->render();
		$expect = '/json.*schema.*url/i';

		$this->assertRegExp($expect, $actual);
	}
	/** @test */
	function does_render_with_json_schema_from_url_input() {
		$subject = new SchemaLoaderField($this->integration, $this->model);

		$actual = $subject->render();
		$expect = '/<input.*[schema_loader][target].*\/>/';

		$this->assertRegExp($expect, $actual);
	}
}