<?php

namespace TNO\EssifLab\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Views\Contracts\BaseView;

class ListForm extends BaseView {
	const ADD_NEW = 'Add New %s';

	const ADD_RELATION = 'Add Relation';

	const NO_VALUES = 'No %s found! %s to continue editing.';

	function render(): string {
		if (empty($this->getDisplayableItems())) {
			return sprintf(self::NO_VALUES, $this->model->getPluralName(), $this->renderAddNewButton());
		}

		return $this->renderActions().$this->renderAddNewButton();
	}

	private function renderActions(): string {
		$select = new Select($this->integration, $this->model, $this->getDisplayableItems());

		return '<div class="actions">'.$select->render().$this->renderAddRelationButton().'</div>';
	}

	private function renderAddNewButton(): string {
		$url = $this->integration->getUtility()->call(BaseUtility::GET_CREATE_MODEL_LINK, $this->model->getTypeName());
		$message = sprintf(self::ADD_NEW, ucfirst($this->model->getSingularName()));

		return '<a href="'.$url.'" class="button btn">'.$message.'</a>';
	}

	private function renderAddRelationButton() {
	    $name = $this->integration->getApplication()->getNamespace().'['.Constants::ACTION_NAME_RELATION_ACTION.']';
	    $value = $this->model->getTypeName();

		return '<button class="button btn" type="submit" name="'.$name.'" value="'.$value.'">'.self::ADD_RELATION.'</button>';
	}
}