<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\BaseModel;

class Input extends BaseModel {
	protected $singular = 'input';

	protected $typeArgs = [
		Constants::TYPE_ARG_HIDE_FROM_NAV => true
	];

	protected $attributeNames = [
		Constants::TYPE_INSTANCE_SLUG_ATTR,
	];
}