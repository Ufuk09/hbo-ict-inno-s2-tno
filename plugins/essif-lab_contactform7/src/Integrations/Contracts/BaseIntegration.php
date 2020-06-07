<?php

namespace TNO\ContactForm7\Integrations\Contracts;

use HaydenPierce\ClassFinder\ClassFinder;
use TNO\ContactForm7\Applications\Contracts\Application;
use TNO\ContactForm7\Utilities\Contracts\Utility;

abstract class BaseIntegration implements Integration {
	protected $application;

	protected $manager;

	protected $renderer;

	protected $utility;

	function __construct(Application $application, Utility $utility) {
		$this->application = $application;
		$this->utility = $utility;
	}

	function getApplication(): Application {
		return $this->application;
	}

	function getUtility(): Utility {
		return $this->utility;
	}
}