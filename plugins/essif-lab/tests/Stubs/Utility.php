<?php

namespace TNO\EssifLab\Tests\Stubs;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WP;

class Utility extends BaseUtility {
	private $history = [];

	protected $callbackTriggeringFunctions = [
		WP::ADD_ACTION => [self::class, 'addHook'],
		WP::ADD_FILTER => [self::class, 'addHook'],
		WP::ADD_META_BOX => [self::class, 'addMetaBox'],
	];

	protected $valueReturningFunctions = [
		BaseUtility::GET_CURRENT_MODEL => [self::class, 'getCurrentModel'],
		BaseUtility::GET_MODEL_META => [self::class, 'getModelMeta'],
		BaseUtility::GET_MODELS => [self::class, 'getModels'],
	];

	function call(string $name, ...$parameters) {
		$wasCalled = count($this->getHistoryByFuncName($name));
		$this->history[] = new History($name, $parameters, $wasCalled + 1);

		if (array_key_exists($name, $this->callbackTriggeringFunctions)) {
			$callback = $this->callbackTriggeringFunctions[$name];
			$callback(...$parameters);
		}

		if (array_key_exists($name, $this->valueReturningFunctions)) {
			$callback = $this->valueReturningFunctions[$name];

			return $callback(...$parameters);
		}

		return null;
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

	static function addHook(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {
		$params = range(0, $accepted_args);
		$callback(...$params);
	}

	static function addMetaBox($id, $title, $callback, $screen) {
		$callback();
	}

	static function getCurrentModel(): Model {
		return new Model([
			Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => 1,
			Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
			Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world',
		]);
	}

	static function getModelMeta(): array {
		return [1];
	}

	static function getModels(): array {
		return [
			new Model([
				Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => 1,
				Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
				Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world',
			]),
		];
	}
}