<?php

namespace TNO\EssifLab\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use TNO\EssifLab\Constants;
use TNO\EssifLab\Tests\Stubs\Application;
use TNO\EssifLab\Tests\Stubs\Integration;
use TNO\EssifLab\Tests\Stubs\Model;
use TNO\EssifLab\Tests\Stubs\ModelManager;
use TNO\EssifLab\Tests\Stubs\ModelRenderer;
use TNO\EssifLab\Tests\Stubs\Utility;

abstract class TestCase extends PHPUnitTestCase {
	protected $application;

	/** @var Utility */
	protected $utility;

	/** @var ModelManager */
	protected $manager;

	/** @var ModelRenderer */
	protected $renderer;

	/** @var Integration */
	protected $integration;

	/** @var Model */
	protected $model;

	protected function setUp(): void {
		parent::setUp();
		if (! defined('ABSPATH')) {
			define('ABSPATH', __DIR__);
		};
		$this->application = new Application('name', 'namespace', __DIR__);
		$this->utility = new Utility();
		$this->renderer = new ModelRenderer();
		$this->manager = new ModelManager($this->application, $this->utility);
		$this->integration = new Integration($this->application, $this->manager, $this->renderer, $this->utility);
		$this->model = new Model([
			Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
			Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world',
		]);
	}
}