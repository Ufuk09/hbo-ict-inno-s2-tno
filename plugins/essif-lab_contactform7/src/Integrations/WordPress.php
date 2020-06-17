<?php

namespace TNO\ContactForm7\Integrations;

use TNO\ContactForm7\Integrations\Contracts\BaseIntegration;

class WordPress extends BaseIntegration {
	function install(): void {
	    $this->utility->getAllForms();
	    $this->utility->getTargetsFromForms([], "Title", 1);

        $this->utility->insertHook();
        $this->utility->insertTarget(0, "Target_title");
        $this->utility->insertInput("Input_slug", "Input_title", 0);

        $this->utility->deleteHook();
        $this->utility->deleteTarget(0, "Target_title");
        $this->utility->deleteInput("Input_slug", "Input_title", 0);

        $this->utility->selectHook("contact-form-7", "Contact Form 7");
        $this->utility->selectTarget([], "contact-form-7");
        $this->utility->selectInput([], "contact-form-7");

        $this->utility->addEssifLabButton();
        $this->utility->loadCustomScripts();

        $this->utility->addActivateHook();
        $this->utility->addDeactivateHook();
    }
}