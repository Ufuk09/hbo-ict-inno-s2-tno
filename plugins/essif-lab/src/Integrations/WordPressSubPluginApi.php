<?php

namespace TNO\EssifLab\Integrations;

use ReflectionClass;
use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Models\Input;
use TNO\EssifLab\Models\Target;
use TNO\EssifLab\Utilities\WP;

class WordPressSubPluginApi extends BaseIntegration {
	const TRIGGER_PRE = 'essif-lab_';
	const PARAMS = 'params';
	const RELATION = 'relation';

	const TRIGGER_TYPES = [
		Hook::class => [self::PARAMS => 1],
		Target::class => [self::PARAMS => 2, self::RELATION => Hook::class],
		Input::class => [self::PARAMS => 2, self::RELATION => Target::class],
	];

	function install(): void {
		foreach (self::TRIGGER_TYPES as $type => $args) {
			$params = $args[self::PARAMS];
			$this->addActionInsert($type, $params);
			$this->addActionDelete($type, $params);
			if ($params === 2) {
				$this->applyFilterSelectAllRelations($type, $args[self::RELATION]);
			} else {
				$this->applyFilterSelect($type);
			}
		}
	}

	private function addActionInsert(string $instance, int $params) {
		$triggerName = self::getTriggerName(self::TRIGGER_PRE.'insert_', $instance);
		$this->utility->call(WP::ADD_ACTION, $triggerName, function (...$params) use ($instance) {
			if (self::isArrayOfArrays($params)) {
				$instance = new $instance(self::getProps($params));

				$this->manager->insert($instance);
			}
		}, 1, $params);
	}

	private function addActionDelete(string $model, int $params) {
		$triggerName = self::getTriggerName(self::TRIGGER_PRE.'delete_', $model);
		$this->utility->call(WP::ADD_ACTION, $triggerName, function (...$params) use ($model) {
			if (self::isArrayOfArrays($params)) {
				$props = self::getProps($params);
				$slug = $props[Constants::TYPE_INSTANCE_SLUG_ATTR];
				$instance = new $model($props);
				$instances = $this->manager->select($instance, ['post_name' => $slug]);

				$this->manager->delete(empty($instances) ? null : current($instances));
			}
		}, 1, $params);
	}

	private function applyFilterSelect(string $model) {
		$triggerName = self::getTriggerName(self::TRIGGER_PRE.'select_', $model);
		$this->utility->call(WP::ADD_FILTER, $triggerName, function ($items) use ($model) {
			var_dump($model);
			return array_merge(is_array($items) ? $items : [], $this->manager->select(new $model()));
		}, 1, 1);
	}

	private function applyFilterSelectAllRelations(string $model, string $relation) {
		$triggerName = self::getTriggerName(self::TRIGGER_PRE.'select_', $model);
		$this->utility->call(WP::ADD_FILTER, $triggerName, function ($items, $parentSlug) use ($model, $relation) {
			$items = is_array($items) ? $items : [];
			$parentInstances = $this->manager->select(new $relation(), ['post_name' => $parentSlug]);
			$from = empty ($parentInstances) ? null : current($parentInstances);

			if (empty($from)) {
				return $items;
			}

			$relations = $this->manager->selectAllRelations($from, new $model());

			return array_merge($items, $relations);
		}, 1, 2);
	}

	private static function isArrayOfArrays(array $value): bool {
		return !empty(array_filter($value, 'is_array'));
	}

	private static function getTriggerName(string $prefix, string $model): string {
		return $prefix.strtolower((new ReflectionClass($model))->getShortName());
	}

	private static function getProps(array $models): array {
		return [
			Constants::TYPE_INSTANCE_SLUG_ATTR => self::getSlug($models),
			Constants::TYPE_INSTANCE_TITLE_ATTR => self::getTitle($models),
		];
	}

	private static function getTitle(array $models): string {
		return strval(current(end($models)));
	}

	private static function getSlug(array $models): string {
		return implode('__', array_map(function (array $param) {
			return key($param);
		}, $models));
	}
}