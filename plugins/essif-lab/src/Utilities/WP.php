<?php

namespace TNO\EssifLab\Utilities;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;

class WP extends BaseUtility {
	const ADD_ACTION = 'add_action';

	const ADD_FILTER = 'add_filter';

	const DO_ACTION = 'do_action';

	const APPLY_FILTERS = 'apply_filters';

	const REMOVE_ALL_ACTIONS_AND_EXEC = 'remove_all_actions_and_exec';

	const ADD_NAV_ITEM = 'add_menu_page';

	const ADD_META_BOX = 'add_meta_box';

	protected $functions = [
		self::ADD_ACTION => [self::class, 'addAction'],
		self::ADD_FILTER => [self::class, 'addFilter'],
		self::DO_ACTION => [self::class, 'doAction'],
		self::APPLY_FILTERS => [self::class, 'applyFilter'],
		self::REMOVE_ALL_ACTIONS_AND_EXEC => [self::class, 'removeAllActionsAndExecute'],
		self::ADD_META_BOX => [self::class, 'addMetaBox'],
		self::ADD_NAV_ITEM => [self::class, 'addAdminNav'],
	];

	static function addAction(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {
		add_action($hook, $callback, $priority, $accepted_args);
	}

	static function addFilter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {
		add_filter($hook, $callback, $priority, $accepted_args);
	}

	static function doAction(string $tag, ...$params): void {
		do_action($tag, ...$params);
	}

	static function applyFilter(string $tag, $value, ...$params) {
		return apply_filters($tag, $value, ...$params);
	}

	static function removeAllActionsAndExecute(string $tag, callable $callback) {
		// Backup all filters and remove all actions temporary
		global $wp_filter, $merged_filters;
		$backup_wp_filter = $wp_filter;
		$backup_merged_filters = $merged_filters;
		remove_all_actions($tag);

		// Execute the callback for the action once
		$callback();

		// Restore filters
		$wp_filter = $backup_wp_filter;
		$merged_filters = $backup_merged_filters;
	}

	static function addMetaBox(string $id, string $title, callable $callback, string $screen): void {
		add_meta_box($id, $title, $callback, $screen, 'normal');
	}

	static function addAdminNav(string $title, string $capability, string $slug, string $icon): void {
		add_menu_page($title, $title, $capability, $slug, null, $icon);
	}

	static function createModelType(string $postType, array $args = []): void {
		register_post_type($postType, $args);
	}

	static function createModel(array $args): bool {
		$result = wp_insert_post($args, true);
		if (! is_int($result)) {
			throw $result;
		}

		return $result;
	}

	static function updateModel($args): bool {
		$result = wp_update_post($args, true);
		if (! is_int($result)) {
			throw $result;
		}

		return $result;
	}

	static function deleteModel(int $postId): bool {
		return wp_delete_post($postId, true);
	}

	static function getModel(int $id): ?Model {
		return self::modelFactory(get_post($id)->to_array());
	}

	static function getModels(array $args = []): array {
		return array_map(function ($post) {
			return self::modelFactory($post->to_array());
		}, get_posts(array_merge([
			'numberposts' => -1,
			Constants::MODEL_TYPE_INDICATOR => 'any',
		], $args)));
	}

	static function getCurrentModel(): ?Model {
		global $post;

		if (empty($post) && array_key_exists('post', $_GET)) {
			$post = get_post($_GET['post']);
		}

		if (empty($post) && array_key_exists(Constants::MODEL_TYPE_INDICATOR, $_GET)) {
			return self::modelFactory([Constants::MODEL_TYPE_INDICATOR => $_GET[Constants::MODEL_TYPE_INDICATOR]]);
		}

		if (empty($post)) {
			return null;
		}

		return self::modelFactory($post->to_array());
	}

	private static function modelFactory(array $args): ?Model {
		$type = array_key_exists(Constants::MODEL_TYPE_INDICATOR, $args) ? $args[Constants::MODEL_TYPE_INDICATOR] : '';

		$className = implode('', array_map('ucfirst', explode(' ', str_replace('-', ' ', $type))));
		$FQN = Constants::TYPE_NAMESPACE.'\\'.$className;

		if (empty($type) || ! class_exists($FQN) || ! in_array(Model::class, class_implements($FQN))) {
			return null;
		}

		$attrs = self::extractAttributesFromArgs($args);

		return new $FQN($attrs);
	}

	private static function extractAttributesFromArgs(array $args): array {
		$id = array_key_exists(Constants::TYPE_INSTANCE_IDENTIFIER_ATTR, $args) ? $args[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR] : 0;
		$title = array_key_exists('post_title', $args) ? $args['post_title'] : '';
		$content = array_key_exists('post_content', $args) ? $args['post_content'] : '';
		$contentToJson = self::jsonStringToAssocArray($content);
		$description = empty($contentToJson) ? $content : '';

		return array_filter(array_merge($contentToJson, [
			Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => $id,
			Constants::TYPE_INSTANCE_TITLE_ATTR => $title,
			Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => $description,
		]));
	}

	private static function jsonStringToAssocArray(string $string): array {
		$json = json_decode($string);
		$isValidJson = json_last_error() === JSON_ERROR_NONE;
		$isAssocArray = is_array($json) && array_keys($json) !== range(0, count($json) - 1);
		if ($isValidJson && $isAssocArray) {
			return $json;
		}

		return [];
	}

	static function createModelMeta(int $postId, string $key, $value): bool {
		return add_post_meta($postId, $key, $value, false);
	}

	static function deleteModelMeta(int $postId, string $key, $value): bool {
		return delete_post_meta($postId, $key, $value);
	}

	static function getModelMeta(int $postId, string $key): array {
		$meta = get_post_meta($postId, $key, false);

		return is_array($meta) ? $meta : [];
	}

	static function getEditModelLink(int $postId): string {
		return get_edit_post_link($postId);
	}

	static function getCreateModelLink(string $postType): string {
		return add_query_arg(['post_type' => $postType], admin_url('post-new.php'));
	}
}