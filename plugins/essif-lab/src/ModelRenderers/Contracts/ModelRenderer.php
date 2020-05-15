<?php

namespace TNO\EssifLab\ModelRenderers\Contracts;

use TNO\EssifLab\Integrations\Contracts\Integration;
use TNO\EssifLab\Models\Contracts\Model;

interface ModelRenderer {
	function renderListAndFormView(Integration $integration, Model $model, array $attrs = []): string;

	function renderFieldSignature(Integration $integration, Model $model, array $attrs = []): string;
}