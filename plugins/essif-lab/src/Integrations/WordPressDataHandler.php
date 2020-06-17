<?php

namespace TNO\EssifLab\Integrations;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Integrations\Contracts\BaseIntegration;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\BaseUtility;
use TNO\EssifLab\Utilities\WP;

class WordPressDataHandler extends BaseIntegration {
	function install(): void {
		$model = $this->utility->call(BaseUtility::GET_CURRENT_MODEL);
		$namespace = $this->application->getNamespace();
		$data = array_key_exists($namespace, $_POST) ? $_POST[$namespace] : [];
		if (! empty(is_array($data) ? array_filter($data) : $data)) {
			$this->handleModelData($model, $data);
			$this->handleModelRelationData($model, $data);
		}
	}

	private function handleModelData(Model $model, array $data): void {
		$hook = 'save_post_'.$model->getTypeName();
		$attrs = $model->getAttributes();
		$id = array_key_exists(Constants::TYPE_INSTANCE_IDENTIFIER_ATTR, $attrs) ? $attrs[Constants::TYPE_INSTANCE_IDENTIFIER_ATTR] : null;
		$newData = $this->handleModelFieldData($model, $data);
		if (! empty($id) && ! empty($newData)) {
			$this->utility->call(WP::REMOVE_ALL_ACTIONS_AND_EXEC, $hook, function () use ($id, $newData) {
				$post = new \stdClass();
				$post->ID = $id;
				$post->post_content = json_encode($newData);
				$this->utility->call(BaseUtility::UPDATE_MODEL, $post);
			});
		}
	}

	private function handleModelFieldData(Model $model, array $data): array {
		$attrs = $model->getAttributes();
		$description = array_key_exists(Constants::TYPE_INSTANCE_DESCRIPTION_ATTR, $attrs) ? $attrs[Constants::TYPE_INSTANCE_DESCRIPTION_ATTR] : null;
		$old = json_decode($description, true);
		$new = is_array($old) ? $old : [];

        if (array_key_exists(Constants::FIELD_TYPE_SIGNATURE, $data)) {
            $new = $this->handleModelFieldDataSignature($data, $new);
        }

        $immutable = array_key_exists(Constants::FIELD_TYPE_IMMUTABLE, $data) ? true : false;
        $this->handleModelFieldDataImmutable($model, $immutable);

		return $new;
	}

	private function handleModelFieldDataSignature(array $data, array $new): array {
		$new[Constants::FIELD_TYPE_SIGNATURE] = $data[Constants::FIELD_TYPE_SIGNATURE];

		return $new;
	}

    private function handleModelFieldDataImmutable(Model $model, bool $immutable): void {
        $this->manager->saveImmutable($model, $immutable);
    }

	private function handleModelRelationData(Model $model, array $data): void {
		$toBeAdded = array_key_exists(Constants::ACTION_NAME_ADD_RELATION, $data) ? $data[Constants::ACTION_NAME_ADD_RELATION] : [];
		$modelTypeToAdd = array_key_exists(Constants::ACTION_NAME_RELATION_ACTION, $data) ? $data[Constants::ACTION_NAME_RELATION_ACTION] : null;
		$idToAdd = is_array($toBeAdded) && array_key_exists($modelTypeToAdd, $toBeAdded) ? $toBeAdded[$modelTypeToAdd] : null;
		if (! empty($idToAdd)) {
			$this->handleAddRelation($model, $idToAdd);
		}

		$idToRemove = array_key_exists(Constants::ACTION_NAME_REMOVE_RELATION, $data) ? $data[Constants::ACTION_NAME_REMOVE_RELATION] : null;
		if (! empty($idToRemove)) {
			$this->handleRemoveRelation($model, $idToRemove);
		}
	}

	private function handleAddRelation(Model $from, int $id): void {
		$to = $this->utility->call(BaseUtility::GET_MODEL, $id);
		if (!empty($to)) {
			$relations = $this->manager->selectAllRelations($from, $to);

			if (! in_array($to, $relations)) {
				$this->manager->insertRelation($from, $to);
			}
		}
	}

	private function handleRemoveRelation(Model $from, int $id): void {
		$to = $this->utility->call(BaseUtility::GET_MODEL, $id);
		if (!empty($to)) {
			$relations = $this->manager->selectAllRelations($from, $to);

			if (in_array($to, $relations)) {
				$this->manager->deleteRelation($from, $to);
			}
		}
	}
}