<?php

namespace TNO\EssifLab\ModelManagers\Contracts;

use TNO\EssifLab\Applications\Contracts\Application;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Utilities\Contracts\Utility;

interface ModelManager {
	public function __construct(Application $application, Utility $utility);

	function insert(Model $model): bool;

	function delete(Model $model): bool;

	function update(Model $model): bool;

	function select(Model $model, array $criteria = []): array;

	function saveImmutable(Model $model, bool $immutable);

    function getImmutable(Model $model) : bool;

	function insertRelation(Model $from, Model $to): bool;

	function deleteRelation(Model $from, Model $to): bool;

	function deleteAllRelations(Model $model): bool;

	function selectAllRelations(Model $from, Model $to): array;
}