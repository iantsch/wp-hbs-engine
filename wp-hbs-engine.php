<?php
/*
 * Plugin Name: WordPress Handlebars Engine
 * Plugin URI: https://github.com/iantsch/wp-hbs-engine
 * Description: Renders Handlebars templates within your WordPress Theme/Plugin.
 * Version: 0.0.1
 * Author: Christian Tschugg
 * Author URI: http://mbt.wien
 * Copyright: Christian Tschugg
 * Text Domain: mbt
*/

namespace MBT {
	define( __NAMESPACE__ . '_ENGINE_DIR', __DIR__ . DIRECTORY_SEPARATOR );
}

namespace {
	use MBT\Engine\Handlebars;

	if (file_exists( MBT_ENGINE_DIR . 'vendor/autoload.php' )) {
		require MBT_ENGINE_DIR . "vendor/autoload.php";
	}

	if (!function_exists('get_hbs_template')) {
		/**
		 * @param string $template
		 * @param array $data
		 * @param bool $templateDir
		 * @param bool $cacheDir
		 * @return bool
		 */
		function get_hbs_template($template, $data, $templateDir = false, $cacheDir = false) {
			return Handlebars::render($template, $data, false, $templateDir, $cacheDir);
		}
	}

	if (!function_exists('the_hbs_template')) {
		/**
		 * @param string $template
		 * @param array $data
		 * @param bool $templateDir
		 * @param bool $cacheDir
		 * @return bool
		 */
		function the_hbs_template($template, $data, $templateDir = false, $cacheDir = false) {
			Handlebars::render($template, $data, true, $templateDir, $cacheDir);
		}
	}
}


