<?php

namespace TNO\EssifLab\Tests\Stubs;

use TNO\EssifLab\Constants;
use TNO\EssifLab\ModelManagers\Contracts\BaseModelManager;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Tests\Stubs\Model as ConcreteModel;

class ModelManager extends BaseModelManager {
	function insert(Model $model): bool {
		return true;
	}

	function delete(Model $model): bool {
		return true;
	}

	function update(Model $model): bool {
		return true;
	}

	function select(Model $model, array $criteria = []): array {
		return [
			new ConcreteModel([
				Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => 1,
				Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
				Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world',
			])
		];
	}

	function insertRelation(Model $from, Model $to): bool {
		return true;
	}

	function deleteRelation(Model $from, Model $to): bool {
		return true;
	}

	function deleteAllRelations(Model $model): bool {
		return true;
	}

	function selectAllRelations(Model $from, Model $to): array {
        return [
            new ConcreteModel([
                Constants::TYPE_INSTANCE_IDENTIFIER_ATTR => 1,
                Constants::TYPE_INSTANCE_TITLE_ATTR => 'hello',
                Constants::TYPE_INSTANCE_DESCRIPTION_ATTR => 'world',
            ])
        ];
	}
}