<?php


namespace TNO\ContactForm7\Tests\Stubs;

use TNO\ContactForm7\Utilities\Contracts\BaseUtility;
use TNO\ContactForm7\Utilities\Helpers\MockHelper;

class Utility extends BaseUtility
{
    private $history = [];

    /**
     * @param string $funcName
     * @return History[]
     */
    function getHistoryByFuncName(string $funcName): array
    {
        return array_slice(array_filter($this->history, function (History $history) use ($funcName) {
            return $history->getFuncName() === $funcName;
        }), 0);
    }

    public function addFormTag()
    {
        $histObj = new History("addFormTag");
        array_push($this->history, $histObj);
    }

    function getAllForms()
    {
        $histObj = new History("getAllForms");
        array_push($this->history, $histObj);
    }

    public function getTargetsFromForms(array $cf7Forms, string $post_title, int $id)
    {
        $histObj = new History("getTargetsFromForms");
        array_push($this->history, $histObj);
    }

    function insertHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->insert("hook", [$slug => $title]);
    }

    function insertTarget(int $id, string $title, string $hookSlug = self::SLUG)
    {
        $this->insert("target", [$id => $title], $hookSlug);
    }

    function insertInput(string $slug, string $title, int $targetId)
    {
        $this->insert("input", [$slug => $title], $targetId);
    }

    private function insert($suffix, ...$params)
    {
        $histObj = new History("insert" . ucfirst($suffix), $params );
        array_push($this->history, $histObj);
    }

    function deleteHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->delete("hook", [$slug => $title]);
    }

    function deleteTarget(int $id, string $title, string $hookSlug = self::SLUG)
    {
        $this->delete("target", [$id => $title], $hookSlug);
    }

    function deleteInput(string $slug, string $title, int $targetId)
    {
        $this->delete("input", [$slug => $title], $targetId);
    }

    private function delete($suffix, ...$params)
    {
        $histObj = new History("delete" . ucfirst($suffix), $params );
        array_push($this->history, $histObj);
    }

    function selectHook(string $slug = self::SLUG, string $title = self::TITLE)
    {
        $this->select("hook", [$slug => $title]);
    }

    function selectTarget(array $items = [], string $hookSlug = self::SLUG)
    {
        $mockHelper = new MockHelper();
        $target = $mockHelper->getTestTarget();
        $this->select("target", $items, $hookSlug, $target);
    }

    function selectInput(array $items = [], string $hookSlug = self::SLUG)
    {
        $mockHelper = new MockHelper();
        $input = $mockHelper->getTestInput();
        $this->select("input", $items, $hookSlug, $input);
    }

    private function select($suffix, ...$params)
    {
        $histObj = new History("select" . ucfirst($suffix), $params );
        array_push($this->history, $histObj);
    }

    function addEssifLabButton()
    {
        $histObj = new History("addEssifLabButton");
        array_push($this->history, $histObj);
    }

    function loadCustomScripts()
    {
        $histObj = new History("loadCustomScripts");
        array_push($this->history, $histObj);
    }

    function addActivateHook()
    {
        $histObj = new History("addActivateHook");
        array_push($this->history, $histObj);
    }

    function addDeactivateHook()
    {
        $histObj = new History("addDeactivateHook");
        array_push($this->history, $histObj);
    }
}