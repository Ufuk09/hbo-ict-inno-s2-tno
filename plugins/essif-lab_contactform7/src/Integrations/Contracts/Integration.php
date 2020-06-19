<?php

namespace TNO\ContactForm7\Integrations\Contracts;

use TNO\ContactForm7\Applications\Contracts\Application;
use TNO\ContactForm7\Utilities\Contracts\Utility;
use TNO\ContactForm7\Utilities\Helpers\CF7Helper;

interface Integration {
	function __construct(Application $application, Utility $utility);

	function install(CF7Helper $cf7Helper): void;

	function getApplication(): Application;

	function getUtility(): Utility;
}