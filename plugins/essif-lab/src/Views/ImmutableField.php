<?php


namespace TNO\EssifLab\Views;


use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseField;

class ImmutableField extends BaseField {
    function render(): string {
        $label = 'Force credential\'s value? ';

        $name = $this->getFieldName(Constants::FIELD_TYPE_IMMUTABLE);
        $checked = $this->getCheckedValue();
        $attrs = $this->getElementAttributes([
            'type' => 'checkbox',
            'name' => $name,
            'checked' => $checked,
        ]);

        return '<label style="font-size: 14px">'.$label.'<input'.$attrs.'/></label>';
    }

    private function getCheckedValue(): string {
        $attrs = $this->model->getAttributes();
        if (!array_key_exists(Constants::TYPE_INSTANCE_DESCRIPTION_ATTR, $attrs)) {
            return '';
        }

        $json = json_decode($attrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR], true);
        if (!is_array($json) || !array_key_exists(Constants::FIELD_TYPE_IMMUTABLE, $json)) {
            return '';
        }

        return $json[Constants::FIELD_TYPE_IMMUTABLE];
    }
}