<?php

namespace TNO\EssifLab\Tests\Stubs;

use TNO\EssifLab\Integrations\Contracts\Integration;
use TNO\EssifLab\Models\Contracts\Model;

class ModelRenderer implements \TNO\EssifLab\ModelRenderers\Contracts\ModelRenderer {
	const LIST_AND_FORM_VIEW_RENDERER = 'ListAndFormView';

	const FIELD_SIGNATURE_RENDERER = 'FieldSignature';

	const FIELD_SCHEMA_LOADER_RENDERER = 'FieldSchemaLoaderRenderer';

    const FIELD_IMMUTABLE_RENDERER = 'FieldImmutableRenderer';

	private $isCalled = [];

	/**
	 * @var Integration[]
	 */
	private $lastIntegrationItsCalledWith = [];

	/**
	 * @var Model[]
	 */
	private $lastModelItsCalledWith = [];

	/**
	 * @var array
	 */
	private $lastAttrsItsCalledWith = [];

	function renderListAndFormView(Integration $integration, Model $model, array $attrs = []): string {
		return $this->callRenderer(self::LIST_AND_FORM_VIEW_RENDERER, $integration, $model, $attrs);
	}

	function renderFieldSignature(Integration $integration, Model $model, array $attrs = []): string {
		return $this->callRenderer(self::FIELD_SIGNATURE_RENDERER, $integration, $model, $attrs);
	}

    function renderSchemaLoader(Integration $integration, Model $model, array $attrs = []): string {
		return $this->callRenderer(self::FIELD_SCHEMA_LOADER_RENDERER, $integration, $model, $attrs);
	}

    function renderFieldImmutable(Integration $integration, Model $model, array $attrs = []): string
    {
        return $this->callRenderer(self::FIELD_IMMUTABLE_RENDERER, $integration, $model, $attrs);
    }

    public function isCalled(string $renderer): bool {
		return array_key_exists($renderer, $this->isCalled) && boolval($this->isCalled[$renderer]);
	}

    public function getIntegrationItsCalledWith(string $renderer): ?Integration {
		return array_key_exists($renderer, $this->lastIntegrationItsCalledWith)
			? $this->lastIntegrationItsCalledWith[$renderer] : null;
	}

    public function getModelItsCalledWith(string $renderer): ?Model {
		return array_key_exists($renderer, $this->lastModelItsCalledWith)
			? $this->lastModelItsCalledWith[$renderer] : null;
	}

    public function getAttrsItsCalledWith(string $renderer): ?array {
		return array_key_exists($renderer, $this->lastAttrsItsCalledWith)
			? $this->lastAttrsItsCalledWith[$renderer] : null;
	}

    private function callRenderer(string $renderer, Integration $integration, Model $model, array $attrs = []): string {
		$this->isCalled[$renderer] = true;
		$this->lastIntegrationItsCalledWith[$renderer] = $integration;
		$this->lastModelItsCalledWith[$renderer] = $model;
		$this->lastAttrsItsCalledWith[$renderer] = $attrs;
		return "@ModelRendererStub\n";
	}
}