<?php

namespace TNO\EssifLab\Integrations;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\Models\Hook;
use TNO\EssifLab\Utilities\WP;

class WordPressSubPluginApi extends BaseIntegration {
	const TRIGGER_PRE = 'essif-lab_';

	const TRIGGER_INSERT_PRE = self::TRIGGER_PRE.'insert_';

	const TRIGGER_DELETE_PRE = self::TRIGGER_PRE.'delete_';

	const TRIGGER_SELECT_PRE = self::TRIGGER_PRE.'select_';

	const TRIGGER_NAME_HOOK = 'hook';

	const TRIGGER_NAME_TARGET = 'target';

	function install(): void {
		$this->addActionInsertHook();
		$this->addActionDeleteHook();
		$this->applyFilterSelectHooks();
	}

	private function addActionInsertHook() {
		$triggerName = self::TRIGGER_INSERT_PRE.self::TRIGGER_NAME_HOOK;
		$this->utility->call(WP::ADD_ACTION, $triggerName, function ($hooks) {
			if (is_array($hooks)) {
				foreach ($hooks as $slug => $title) {
					$instance = new Hook([
						Constants::TYPE_INSTANCE_TITLE_ATTR => $hooks[$slug],
						Constants::TYPE_INSTANCE_SLUG_ATTR => $slug,
					]);

					$this->manager->insert($instance);
				}
			}
		});
	}

	private function addActionDeleteHook() {
		$triggerName = self::TRIGGER_DELETE_PRE.self::TRIGGER_NAME_HOOK;
		$this->utility->call(WP::ADD_ACTION, $triggerName, function ($hooks) {
			if (is_array($hooks)) {
				return array_filter(array_map(function ($slug) use ($hooks) {
					$instance = new Hook([
						Constants::TYPE_INSTANCE_SLUG_ATTR => $slug,
						Constants::TYPE_INSTANCE_TITLE_ATTR => $hooks[$slug],
					]);
					$models = $this->manager->select($instance, ['post_name' => $slug]);
					$model = empty($models) ? null : $models[0];

					return empty($model) ? false : $this->manager->delete($model);
				}, array_keys($hooks)));
			}

			return [];
		});
	}

	private function applyFilterSelectHooks() {
		$triggerName = self::TRIGGER_SELECT_PRE.self::TRIGGER_NAME_HOOK;
		$this->utility->call(WP::ADD_FILTER, $triggerName, function ($items) {
			return array_merge(is_array($items) ? $items : [], $this->manager->select(new Hook()));
		});
	}
}