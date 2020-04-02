<?php

namespace TNO\EssifLab\Abstracts;

defined('ABSPATH') or die();

use TNO\EssifLab\Interfaces\Core as ICore;
use TNO\EssifLab\Interfaces\CoreSettings as ICoreSettings;

abstract class Core implements ICore, ICoreSettings
{
    /**
     * The full name of the plugin as shown in the plugins list.
     *
     * @var string
     */
    private $name;

    /**
     * The version number of this plugin.
     *
     * @var string
     */
    private $version;

    /**
     * The text domain mainly used to identify translatable strings.
     *
     * @var string
     */
    private $domain;

    /**
     * The absolute path to the plugin directory.
     *
     * @var string
     */
    private $path;

    /**
     * Custom options set by the administrator of the plugin.
     *
     * @var array
     */
    private $options = [];

    /**
     * All plugin data that initiated the plugin.
     *
     * @var array
     */
    private $plugin_data = [];

    /**
     * CoreAbstract constructor.
     *
     * @param array $plugin_data
     */
    public function __construct($plugin_data = [])
    {
        $this->plugin_data = $plugin_data;
        $this->name = $this->get_plugin_data_value('Name');
        $this->version = $this->get_plugin_data_value('Version');
        $this->domain = $this->get_plugin_data_value('TextDomain');
        $this->path = $this->get_plugin_data_value('PluginPath');
        $this->options = function_exists('get_option') ? get_option($this->get_domain()) : [];
    }

    /**
     * The full name of the plugin as shown in the plugins list.
     *
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * The version number of this plugin.
     *
     * @return string
     */
    public function get_version(): string
    {
        return $this->version;
    }

    /**
     * The text domain mainly used to identify translatable strings.
     *
     * @return string
     */
    public function get_domain(): string
    {
        return $this->domain;
    }

    /**
     * The absolute path to the plugin directory.
     *
     * @return string
     */
    public function get_path(): string
    {
        return $this->path;
    }

    /**
     * Get all the options
     *
     * @return array
     */
    public function get_options(): array
    {
        $options = empty($this->options) ? [] : $this->options;
        return array_merge([
            self::FIELD_MESSAGE => $this->get_option_default(self::FIELD_MESSAGE),
        ], $options);
    }

    /**
     * Retrieve an option from the `$options` array.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get_option($key)
    {
        $options = $this->get_options();
        return is_array($options) && array_key_exists($key, $options) ?
            $options[$key] : $this->get_option_default($key);
    }

    /**
     * Check if an option exists
     *
     * @param string $key
     * @return mixed
     */
    public function has_option($key) {
        return is_array($this->options) && array_key_exists($key, $this->options);
    }

    /**
     * Add or update an option
     *
     * @param string $key
     * @param null|mixed $value
     * @return void
     */
    public function update_option($key, $value = null) {
        $options = $this->get_options();
        $options[$key] = $value;
        update_option($this->get_domain(), $options);
        $this->options = $options;
    }

    /**
     * Batch add or update options
     *
     * @param $options
     * @return void
     */
    public function update_options($options) {
        $options = array_merge($this->get_options(), $options);
        update_option($this->get_domain(), $options);
        $this->options = $options;
    }

    /**
     * Get the default value of an option.
     *
     * @param $key
     * @return mixed
     */
    public function get_option_default($key)
    {
        switch ($key) {
            case self::FIELD_MESSAGE:
                return 'Hello, World!';

            default:
                return '';
        }
    }

    /**
     * Get the plugin data
     *
     * @return array
     */
    public function get_plugin_data(): array
    {
        return $this->plugin_data;
    }

    /**
     * Retrieve a value of specific piece of `$plugin_data`.
     *
     * @param string $key
     * @return mixed|null
     */
    private function get_plugin_data_value($key)
    {
        return is_array($this->plugin_data) && array_key_exists($key, $this->plugin_data) ? $this->plugin_data[$key] : null;
    }
}