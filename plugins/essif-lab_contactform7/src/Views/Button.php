<?php

namespace TNO\ContactForm7\Views;

class Button
{
    function custom_essif_lab_form_tag_handler($tag)
    {
        $inputType = "Submit";
        $inputValue = "Gegevens inladen";
        $inputClass = "wpcf7-form-control wpcf7-submit essif-lab";

        $button = "<br /><input type=\"" . $inputType . "\" value=\"" . $inputValue . "\" class=\"" . $inputClass . "\">";
        return $button;
    }
}