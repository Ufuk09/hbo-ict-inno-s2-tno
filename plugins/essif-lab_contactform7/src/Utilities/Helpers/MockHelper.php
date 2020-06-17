<?php

namespace TNO\ContactForm7\Utilities\Helpers;

class MockHelper
{
    function getTestTarget()
    {
        $targets = [
            12 => "Contactform 1",
            13 => "Contactform 2",
            14 => "Contactform 3",
            15 => "Contactform 4",
            16 => "Contactform 5"
        ];
        return $targets;
    }

    function getTestInput()
    {
        $input = [
            12 => "Contactform 1",
            ["my-email" => "Email",
                "my-name" => "Name",
                "my-message" => "Message"]
        ];
        return $input;
    }
}