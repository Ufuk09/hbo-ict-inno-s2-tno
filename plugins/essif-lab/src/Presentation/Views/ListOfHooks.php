<?php

namespace TNO\EssifLab\Presentation\Views;

use TNO\EssifLab\Contracts\Abstracts\View;
use TNO\EssifLab\Presentation\Components\FormControl;
use TNO\EssifLab\Presentation\Components\PostList;

class ListOfHooks extends View {
	private $headings = ['context', 'target'];

	private $items = [];

	private $name;

	public function __construct($pluginData, $args) {
		parent::__construct($pluginData, $args);

		$this->headings = $this->getArg('headings', $this->headings);
		$this->items = $this->getArg('items', $this->items);
		$this->name = $this->getDomain().':'.substr(strrchr(get_class($this), '\\'), 1);
	}

	public function render(): string {
		return $this->getPostList()->render();
	}

	private function getPostList(): PostList {
		$delete = function ($id) {
			return $this->getDeleteFormControl($id)->render();
		};

		return new PostList($this, [
			'headings' => $this->headings,
			'items' => $this->items,
			'itemActions' => [$delete],
		]);
	}

	private function getDeleteFormControl($id): FormControl {
		return new FormControl($this, [
			'name' => $this->name,
			'fields' => [
				[
					'name' => 'id',
					'value' => $id,
					'type' => 'hidden',
				],
				[
					'children' => __('Delete', $this->getDomain()),
					'name' => 'action',
					'value' => 'delete',
					'type' => 'button',
					'class' => 'button-link',
				],
			],
		]);
	}
}