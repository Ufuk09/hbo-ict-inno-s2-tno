<?php

namespace TNO\EssifLab\Integrations;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WordPress as WP;
use TNO\EssifLab\Views\Items\Displayable;
use TNO\EssifLab\Views\Items\MultiDimensional;
use TNO\EssifLab\Views\TypeList;

class WordPress extends BaseIntegration {
	const DEFAULT_TYPE_ARGS = [
		'public' => false,
		'show_ui' => true,
	];

	function install(): void {
		$this->utility->call(WP::ADD_ACTION, 'admin_menu', [$this, 'registerAdminMenu']);
		$this->utility->call(WP::ADD_ACTION, 'init', [$this, 'registerModelTypes']);
		$this->registerMetaBoxes();
	}

	function registerAdminMenu(): void {
		$title = $this->application->getName();
		$capability = Constants::ADMIN_MENU_CAPABILITY;
		$slug = $this->application->getNamespace();
		$icon = Constants::ADMIN_MENU_ICON_URL;
		$this->utility->call(WP::ADD_NAV_ITEM, $title, $capability, $slug, $icon);
	}

	function registerModelTypes(): void {
		BaseIntegration::forAllModels(function (Model $instance) {
			$this->registerModelType($instance);
		});
	}

	function registerModelType(Model $model): void {
		$args = $this->parseTypeArgs($model);
		$this->utility->call(BaseUtility::CREATE_MODEL_TYPE, $model->getTypeName(), $args);
	}

	function registerMetaBoxes(): void {
		BaseIntegration::forAllModels(function (Model $model) {
			$hook = 'add_meta_boxes_'.$model->getTypeName();
			$this->utility->call(WP::ADD_ACTION, $hook, function () use ($model) {
				$this->registerModelRelations($model);
			});
		});
	}

	function registerModelRelations(Model $model): void {
		$classes = $model->getRelations();
		BaseIntegration::forEachModel($classes, function (Model $related) use ($model) {
			$id = $model->getTypeName().'_'.$related->getTypeName();
			$title = self::toTitleCase($related->getPluralName());
			$callback = function () use ($model, $related) {
				print $this->renderModelRelation($model, $related);
			};
			$screen = $model->getTypeName();
			$this->utility->call(WP::ADD_META_BOX, $id, $title, $callback, $screen);
		});
	}

	function renderModelRelation(Model $parent, Model $related): string {
        $formItems = $this->generateFormItems($related);
        $listItems = $this->generateListItems($parent);
		$values = [
			new MultiDimensional($formItems, TypeList::FORM_ITEMS),
			new MultiDimensional($listItems, TypeList::LIST_ITEMS),
		];

		return $this->renderer->renderListAndFormView($this, $related, $values);
	}

	static function getAddTypeLink(string $postType) {
		return get_admin_url(null, 'edit.php?post_type='.$postType);
	}

	static function generateLabels(Model $model): array {
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

	static function toTitleCase(string $v): string {
		return implode(' ', array_map('ucfirst', explode(' ', $v)));
	}

	private function parseTypeArgs(Model $model): array {
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

    /**
     * @param Model $related
     * @return array
     */
    public function generateFormItems(Model $related): array
    {
        return array_map(function (Model $model) {
            $attr = $model->getAttributes();
            $ID = $attr[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];
            return new Displayable($ID, $attr[Constants::TYPE_INSTANCE_TITLE_ATTR]);
        }, $this->manager->select($related));
    }

    /**
     * @param Model $parent
     * @return array
     */
    public function generateListItems(Model $parent): array
    {
        return array_map(function (Model $model) {
            $attr = $model->getAttributes();
            $ID = $attr[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];
            $title = new Displayable($ID, $attr[Constants::TYPE_INSTANCE_TITLE_ATTR]);
            $description = new Displayable($ID, $attr[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR]);
            return new MultiDimensional([$title, $description]);
        }, $this->manager->selectAllRelations($parent));
    }
}