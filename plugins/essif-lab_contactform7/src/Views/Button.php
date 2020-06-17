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
        return "<br /><input type=\"submit\" value=\"Gegevens inladen\" id=\"essif-lab\" class=\"wpcf7-form-control wpcf7-submit\">";
    }
}