<?php

namespace TNO\EssifLab\ModelManagers;

use TNO\EssifLab\Applications\Contracts\Application;
use TNO\EssifLab\Constants;
use TNO\EssifLab\ModelManagers\Contracts\BaseModelManager;
use TNO\EssifLab\ModelManagers\Exceptions\MissingIdentifier;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\Contracts\Utility;

class WordPressPostTypes extends BaseModelManager {
	private $relationKey;

	public function __construct(Application $application, Utility $utility) {
		parent::__construct($application, $utility);
		$this->relationKey = $application->getNamespace().'_'.Constants::MANAGER_TYPE_RELATION_ID_NAME;
	}

	function insert(Model $model): bool {
		return $this->utility->call(BaseUtility::CREATE_MODEL, $model->getAttributes());
	}

	function update(Model $model): bool {
		if (self::getModelId($model) < 0) {
			throw new MissingIdentifier($model->getSingularName());
		}

		return $this->insert($model);
	}

	function select(Model $model, array $criteria = []): array {
		$args = array_merge([
			Constants::MODEL_TYPE_INDICATOR => $model->getTypeName(),
		], $criteria);

		return $this->utility->call(BaseUtility::GET_MODELS, $args);
	}

	function delete(Model $model): bool {
		$id = $this->getGivenOrCurrentModelId($model);

		$result = $this->utility->call(BaseUtility::DELETE_MODEL, $id);

		$this->deleteAllRelations($model);

		return $result !== null || $result !== false;
	}

	function insertRelation(Model $from, Model $to): bool {
		$fromId = $this->getGivenOrCurrentModelId($from);
		$toId = $this->getModelId($to);

		if ($toId < 0) {
			throw new MissingIdentifier($to->getSingularName());
		}

		$fromTo = boolval($this->utility->call(BaseUtility::CREATE_MODEL_META, $fromId, $this->relationKey, $toId));
		$toFrom = boolval($this->utility->call(BaseUtility::CREATE_MODEL_META, $toId, $this->relationKey, $fromId));

		return $fromTo && $toFrom;
	}

	function deleteRelation(Model $from, Model $to): bool {
		$fromId = $this->getGivenOrCurrentModelId($from);
		$toId = $this->getModelId($to);

		if ($toId < 0) {
			throw new MissingIdentifier($to->getSingularName());
		}

		$fromTo = $this->utility->call(BaseUtility::DELETE_MODEL_META, $fromId, $this->relationKey, $toId);
		$toFrom = $this->utility->call(BaseUtility::DELETE_MODEL_META, $toId, $this->relationKey, $fromId);

		return $fromTo && $toFrom;
	}

	function deleteAllRelations(Model $model): bool {
		$id = $this->getGivenOrCurrentModelId($model);

		$relationIds = get_post_meta($id, $this->relationKey);
		foreach ($relationIds as $relationId) {
			$this->utility->call(BaseUtility::DELETE_MODEL_META, $relationId, $this->relationKey, $id);
		}

		return $this->utility->call(BaseUtility::DELETE_MODEL_META, $id, $this->relationKey);
	}

	function selectAllRelations(Model $model): array {
		$id = $this->getGivenOrCurrentModelId($model);

		$relationIds = $this->utility->call(BaseUtility::GET_MODEL_META, $id, $this->relationKey);

		$args = array_merge(Constants::TYPE_DEFAULT_TYPE_ARGS, [
			'post__in' => $relationIds,
		]);

		return empty($relationIds) ?  [] : $this->utility->call(BaseUtility::GET_MODELS, $args);
	}

	private function getGivenOrCurrentModelId(Model $model): int {
		$id = $this->getModelId($model);

		if ($id > 0) {
			return $id;
		}

		$currentModel = $this->utility->call(BaseUtility::GET_CURRENT_MODEL);
		$currentModelAttrs = empty($currentModel) ? [] : $currentModel->getAttributes();
		if (array_key_exists(Constants::TYPE_INSTANCE_IDENTIFIER_ATTR, $currentModelAttrs)) {
			return $currentModelAttrs[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR];
		}

		throw new MissingIdentifier($model->getSingularName());
	}

	private static function getModelId(Model $model): int {
		$attributes = $model->getAttributes();
		$idKey = Constants::TYPE_INSTANCE_IDENTIFIER_ATTR;

		return array_key_exists($idKey, $attributes) && intval($attributes[$idKey]) !== 0 ? intval($attributes[$idKey]) : -1;
	}
}