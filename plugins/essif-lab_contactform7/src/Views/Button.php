<?php

namespace TNO\ContactForm7\Views;

class Button
{
    public static function custom_essif_lab_form_tag_handler() : string {
        $inputType = "Submit";
        $inputValue = "Gegevens inladen";
        $inputClass = "wpcf7-form-control wpcf7-submit essif-lab";

        return "<br /><input type=\"" . $inputType . "\" value=\"" . $inputValue . "\" class=\"" . $inputClass . "\">";
    }
}