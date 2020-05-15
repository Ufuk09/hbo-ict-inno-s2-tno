<?php

namespace TNO\EssifLab\Tests\Integrations;

use Closure;
use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\WordPress;
use TNO\EssifLab\Tests\TestCase;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WordPress as WP;

class WordPressTest extends TestCase {
	protected $subject;

	protected function setUp(): void {
		parent::setUp();
		$this->subject = new WordPress($this->application, $this->manager, $this->renderer, $this->utility);
	}

	/** @test */
	function registers_model_type_as_custom_post_type() {
		$this->subject->registerModelType($this->model);

		$history = $this->utility->getHistoryByFuncName(BaseUtility::CREATE_MODEL_TYPE);
		$this->assertNotEmpty($history);
		$this->assertCount(1, $history);
		
		$entry = current($history);
		$this->assertEquals([
			$this->model->getTypeName(),
			array_merge(WordPress::DEFAULT_TYPE_ARGS, [
				'show_in_menu' => $this->application->getNamespace(),
				'supports' => $this->model->getFields(),
				'labels' => WordPress::generateLabels($this->model),
			]),
		], $entry->getParams());
	}

	/** @test */
	function register_model_relations_as_meta_boxes() {
		$this->subject->registerModelRelations($this->model);

		$id = 'model_model';
		$title = 'Models';
		$post_type = 'model';
		$is_closure = function ($x): bool {
			return $x instanceof Closure;
		};

		$history = $this->utility->getHistoryByFuncName(WP::ADD_META_BOX);
		$this->assertNotEmpty($history);
		$this->assertCount(1, $history);
		
		$entry = current($history);
		$params = $entry->getParams();
		// $id parameter is equal to:
		$this->assertEquals($id, $params[0]);
		// $title parameter is equal to:
		$this->assertEquals($title, $params[1]);
		// $callback is a closure:
		$this->assertTrue($is_closure($params[2]));
		// $screen parameter is equal to:
		$this->assertEquals($post_type, $params[3]);
	}

	/** @test */
	function registers_models_when_running_install() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(BaseUtility::CREATE_MODEL_TYPE);
		$this->assertNotEmpty($history);
		/**
		 * Types
		 * 1. Credential
		 * 2. Hook
		 * 3. Input
		 * 4. Issuer
		 * 5. Schema
		 * 6. Target
		 * 7. ValidationPolicy
		 */
		$this->assertCount(7, $history);
	}

	/** @test */
	function registers_to_hide_nav_for_certain_models() {
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
	function registers_model_relations_when_running_install() {
		$this->subject->install();

		$history = $this->utility->getHistoryByFuncName(WP::ADD_META_BOX);
		/**
		 * Relations
		 * - Credential:
		 *   1. Input
		 *   2. Issuer
		 *   3. Schema
		 * - Hook:
		 *   4. Target
		 * - ValidationPolicy:
		 *   5. Hook
		 *   6. Credential
		 */
		$this->assertCount(6, $history);
	}

	/** @test */
	function registers_menu_item_when_installing() {
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
    function can_get_form_items_from_each_model_relation() {
        $this->subject->install();

        $renderWasCalled = $this->renderer->isRenderListAndFormViewCalled();
        $this->assertTrue($renderWasCalled);

        $attrs = $this->renderer->getAttrsParamWhereRenderListAndFormViewWasCalledWith();

        $this->assertNotEmpty($attrs);
        $this->assertNotEmpty($attrs[0]->getValue());

        // ID of the model
        $this->assertEquals(1, $attrs[0]->getValue()[0]->getValue());
        // Title of the model
        $this->assertEquals('hello', $attrs[0]->getValue()[0]->getLabel());
    }

    /** @test */
    function can_get_list_items_from_each_model_relation() {
        $this->subject->install();

        $renderWasCalled = $this->renderer->isRenderListAndFormViewCalled();
        $this->assertTrue($renderWasCalled);

        $attrs = $this->renderer->getAttrsParamWhereRenderListAndFormViewWasCalledWith();

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
}