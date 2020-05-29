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

	function install(): void {
		$this->addActionInsertHook();
		$this->addActionDeleteHook();
	}

	private function addActionInsertHook() {
		$this->utility->call(WP::ADD_ACTION, self::TRIGGER_INSERT_PRE.'hook', function ($hooks) {
			if (is_array($hooks)) {
				return array_filter(array_map(function ($slug) use ($hooks) {
					$instance = new Hook([
						Constants::TYPE_INSTANCE_TITLE_ATTR => $hooks[$slug],
						Constants::TYPE_INSTANCE_SLUG_ATTR => $slug,
					]);

					return $this->manager->insert($instance);
				}, array_keys($hooks)));
			}

			return [];
		});
	}

	private function addActionDeleteHook() {
		$this->utility->call(WP::ADD_ACTION, self::TRIGGER_DELETE_PRE.'hook', function ($hooks) {
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
}