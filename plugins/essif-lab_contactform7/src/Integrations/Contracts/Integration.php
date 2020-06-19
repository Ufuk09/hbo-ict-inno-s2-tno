<?php

namespace TNO\ContactForm7\Integrations\Contracts;

use TNO\ContactForm7\Applications\Contracts\Application;
use TNO\ContactForm7\Utilities\Contracts\Utility;

interface Integration {
	function __construct(Application $application, Utility $utility);

	function install(): void;

	function getApplication(): Application;

	function getUtility(): Utility;
}