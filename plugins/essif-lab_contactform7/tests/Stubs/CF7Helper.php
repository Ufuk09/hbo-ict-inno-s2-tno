<?php

namespace TNO\ContactForm7\Tests\Stubs;

class CF7Helper
{
    function getTestTarget()
    {
        return [
            12 => "Contactform 1",
            13 => "Contactform 2",
            14 => "Contactform 3",
            15 => "Contactform 4",
            16 => "Contactform 5"
        ];
    }

    function getTestInput()
    {
        return [
            12 => "Contactform 1",
            ["my-email" => "Email",
                "my-name" => "Name",
                "my-message" => "Message"]
        ];
    }
}