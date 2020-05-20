<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\BaseModel;

class Schema extends BaseModel {
	protected $singular = 'schema';

	protected $fields = [
		Constants::FIELD_TYPE_SCHEMA_LOADER
	];
}