<?php

namespace TNO\EssifLab\Tests\Stubs;

trait WithHistory {
	protected $history = [];

	protected function recordHistory(string $funcName, array $parameters = [], callable $handleParameters = null) {
		$wasCalled = count($this->getHistoryByFuncName($funcName));
		$this->history[] = new History($funcName, $parameters, $wasCalled + 1);

		if (!empty($handleParameters) && is_array($parameters) && !empty($parameters)) {
			array_map(function ($parameter) use ($handleParameters) {
				$handleParameters($parameter);
			}, $parameters);
		}
	}

	/**
	 * @param string $funcName
	 * @return History[]
	 */
	function getHistoryByFuncName(string $funcName): array {
		return array_slice(array_filter($this->history, function (History $history) use ($funcName) {
			return $history->getFuncName() === $funcName;
		}), 0);
	}
}