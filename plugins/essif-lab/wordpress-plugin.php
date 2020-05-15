<?php
/**
 * Plugin Name: eSSIF-Lab
 * Plugin URI: https://github.com/LSVH/hbo-ict-inno-s2-tno
 * Description: The purpose of the eSSIF-Lab is to specify, develop and validate technological and non-technological means that support people, businesses and governments to think about, design and operate their (information) processes and (electronically) conduct business transactions with one another.
 * Author: Duur Klop, Luuk van Houdt, Ruben Sikkens, Ufuk Altinçöp en Weis Mateen
 * TextDomain: tno-essif-lab
 * Version: 1.0
 */

defined('ABSPATH') or die();

$classAutoloader = __DIR__.'/vendor/autoload.php';
if (file_exists($classAutoloader)) {
	require_once($classAutoloader);
}

use TNO\EssifLab\Applications\Contracts\Application;
use TNO\EssifLab\Applications\Plugin;
use TNO\EssifLab\Integrations\Contracts\Integration;
use TNO\EssifLab\Integrations\WordPress;
use TNO\EssifLab\ModelManagers\Contracts\ModelManager;
use TNO\EssifLab\ModelManagers\WordPressPostTypes;
use TNO\EssifLab\ModelRenderers\Contracts\ModelRenderer;
use TNO\EssifLab\ModelRenderers\WordPressMetaBox;
use TNO\EssifLab\Utilities\Contracts\Utility;
use TNO\EssifLab\Utilities\WP;

$wpPluginApi = ABSPATH.'wp-admin/includes/plugin.php';
if (! function_exists('get_plugin_data') && file_exists($wpPluginApi)) {
	require_once($wpPluginApi);
}

$getApplication = function (): Application {
	$pluginData = get_plugin_data(__FILE__, false, false);

	$name = function () use ($pluginData): string {
		return array_key_exists('Name', $pluginData) ? $pluginData['Name'] : 'App';
	};

	$namespace = function () use ($pluginData): string {
		return array_key_exists('TextDomain', $pluginData) ? $pluginData['TextDomain'] : 'MyApp';
	};

	$appDir = function (): string {
		return plugin_dir_path(__FILE__);
	};

	return new Plugin($name(), $namespace(), $appDir());
};

$getUtility = function (): Utility {
	return new WP();
};

$getModelRenderer = function (): ModelRenderer {
	return new WordPressMetaBox();
};

$getModelManager = function (Application $application) use ($getUtility): ModelManager {
	return new WordPressPostTypes($application, $getUtility());
};

$getIntegration = function (Application $application) use (
	$getModelManager,
	$getModelRenderer,
	$getUtility
): Integration {
	return new WordPress($application, $getModelManager($application), $getModelRenderer(), $getUtility());
};

$getIntegration($getApplication())->install();