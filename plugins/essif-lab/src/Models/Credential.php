<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\BaseModel;

class Credential extends BaseModel {
	protected $singular = 'credential';

    protected $fields = [
        Constants::FIELD_TYPE_IMMUTABLE
    ];

	protected $relations = [
		Input::class,
		Issuer::class,
		Schema::class
	];
}