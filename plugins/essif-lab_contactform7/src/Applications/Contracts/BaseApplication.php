<?php

namespace TNO\ContactForm7\Applications\Contracts;

class BaseApplication implements Application {
	protected $name;

	protected $namespace;

	protected $appDirectory;

	public function __construct($name, $namespace, $appDirectory) {
		$this->name = $name;
		$this->namespace = $namespace;
		$this->appDirectory = $appDirectory;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getNamespace(): string {
		return $this->namespace;
	}

	public function getAppDir(): string {
		return $this->appDirectory;
	}
}