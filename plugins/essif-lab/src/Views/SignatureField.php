<?php

namespace TNO\EssifLab\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseView;

class SignatureField extends BaseView {
	function render(): string {
		$name = $this->getFieldName();
		$value = $this->getFieldValue();
		$attrs = $this->getElementAttributes([
			'type' => 'text',
			'style' => 'width:100%',
			'name' => $name,
			'value' => $value,
		]);

		return '<input'.$attrs.'/>';
	}

	private function getFieldName(): string {
		return $this->integration->getApplication()->getNamespace().'['.Constants::FIELD_TYPE_SIGNATURE.']';
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

	private function getElementAttributes(array $attrs = []): string {
		$parsed = [];
		foreach ($attrs as $key => $value) {
			if (! empty($key) && ! empty($value)) {
				$parsed[] = $key.'="'.$value.'"';
			}
		}

		return " ".implode(" ", array_filter($parsed));
	}
}