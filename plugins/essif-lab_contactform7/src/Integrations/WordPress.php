<?php

namespace TNO\ContactForm7\Integrations;

use TNO\ContactForm7\Integrations\Contracts\BaseIntegration;
use TNO\ContactForm7\Utilities\Helpers\CF7Helper;

class WordPress extends BaseIntegration {
	function install(): void {
	    $cf7helper = new CF7Helper();

        $this->utility->insertHook();
        $this->utility->insertTarget(1, "Target");
        $this->utility->insertInput("Input", "Input", 1);

        $this->utility->deleteHook();
        $this->utility->deleteTarget(1, "Target");
        $this->utility->deleteInput("Input", "Input", 1);

        $this->utility->selectHook("contact-form-7", "Contact Form 7");
        $this->utility->selectTarget($cf7helper->getAllTargets(), "contact-form-7");
        $this->utility->selectInput([], "contact-form-7");

        $this->utility->addEssifLabButton();
        $this->utility->loadCustomJs();
	}

	function deinstall() {
        // TODO: Write all actions for deinstalling subplugin
    }
}