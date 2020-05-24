<?php

namespace TNO\EssifLab\Views\Contracts;

abstract class BaseField extends BaseView {
	protected function getFieldName(string $key): string {
		return $this->integration->getApplication()->getNamespace().'['.$key.']';
	}

	protected function getElementAttributes(array $attrs = []): string {
		return ' '.implode(' ', array_filter(array_map(function ($key, $value) {
				if (! empty($key) && ! empty($value)) {
					return $key.'="'.$value.'"';
				}

				return '';
			}, array_keys($attrs), $attrs)));
	}
}