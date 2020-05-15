<?php

namespace TNO\EssifLab;

abstract class Constants {
	const TYPE_NAMESPACE = 'TNO\EssifLab\Models';

	const TYPE_INSTANCE_IDENTIFIER_ATTR = 'ID';

	const TYPE_INSTANCE_TITLE_ATTR = 'title';

	const TYPE_INSTANCE_DESCRIPTION_ATTR = 'description';

	const TYPE_NAME_SEPARATOR = '-';

	const TYPE_NAME_MAX_LENGTH = 20;

	const TYPE_ARG_HIDE_FROM_NAV = 'hidden';

	const TYPE_DEFAULT_SINGULAR_SUBSTR_LENGTH = -1;

	const TYPE_DEFAULT_PLURAL_SUFFIX = 's';

	const ACTION_NAME_ADD_RELATION = 'add_relation';

	const ACTION_NAME_REMOVE_RELATION = 'remove_relation';

	const MODEL_TYPE_INDICATOR = 'post_type';

	const MANAGER_TYPE_RELATION_ID_NAME = 'relation';

	const FIELD_TYPE_SIGNATURE = 'signature';

	const TYPE_DEFAULT_ATTRIBUTE_NAMES = [
		self::TYPE_INSTANCE_IDENTIFIER_ATTR,
		self::TYPE_INSTANCE_TITLE_ATTR,
        self::TYPE_INSTANCE_DESCRIPTION_ATTR,
	];

	const TYPE_DEFAULT_FIELDS = [
		self::TYPE_INSTANCE_TITLE_ATTR,
	];

	const TYPE_DEFAULT_TYPE_ARGS = [
		self::TYPE_ARG_HIDE_FROM_NAV => false,
	];

	const TYPE_LIST_DEFAULT_HEADINGS = [
		self::TYPE_INSTANCE_TITLE_ATTR,
		self::TYPE_INSTANCE_DESCRIPTION_ATTR,
	];

	public const ADMIN_MENU_CAPABILITY = 'manage_options';

	public const ADMIN_MENU_ICON_URL = 'dashicons-lock';
}