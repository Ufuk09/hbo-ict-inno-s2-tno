<?php

namespace TNO\EssifLab\Integrations;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\ModelManagers\WordPressPostTypes;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WP;
use TNO\EssifLab\Views\Items\Displayable;
use TNO\EssifLab\Views\Items\MultiDimensional;
use TNO\EssifLab\Views\TypeList;

class WordPress extends BaseIntegration {
	const DEFAULT_TYPE_ARGS = [
		'public' => false,
		'show_ui' => true,
	];

	function install(): void {
		$this->configureAllMiscellaneous();
		$this->configureAllModelsAvailable();
		$this->configureModelCurrentlyBeingViewed();
	}

	private function configureAllMiscellaneous(): void {
		$this->utility->call(WP::ADD_ACTION, 'admin_menu', function () {
			$title = $this->application->getName();
			$capability = Constants::ADMIN_MENU_CAPABILITY;
			$slug = $this->application->getNamespace();
			$icon = Constants::ADMIN_MENU_ICON_URL;
			$this->utility->call(WP::ADD_NAV_ITEM, $title, $capability, $slug, $icon);
		});
	}

	private function configureAllModelsAvailable(): void {
		BaseIntegration::forAllModels(function (Model $model) {
			$this->registerModelType($model);
			$this->registerModelSaveHandler($model);
		});
	}

	private function configureModelCurrentlyBeingViewed(): void {
		$model = $this->utility->call(BaseUtility::GET_CURRENT_MODEL);
		if (! empty($model)) {
			$this->registerModelComponents($model);
		}
	}

	private function registerModelType(Model $model): void {
		$this->utility->call(WP::ADD_ACTION, 'init', function () use ($model) {
			$args = $this->generateTypeArgs($model);
			$this->utility->call(BaseUtility::CREATE_MODEL_TYPE, $model->getTypeName(), $args);
		});
	}

	private function registerModelSaveHandler(Model $model): void {
		$hook = 'save_post_'.$model->getTypeName();
		$this->utility->call(WP::ADD_ACTION, $hook, function ($_, $post) use ($hook) {
			$namespace = $this->application->getNamespace();
			if (array_key_exists($namespace, $_POST)) {
                $namespace_data = $_POST[$namespace];
                if (array_key_exists(Constants::FIELD_TYPE_SIGNATURE, $namespace_data)){
                    $newData = $this->prepareModelSaveData($namespace_data, $post);
                    $this->utility->call(WP::REMOVE_ALL_ACTIONS_AND_EXEC, $hook, function () use ($post, $newData) {
                        $this->executeModelSave($post, $newData);
                    });
                } elseif (array_key_exists(Constants::ACTION_NAME_ADD_RELATION, $namespace_data)){
			        $relation_post_type = $namespace_data[Constants::ACTION_NAME_RELATION_ACTION];
			        $this->manager->insertRelation(WP::getCurrentModel(), WP::getModel($namespace_data[Constants::ACTION_NAME_ADD_RELATION][$relation_post_type]));
                }
			}
		}, 10, 2);
	}

	private function prepareModelSaveData($data, $post): array {
		$content = is_object($post) && property_exists($post, 'post_content') ? $post->post_content : '';
		$old = json_decode($content, true);
		$new = is_array($old) ? $old : [];
		return $this->parseFieldSignature($data, $new);
	}

	private function executeModelSave($post, array $new): void {
		if (! empty($new)) {
			$post->post_content = json_encode($new);
			$this->utility->call(BaseUtility::UPDATE_MODEL, $post);
		}
	}

	private function registerModelComponents(Model $model): void {
		$hook = 'add_meta_boxes_'.$model->getTypeName();
		$this->utility->call(WP::ADD_ACTION, $hook, function () use ($model) {
			$this->registerModelFields($model);
			$this->registerModelRelations($model);
		});
	}

	private function registerModelFields(Model $model): void {
		$fields = $model->getFields();
		$screen = $model->getTypeName();
		foreach ($fields as $field) {
			$output = $this->renderModelField($field, $model);
			if (! empty($output)) {
				$id = $screen.'_field_'.$field;
				$title = ucfirst($field);
				$callback = function () use ($output) {
					print $output;
				};
				$this->utility->call(WP::ADD_META_BOX, $id, $title, $callback, $screen);
			}
		}
	}

	private function renderModelField(string $field, Model $model): string {
		switch ($field) {
			case Constants::FIELD_TYPE_SIGNATURE:
				return $this->renderer->renderFieldSignature($this, $model);

			default:
				return '';
		}
	}

	private function registerModelRelations(Model $model): void {
		$classes = $model->getRelations();
		$screen = $model->getTypeName();
		BaseIntegration::forEachModel($classes, function (Model $related) use ($model, $screen) {
			$output = $this->renderModelRelation($model, $related);
			if (! empty($output)) {
				$id = $screen.'_relation_'.$related->getTypeName();
				$title = self::toTitleCase($related->getPluralName());
				$callback = function () use ($output) {
					print $output;
				};
				$this->utility->call(WP::ADD_META_BOX, $id, $title, $callback, $screen);
			}
		});
	}

	private function renderModelRelation(Model $parent, Model $related): string {
		$formItems = $this->generateFormItems($related);
		$listItems = $this->generateListItems($parent);
		$values = [
			new MultiDimensional($formItems, TypeList::FORM_ITEMS),
			new MultiDimensional($listItems, TypeList::LIST_ITEMS),
		];

		return $this->renderer->renderListAndFormView($this, $related, $values);
	}

	private static function generateLabels(Model $model): array {
		$singular = $model->getSingularName();
		$singularTitleCase = self::toTitleCase($singular);
		$plural = $model->getPluralName();
		$pluralTitleCase = self::toTitleCase($plural);

		return [
			'name' => $pluralTitleCase,
			'singular_name' => $singularTitleCase,
			'menu_name' => $pluralTitleCase,
			'name_admin_bar' => $singularTitleCase,
			'archives' => sprintf('%s Archives', $singularTitleCase),
			'attributes' => sprintf('%s Attributes', $singularTitleCase),
			'parent_item_colon' => sprintf('Parent %s:', $singularTitleCase),
			'all_items' => $pluralTitleCase,
			'add_new_item' => sprintf('Add New %s', $singularTitleCase),
			'new_item' => sprintf('New %s', $singularTitleCase),
			'edit_item' => sprintf('Edit %s', $singularTitleCase),
			'update_item' => sprintf('Update %s', $singularTitleCase),
			'view_item' => sprintf('View %s', $singularTitleCase),
			'view_items' => sprintf('View %s', $pluralTitleCase),
			'search_items' => sprintf('Search %s', $pluralTitleCase),
			'not_found' => sprintf('No %s found', $plural),
			'not_found_in_trash' => sprintf('Not %s found in Trash', $plural),
			'insert_into_item' => sprintf('Insert into %s', $singular),
			'uploaded_to_this_item' => sprintf('Uploaded to this %s', $singular),
			'items_list' => sprintf('%s list', $pluralTitleCase),
			'items_list_navigation' => sprintf('%s list navigation', $pluralTitleCase),
			'filter_items_list' => sprintf('Filter %s list', $plural),
		];
	}

	private static function toTitleCase(string $v): string {
		return implode(' ', array_map('ucfirst', explode(' ', $v)));
	}

	private function generateTypeArgs(Model $model): array {
		$default = array_merge(self::DEFAULT_TYPE_ARGS, [
			'labels' => self::generateLabels($model),
			'show_in_menu' => $this->application->getNamespace(),
			'supports' => $model->getFields(),
		]);

		$args = $model->getTypeArgs();
		if (array_key_exists(Constants::TYPE_ARG_HIDE_FROM_NAV, $args)) {
			$args['show_ui'] = ! $args[Constants::TYPE_ARG_HIDE_FROM_NAV];
			unset($args[Constants::TYPE_ARG_HIDE_FROM_NAV]);
		}

		return array_merge($default, $args);
	}

	private function generateFormItems(Model $related): array {
		return array_map(function (Model $model) {
			$attr = $model->getAttributes();
			$ID = $attr[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];

			return new Displayable($ID, $attr[Constants::TYPE_INSTANCE_TITLE_ATTR]);
		}, $this->manager->select($related));
	}

	private function generateListItems(Model $parent): array {
		return array_map(function (Model $model) {
			$attr = $model->getAttributes();
			$ID = $attr[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];
			$title = new Displayable($ID, $attr[Constants::TYPE_INSTANCE_TITLE_ATTR]);
			$description = new Displayable($ID, $attr[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR]);

			return new MultiDimensional([$title, $description]);
		}, $this->manager->selectAllRelations($parent));
	}

	private function parseFieldSignature(array $data, array $new): array {
		if (array_key_exists(Constants::FIELD_TYPE_SIGNATURE, $data)) {
			$new[Constants::FIELD_TYPE_SIGNATURE] = $data[Constants::FIELD_TYPE_SIGNATURE];
		}

		return $new;
	}
}