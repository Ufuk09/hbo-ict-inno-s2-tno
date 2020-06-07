<?php

namespace TNO\EssifLab\Tests\Stubs;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WP;

class Utility extends BaseUtility {
	private $history = [];

	static $meta = array();

	protected $callbackTriggeringFunctions = [
		WP::ADD_ACTION => [self::class, 'addHook'],
		WP::ADD_FILTER => [self::class, 'addHook'],
		WP::ADD_META_BOX => [self::class, 'addMetaBox'],
	];

	protected $valueReturningFunctions = [
		BaseUtility::GET_CURRENT_MODEL => [self::class, 'getCurrentModel'],
        BaseUtility::GET_MODEL => [self::class, 'getModel'],
        BaseUtility::CREATE_MODEL_META => [self::class, 'createModelMeta'],
        BaseUtility::DELETE_MODEL_META => [self::class, 'deleteModelMeta'],
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

    static function getModel(int $id): Model {
        return self::createModelWithId($id);
    }

	static function getCurrentModel(): Model {
		return self::createModelWithId(1);
	}

	static function createModelMeta(int $postId, string $key, $value): bool {
	    if (!isset(self::$meta[$postId])){
	        self::$meta[$postId] = array();
        }
	    if (!isset(self::$meta[$postId][$key])){
	        self::$meta[$postId][$key] = array();
        }
	    self::$meta[$postId][$key][] = $value;
	    return true;
    }

    static function deleteModelMeta(int $postId, string $key, $value = ''): bool {
        if (self::checkPostIdAndKey($postId, $key)){
            if (!empty($value)){
                if (($value_key = array_search($value, self::$meta[$postId][$key])) !== false){
                    unset(self::$meta[$postId][$key][$value_key]);
                    return !in_array($value, self::$meta[$postId][$key]);
                }
            } else {
                self::$meta[$postId][$key] = array();
                return empty($meta[$postId][$key]) ? true : false;
            }
        }
        return false;
    }

	static function getModelMeta(int $postId, string $key): array {
	    if (isset(self::$meta) && isset(self::$meta[$postId]) && isset(self::$meta[$postId][$key])){
            return self::$meta[$postId][$key];
        }
		return [1];
	}

	static function getModels(array $args = []): array {
	    if (!empty($args) && !empty($args['post__in'])){
            return array_map(function($id) {
                return self::createModelWithId($id);
            }, $args['post__in']);
        }
        return [self::createModelWithId(1)];
    }

    static function createModelWithId($id): Model
    {
        return new Model([
            Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => $id,
            Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
            Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world'
        ]);
    }

    private static function checkPostIdAndKey(int $postId, string $key): bool
    {
        return isset(self::$meta) && isset(self::$meta[$postId]) && isset(self::$meta[$postId][$key]);
    }

    public function clearMeta(): void {
	    self::$meta = array();
    }
}