<?php

namespace TNO\ContactForm7\Integrations\Contracts;

use TNO\ContactForm7\Applications\Contracts\Application;
use TNO\ContactForm7\Utilities\Contracts\Utility;
use TNO\ContactForm7\Views\Button;

interface Integration {
	function __construct(Application $application, Utility $utility);

	function install(): void;

	function getApplication(): Application;

	function getUtility(): Utility;
}