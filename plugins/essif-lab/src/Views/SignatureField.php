<?php

namespace TNO\EssifLab\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseField;

class SignatureField extends BaseField {
	function render(): string {
		$name = $this->getFieldName(Constants::FIELD_TYPE_SIGNATURE);
		$value = $this->getFieldValue();
		$attrs = $this->getElementAttributes([
			'type' => 'text',
			'class' => 'regular-text',
			'name' => $name,
			'value' => $value,
		]);

		return '<input'.$attrs.'/>';
	}

	private function getFieldValue(): string {
		$attrs = $this->model->getAttributes();
		if (!array_key_exists(Constants::TYPE_INSTANCE_DESCRIPTION_ATTR, $attrs)) {
			return '';
		}

		$json = json_decode($attrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR], true);
		if (!is_array($json) || !array_key_exists(Constants::FIELD_TYPE_SIGNATURE, $json)) {
			return '';
		}

		return $json[Constants::FIELD_TYPE_SIGNATURE];
	}
}