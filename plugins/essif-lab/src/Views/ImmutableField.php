<?php


namespace TNO\EssifLab\Views;


use TNO\EssifLab\Constants;
use TNO\EssifLab\Views\Contracts\BaseField;

class ImmutableField extends BaseField {
    function render(): string {
        $label = 'Force credential\'s value? ';

        $name = $this->getFieldName(Constants::FIELD_TYPE_IMMUTABLE);
        $attrs = $this->getElementAttributes([
            'type' => 'checkbox',
            'name' => $name,
            'checked' => $this->items['checked'],
        ]);

        return '<label style="font-size: 14px">'.$label.'<input'.$attrs.'/></label>';
    }
}