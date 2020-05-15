<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\BaseModel;

class Hook extends BaseModel {
	protected $singular = 'hook';

	protected $relations = [
		Target::class
	];

	protected $typeArgs = [
		Constants::TYPE_ARG_HIDE_FROM_NAV => true,
	];
}