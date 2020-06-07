<?php

namespace TNO\ContactForm7\Views;

class Button
{

    function custom_add_form_tag_essif_lab()
    {
        wpcf7_add_form_tag('essif_lab', array ($this, 'custom_essif_lab_form_tag_handler' ) );
    }

    function custom_essif_lab_form_tag_handler($tag)
    {
        return "<br /><input type=\"submit\" value=\"Gegevens inladen\" id=\"essif-lab\" class=\"wpcf7-form-control wpcf7-submit\">";
    }
}