<?php

namespace TNO\EssifLab\Tests\Integrations;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\WordPress;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Tests\Stubs\ModelManager;
use TNO\EssifLab\Tests\Stubs\ModelRenderer;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WP;

class WordPressTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new WordPress($this->application, $this->manager, $this->renderer, $this->utility);
	}

	/** @test */
	function installs_the_admin_menu_item() {
		$this->subject->install();

		$title = $this->application->getName();
		$capability = Constants::ADMIN_MENU_CAPABILITY;
		$menu_slug = $this->application->getNamespace();
		$icon_url = Constants::ADMIN_MENU_ICON_URL;

		$history = $this->utility->getHistoryByFuncName(WP::ADD_NAV_ITEM);
		$this->assertNotEmpty($history);
		$this->assertCount(1, $history);

		$entry = current($history);
		$params = $entry->getParams();

		$this->assertEquals($title, $params[0]);
		$this->assertEquals($capability, $params[1]);
		$this->assertEquals($menu_slug, $params[2]);
		$this->assertEquals($icon_url, $params[3]);
	}

	/** @test */
	function installs_the_admin_menu_item_and_adds_non_hidden_model_types() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(BaseUtility::CREATE_MODEL_TYPE);
		$hook = $history[1];
		$target = $history[5];
		$input = $history[2];

		$this->assertFalse($hook->getParams()[1]['show_ui']);
		$this->assertFalse($target->getParams()[1]['show_ui']);
		$this->assertFalse($input->getParams()[1]['show_ui']);
	}

	/** @test */
	function installs_all_model_types() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(BaseUtility::CREATE_MODEL_TYPE);
		$this->assertNotEmpty($history);
		$this->assertCount(7, $history);
	}

	/** @test */
	function installs_all_model_types_their_save_handlers() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(WP::ADD_ACTION);
		$this->assertNotEmpty($history);

		$fields = array_filter($history, function ($entry) {
			$hookName = $entry->getParams()[0];

			return strpos($hookName, 'save_post') !== false;
		});
		$this->assertCount(7, $fields);
	}

    /** @test */
    function installs_all_model_types_their_delete_handlers() {
        $this->subject->install();

        $history = $this->utility->getHistoryByFuncName(WP::ADD_ACTION);
        $this->assertNotEmpty($history);

        $fields = array_filter($history, function ($entry) {
            $hookName = $entry->getParams()[0];

            return strpos($hookName, 'delete_post') !== false;
        });
        $this->assertCount(7, $fields);
    }

	/** @test */
	function installs_all_model_types_their_save_handlers_and_removes_all_actions_before_updating() {
		$_POST['namespace'][Constants::FIELD_TYPE_SIGNATURE] = 'hello';
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(WP::REMOVE_ALL_ACTIONS_AND_EXEC);
		$this->assertNotEmpty($history);
	}

    /** @test */
    function installs_all_model_types_their_delete_handlers_and_removes_all_actions_before_updating() {
        $_POST['namespace'][Constants::FIELD_TYPE_SIGNATURE] = 'hello';
        $this->subject->install();

        $history = $this->utility->getHistoryByFuncName(WP::REMOVE_ALL_ACTIONS_AND_EXEC);
        $this->assertNotEmpty($history);
    }

    /** @test */
    function installs_all_model_types_their_save_handlers_and_adds_a_relation() {
        $id = 5;
        $this->create_a_relation($id);

        $managerWasCalled = $this->manager->isCalled(ModelManager::MODEL_MANAGER);
        $this->assertTrue($managerWasCalled);

        $modelFrom = $this->manager->getModel1ItsCalledWith(ModelManager::MODEL_MANAGER);
        $this->assertNotEmpty($modelFrom);

        $modelTo = $this->manager->getModel2ItsCalledWith(ModelManager::MODEL_MANAGER);
        $this->assertNotEmpty($modelTo);

        $modelIds = [$modelFrom->getAttributes()[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR], $modelTo->getAttributes()[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR]];
        $this->assertContains(5, $modelIds);

        $relations = $this->manager->selectAllRelations($modelFrom, $modelTo);
        $result = false;
        foreach ($relations as $model){
            if ($model == $modelTo) $result = true;
        }
        $this->assertTrue($result);
    }

    /** @test */
    function installs_all_model_types_their_save_handlers_and_adds_a_relation_and_then_removes_it() {
        $id = 5;
        $this->create_a_relation($id);
        $_POST['namespace'] = [];
        $_POST['namespace'][Constants::ACTION_NAME_REMOVE_RELATION] = $id;
        $this->subject->install();

        $managerWasCalled = $this->manager->isCalled(ModelManager::MODEL_MANAGER);
        $this->assertTrue($managerWasCalled);

        $model1 = $this->manager->getModel1ItsCalledWith(ModelManager::MODEL_MANAGER);
        $this->assertNotEmpty($model1);

        $model2 = $this->manager->getModel2ItsCalledWith(ModelManager::MODEL_MANAGER);
        $this->assertNotEmpty($model2);

        $from = $this->utility->call(BaseUtility::GET_CURRENT_MODEL);
        $to = $this->utility->call(BaseUtility::GET_MODEL, $id);
        $relations = $this->manager->selectAllRelations($from, $to);
        foreach ($relations as $model){
            $this->assertFalse($model == $to);
        }
    }

	/** @test */
	function installs_the_relation_components_for_the_model_currently_being_viewed() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(WP::ADD_META_BOX);
		$this->assertNotEmpty($history);;

		$relations = array_filter($history, function ($entry) {
			$id = $entry->getParams()[0];

			return strpos($id, '_relation_') !== false;
		});
		$this->assertCount(1, $relations);

		$entry = current($relations);
		$title = $entry->getParams()[1];
		$this->assertEquals('Models', $title);
	}

	/** @test */
	function installs_the_relation_components_with_relation_specific_form_items() {
		$this->subject->install();

		$renderWasCalled = $this->renderer->isCalled(ModelRenderer::LIST_AND_FORM_VIEW_RENDERER);
		$this->assertTrue($renderWasCalled);

		$attrs = $this->renderer->getAttrsItsCalledWith(ModelRenderer::LIST_AND_FORM_VIEW_RENDERER);
		$this->assertNotEmpty($attrs);
		$this->assertNotEmpty($attrs[0]->getValue());

		// ID of the model
		$this->assertEquals(1, $attrs[0]->getValue()[0]->getValue());
		// Title of the model
		$this->assertEquals('hello', $attrs[0]->getValue()[0]->getLabel());
	}

	/** @test */
	function installs_the_relation_components_with_relation_specific_list_items() {
		$this->subject->install();

		$renderWasCalled = $this->renderer->isCalled(ModelRenderer::LIST_AND_FORM_VIEW_RENDERER);
		$this->assertTrue($renderWasCalled);

		$attrs = $this->renderer->getAttrsItsCalledWith(ModelRenderer::LIST_AND_FORM_VIEW_RENDERER);
		$this->assertNotEmpty($attrs);
		$this->assertNotEmpty($attrs[1]->getValue());
		$this->assertNotEmpty($attrs[1]->getValue()[0]->getValue());

		//ID of the model
		$this->assertEquals(1, $attrs[1]->getValue()[0]->getValue()[0]->getValue());
		//Title of the model
		$this->assertEquals('hello', $attrs[1]->getValue()[0]->getValue()[0]->getLabel());
		//Description of the model
		$this->assertEquals('world', $attrs[1]->getValue()[0]->getValue()[1]->getLabel());
	}

	/** @test */
	function installs_the_field_components_for_the_model_currently_being_viewed() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(WP::ADD_META_BOX);
		$this->assertNotEmpty($history);;

		$relations = array_filter($history, function ($entry) {
			$id = $entry->getParams()[0];

			return strpos($id, '_field_') !== false;
		});
		$this->assertCount(3, $relations);

		$entry = current($relations);
		$title = $entry->getParams()[1];
		$this->assertEquals('Signature', $title);
	}

    private function create_a_relation($id): void {
        $hook = new Hook([Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => $id]);
        $_POST['namespace'] = [];
        $_POST['namespace'][Constants::ACTION_NAME_ADD_RELATION] = ['hook' => $hook->getAttributes()[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR]];
        $_POST['namespace'][Constants::ACTION_NAME_RELATION_ACTION] = 'hook';
        $this->subject->install();
    }
}