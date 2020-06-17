<?php

namespace TNO\ContactForm7\Views;

use TNO\ContactForm7\Utilities\WP;

class Button
{

    function custom_add_form_tag_essif_lab()
    {
        $wp = new WP();
        $wp->addFormTag();
    }

    function custom_essif_lab_form_tag_handler($tag)
    {
        $inputType = "submit";
        $inputValue = 'Gegevens inladen';
        $inputClass = "wpcf7-form-control wpcf7-submit essif-lab";

        $button = "<br /><input type=\"" . $inputType . "\" value=\"" . $inputValue . "\" class=\"" . $inputClass . "\">";
        return $button;
    }
}