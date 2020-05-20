<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Models\Contracts\BaseModel;

class ValidationPolicy extends BaseModel {
	protected $singular = 'validation policy';

	protected $plural = 'validation policies';

	protected $relations = [
		Hook::class,
		Credential::class,
	];
}