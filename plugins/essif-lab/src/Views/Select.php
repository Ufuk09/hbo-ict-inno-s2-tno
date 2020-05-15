<?php

namespace TNO\EssifLab\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseView;
use TNO\EssifLab\Views\Items\Contracts\Item;

class Select extends BaseView {
	function render(): string {
		if (empty($this->getDisplayableItems())) {
			return '';
		}
		
		$name = $this->integration->getApplication()->getNamespace() . '['.Constants::ACTION_NAME_ADD_RELATION.']['.$this->model->getTypeName().']';
		return '<select name="'.$name.'">'.$this->renderItems().'</select>';
	}

	private function renderItems(): string {
		return implode('', array_map(function (Item $item) {
			return '<option value="'.$item->getValue().'">'.$item->getLabel().'</option>';
		}, $this->getDisplayableItems()));
	}
}