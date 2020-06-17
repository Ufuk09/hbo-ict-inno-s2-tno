<?php

namespace TNO\EssifLab\Tests\ModelManager;

use TNO\EssifLab\Constants;
use TNO\EssifLab\ModelManagers\WordPressPostTypes;
use TNO\EssifLab\Tests\Stubs\Model;
use TNO\EssifLab\Tests\Stubs\Utility;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;

class WordPressPostTypesTest extends TestCase {
    protected $subject;

    protected function setUp(): void {
        parent::setUp();
        $this->subject = new WordPressPostTypes($this->application, $this->utility);
    }

	/** @test */
	function uses_id_of_current_model_if_attribute_is_missing_and_same_type() {
		$modelWithoutId = new Model();

		$result = $this->subject->selectAllRelations($modelWithoutId, $modelWithoutId);

		$history = $this->utility->getHistoryByFuncName(BaseUtility::GET_CURRENT_MODEL);
		$this->assertNotEmpty($history);
		$this->assertCount(1, $history);

		$instance = current($result);
		$attrs = $instance->getAttributes();
		$this->assertEquals('hello', $attrs[Constants::TYPE_INSTANCE_TITLE_ATTR]);
		$this->assertEquals('world', $attrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR]);
	}

    /** @test */
    function inserts_immutable_true_and_retrieves_it(){
        $this->utility->clearMeta();
        $model = Utility::createModelWithId(1);

        $this->subject->saveImmutable($model, true);

        $this->assertTrue($this->subject->getImmutable($model));
    }

    /** @test */
    function inserts_immutable_false_and_retrieves_it(){
        $this->utility->clearMeta();
        $model = Utility::createModelWithId(1);

        $this->subject->saveImmutable($model, false);

        $this->assertFalse($this->subject->getImmutable($model));
    }

    /** @test */
    function inserts_model_and_its_relations(){
        $this->utility->clearMeta();
        $models = $this->insertModelWith2Relations();

        $relations1 = $this->subject->selectAllRelations($models["mainModel"], $models["subModel1"]);
        $contains = false;
        foreach ($relations1 as $model){
            if($model == $models["subModel1"]){
                $contains = true;
            }
        }
        $this->assertTrue($contains);

        $relations2 = $this->subject->selectAllRelations($models["mainModel"], $models["subModel2"]);
        $contains = false;
        foreach ($relations2 as $model){
            if($model == $models["subModel2"]){
                $contains = true;
            }
        }
        $this->assertTrue($contains);
    }

	/** @test */
    function inserts_model_and_its_relations_and_then_deletes_it_and_its_relations(){
        $this->utility->clearMeta();
        $models = $this->insertModelWith2Relations();

        $this->subject->delete($models["mainModel"]);

        $relations1 = $this->subject->selectAllRelations($models["mainModel"], $models["subModel1"]);
        $contains = false;
        foreach ($relations1 as $model){
            if($model == $models["subModel1"]){
                $contains = true;
            }
        }
        $this->assertFalse($contains);

        $relations2 = $this->subject->selectAllRelations($models["mainModel"], $models["subModel2"]);
        $contains = false;
        foreach ($relations2 as $model){
            if($model == $models["subModel2"]){
                $contains = true;
            }
        }
        $this->assertFalse($contains);
    }

    private function insertModelWith2Relations(): array{
        $mainModel = Utility::createModelWithId(2);
        $subModel1 = Utility::createModelWithId(3);
        $subModel2 = Utility::createModelWithId(4);
        $this->subject->insertRelation($mainModel, $subModel1);
        $this->subject->insertRelation($mainModel, $subModel2);
        return ["mainModel" => $mainModel, "subModel1" => $subModel1, "subModel2" => $subModel2];
    }
}