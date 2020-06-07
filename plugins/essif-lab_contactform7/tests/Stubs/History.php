<?php

namespace TNO\ContactForm7\Tests\Stubs;

class History {
	private $funcName;

	private $params;

	private $wasCalled;

	public function __construct(string $funcName, array $params = [], int $wasCalled = 1) {
		$this->funcName = $funcName;
		$this->params = $params;
		$this->wasCalled = $wasCalled;
	}

	public function getFuncName(): string {
		return $this->funcName;
	}


	public function getParams(): array {
		return $this->params;
	}

	public function getWasCalled(): int {
		return $this->wasCalled;
	}
}