<?php

namespace TNO\ContactForm7\Integrations;

use TNO\ContactForm7\Integrations\Contracts\BaseIntegration;

class WordPress extends BaseIntegration {
	function install(): void {
        $this->utility->addEssifLabButton();
        $this->utility->loadCustomScripts();

        $this->utility->addActivateHook();
        $this->utility->addDeactivateHook();
    }
}