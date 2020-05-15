<?php

namespace TNO\EssifLab\Models;

use TNO\EssifLab\Constants;
use TNO\EssifLab\Models\Contracts\BaseModel;

class Issuer extends BaseModel {
	protected $singular = 'issuer';

	protected $fields = [
		Constants::FIELD_TYPE_SIGNATURE
	];
}