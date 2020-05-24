<?php

namespace TNO\EssifLab\Views;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseField;

class SchemaLoaderField extends BaseField {
	const STYLE = 'style';

	const TARGET = 'target';

	function render(): string {
		$contents = $this->renderContents();

		return '<table class="form-table">'.$contents.'</table>';
	}

	function renderContents(): string {
		$name = $this->getFieldName(Constants::FIELD_TYPE_SCHEMA_LOADER);
		$placeholder = $this->getPlaceholderOption($name);
		$jsonSchemaFromUrl = $this->renderJsonSchemaFromUrlField($name);

		return $placeholder.$jsonSchemaFromUrl;
	}

	private function getPlaceholderOption(string $baseName): string {
		$label = 'Do not load a schema';
		$attrs = ['value' => '', 'checked' => 'checked'];

		return '<tr>'.$this->renderStyleOption($baseName, $label, $attrs).'</tr>';
	}

	private function renderJsonSchemaFromUrlField(string $baseName): string {
		$label = 'JSON schema from URL';
		$labelAttrs = ['value' => 'json_schema_from_url'];
		$option = $this->renderStyleOption($baseName, $label, $labelAttrs);

		$name = $baseName.'['.self::TARGET.']';
		$inputAttrs = $this->getElementAttributes([
			'class' => 'regular-text',
			'type' => 'url',
			'name' => $name,
			'placeholder' => 'Enter an URL to a JSON schema',
		]);
		$input = "<td><input$inputAttrs/></td>";

		return '<tr>'.$option.$input.'</tr>';
	}

	private function renderStyleOption(string $baseName, string $label, array $attrs = []): string {
		$name = $baseName.'['.self::STYLE.']';

		$inputAttrs = $this->getElementAttributes(array_merge([
			'name' => $name,
			'type' => 'radio',
		], $attrs));

		return "<th><label><input$inputAttrs/>$label</label></th>";
	}
}