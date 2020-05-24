<?php

namespace TNO\EssifLab\ModelManagers\Contracts;

use TNO\EssifLab\Applications\Contracts\Application;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\Utility;

abstract class BaseModelManager implements ModelManager {
	protected $application;

	protected $utility;

	function __construct(Application $application, Utility $utility) {
		$this->application = $application;
		$this->utility = $utility;
	}

	protected static function forEachModel(array $classNames, callable $callback): void {
		if (! empty($classNames)) {
			foreach ($classNames as $className) {
				if (self::isConcreteModel($className)) {
					$instance = new $className();
					$callback($instance);
				}
			}
		}
	}

	protected static function isConcreteModel(string $class): bool {
		return class_exists($class) && in_array(Model::class, class_implements($class));
	}
}