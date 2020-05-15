<?php

namespace TNO\EssifLab\Tests\ModelManager;

use TNO\EssifLab\Constants;
use TNO\EssifLab\ModelManagers\WordPressPostTypes;
use TNO\EssifLab\Tests\Stubs\Model;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;

class WordPressPostTypesTest extends TestCase {
	/** @test */
	function uses_id_of_current_model_if_attribute_is_missing_and_same_type() {
		$subject = new WordPressPostTypes($this->application, $this->utility);
		$modelWithoutId = new Model();

		$result = $subject->selectAllRelations($modelWithoutId, $modelWithoutId);

		$history = $this->utility->getHistoryByFuncName(BaseUtility::GET_CURRENT_MODEL);
		$this->assertNotEmpty($history);
		$this->assertCount(1, $history);

		$instance = current($result);
		$attrs = $instance->getAttributes();
		$this->assertEquals('hello', $attrs[Constants::TYPE_INSTANCE_TITLE_ATTR]);
		$this->assertEquals('world', $attrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR]);
	}
}