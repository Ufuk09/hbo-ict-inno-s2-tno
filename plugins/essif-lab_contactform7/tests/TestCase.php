<?php

namespace TNO\ContactForm7\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use TNO\ContactForm7\Tests\Stubs\Application;
use TNO\ContactForm7\Tests\Stubs\Utility;

abstract class TestCase extends PHPUnitTestCase {
    protected $application;
    /**
     * @var Utility
     */
    protected $utility;
    protected $integration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->application = new Application("name", "namespace", __DIR__);
        $this->utility = new Utility();
    }


}