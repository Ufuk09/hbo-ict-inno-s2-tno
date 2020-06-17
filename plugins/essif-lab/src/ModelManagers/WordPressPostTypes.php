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
		return $this->utility->call(BaseUtility::CREATE_MODEL, $model);
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

        $resultRelations = $this->deleteAllRelations($model);

        $result = $this->utility->call(BaseUtility::DELETE_MODEL, $id);

        return ($result !== null || $result !== false) && ($resultRelations !== null || $resultRelations !== false);
	}

    function saveImmutable(Model $model, bool $immutable) {
        $this->utility->call(BaseUtility::UPDATE_MODEL_META, $this->getGivenOrCurrentModelId($model), "essif-lab_immutable", $immutable);
    }

    function getImmutable(Model $model) : bool {
        return $this->utility->call(BaseUtility::GET_MODEL_META, $this->getGivenOrCurrentModelId($model), "essif-lab_immutable")[0];
    }

    function insertRelation(Model $from, Model $to): bool {
		$fromId = $this->getGivenOrCurrentModelId($from);
		$toId = $this->getModelId($to);

		if ($toId < 0) {
			throw new MissingIdentifier($to->getSingularName());
		}

		$relationFromToKey = $this->relationKey.$to->getTypeName();
		$fromTo = boolval($this->utility->call(BaseUtility::CREATE_MODEL_META, $fromId, $relationFromToKey, $toId));

		$relationToFromKey = $this->relationKey.$from->getTypeName();
		$toFrom = boolval($this->utility->call(BaseUtility::CREATE_MODEL_META, $toId, $relationToFromKey, $fromId));

		return $fromTo && $toFrom;
	}

    function deleteRelation(Model $from, Model $to): bool {
		$fromId = $this->getGivenOrCurrentModelId($from);
		$toId = $this->getModelId($to);

		if ($toId < 0) {
			throw new MissingIdentifier($to->getSingularName());
		}

		$relationFromToKey = $this->relationKey.$to->getTypeName();
		$fromTo = $this->utility->call(BaseUtility::DELETE_MODEL_META, $fromId, $relationFromToKey, $toId);

		$relationToFromKey = $this->relationKey.$from->getTypeName();
		$toFrom = $this->utility->call(BaseUtility::DELETE_MODEL_META, $toId, $relationToFromKey, $fromId);

		return $fromTo && $toFrom;
	}

    function deleteAllRelations(Model $from): bool {
		$result = true;
		$fromId = $this->getGivenOrCurrentModelId($from);
        BaseModelManager::forEachModel($from->getRelations(), function (Model $to) use (&$result, $from, $fromId) {
            if (!empty($toIds = $this->utility->call(BaseUtility::GET_MODEL_META, $fromId, $this->relationKey.$to->getTypeName()))) {
                $relationFromToKey = $this->relationKey.$to->getTypeName();
                $relationToFromKey = $this->relationKey.$from->getTypeName();
                $result = $this->utility->call(BaseUtility::DELETE_MODEL_META, $fromId, $relationFromToKey);
                foreach ($toIds as $id) {
                    if ($result) {
                        $result = $this->utility->call(BaseUtility::DELETE_MODEL_META, $id, $relationToFromKey, $fromId);
                    }
                }
            }
        });

		return $result;
	}

    function selectAllRelations(Model $from, Model $to): array {
		$fromId = $this->getGivenOrCurrentModelId($from);

		$relationFromToKey = $this->relationKey.$to->getTypeName();
		$relationIds = $this->utility->call(BaseUtility::GET_MODEL_META, $fromId, $relationFromToKey);

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