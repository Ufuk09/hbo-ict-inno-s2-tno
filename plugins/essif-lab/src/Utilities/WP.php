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

	const POST_ID = Constants::TYPE_INSTANCE_IDENTIFIER_ATTR;

	const POST_NAME = 'post_name';

	const POST_TITLE = 'post_title';

	const POST_CONTENT = 'post_content';

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

	static function createModel(Model $model): bool {
		$result = wp_insert_post(self::mapModelToPost($model), true);
		if (! is_int($result)) {
			throw $result;
		}

		return $result;
	}

	static function updateModel(array $post): bool {
		$result = wp_update_post($post, true);
		if (! is_int($result)) {
			throw $result;
		}

		return $result;
	}

	static function deleteModel(int $postId): bool {
		return !empty(wp_delete_post($postId, true));
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

	private static function modelFactory(array $postAttrs): ?Model {
		$type = array_key_exists(Constants::MODEL_TYPE_INDICATOR, $postAttrs) ? $postAttrs[Constants::MODEL_TYPE_INDICATOR] : '';

		$className = implode('', array_map('ucfirst', explode(' ', str_replace('-', ' ', $type))));
		$FQN = Constants::TYPE_NAMESPACE.'\\'.$className;

		if (empty($type) || ! class_exists($FQN) || ! in_array(Model::class, class_implements($FQN))) {
			return null;
		}

		$modelAttrs = self::mapPostAttrsToModelAttrs($postAttrs);

		return new $FQN($modelAttrs);
	}

	private static function mapPostAttrsToModelAttrs(array $attrs): array {
		$content = array_key_exists(self::POST_CONTENT, $attrs) ? $attrs[self::POST_CONTENT] : '';
		$modelAttrs = self::mapPostContentToModelAttributes($content);

		if (array_key_exists(self::POST_ID, $attrs)) {
			$modelAttrs[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR] = $attrs[self::POST_ID];
		}
		if (array_key_exists(self::POST_NAME, $attrs)) {
			$modelAttrs[Constants::TYPE_INSTANCE_SLUG_ATTR] = $attrs[self::POST_NAME];
		}
		if (array_key_exists(self::POST_TITLE, $attrs)) {
			$modelAttrs[Constants::TYPE_INSTANCE_TITLE_ATTR] = $attrs[self::POST_TITLE];
		}

		return $modelAttrs;
	}

	private static function mapPostContentToModelAttributes(string $content): array {
		$json = json_decode($content);
		$isValidJson = json_last_error() === JSON_ERROR_NONE;
		$isAssocArray = is_array($json) && array_keys($json) !== range(0, count($json) - 1);
		if ($isValidJson && $isAssocArray) {
			return $json;
		}

		return !empty($content) ? [
			Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => $content,
		] : [];
	}

	private static function mapModelToPost(Model $model): array {
		$modelAttrs = $model->getAttributes();

		$postAttrs = [
			'post_type' => $model->getTypeName(),
		];
		if (array_key_exists(Constants::TYPE_INSTANCE_IDENTIFIER_ATTR, $modelAttrs)) {
			$postAttrs[self::POST_ID] = $modelAttrs[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];
		}
		if (array_key_exists(Constants::TYPE_INSTANCE_SLUG_ATTR, $modelAttrs)) {
			$postAttrs[self::POST_NAME] = $modelAttrs[Constants::TYPE_INSTANCE_SLUG_ATTR];
		}
		if (array_key_exists(Constants::TYPE_INSTANCE_TITLE_ATTR, $modelAttrs)) {
			$postAttrs[self::POST_TITLE] = $modelAttrs[Constants::TYPE_INSTANCE_TITLE_ATTR];
		}
		if (array_key_exists(Constants::TYPE_INSTANCE_DESCRIPTION_ATTR, $modelAttrs)) {
			$postAttrs[self::POST_CONTENT] = $modelAttrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR];
		}

		return $postAttrs;
	}

	static function createModelMeta(int $postId, string $key, $value): bool {
		return add_post_meta($postId, $key, $value, false);
	}

    static function updateModelMeta(int $postId, string $key, $value): bool {
        return update_post_meta($postId, $key, $value);
    }

	static function deleteModelMeta(int $postId, string $key, $value = ''): bool {
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