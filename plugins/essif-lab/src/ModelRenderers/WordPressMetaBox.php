<?php

namespace TNO\EssifLab\ModelRenderers;

use TNO\EssifLab\Integrations\Contracts\Integration;
use TNO\EssifLab\Models\Contracts\Model;
use TNO\EssifLab\Views\SchemaLoaderField;
use TNO\EssifLab\Views\SignatureField;
use TNO\EssifLab\Views\TypeList;
use TNO\EssifLab\Views\ImmutableField;

class WordPressMetaBox implements Contracts\ModelRenderer {
	function renderListAndFormView(Integration $integration, Model $model, array $attrs = []): string {
		$view = new TypeList($integration, $model, $attrs);
		return $view->render();
	}

	function renderFieldSignature(Integration $integration, Model $model, array $attrs = []): string {
		$view = new SignatureField($integration, $model, $attrs);
		return $view->render();
	}

	function renderSchemaLoader(Integration $integration, Model $model, array $attrs = []): string {
		$view = new SchemaLoaderField($integration, $model, $attrs);
		return $view->render();
	}

    function renderFieldImmutable(Integration $integration, Model $model, array $attrs = []): string {
        $view = new ImmutableField($integration, $model, $attrs);
        return $view->render();
    }
}