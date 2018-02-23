<?php

namespace MBT\Engine;

/**
 * Class Handlebars
 * @package wp-hbs-engine
 */

use LightnCandy\LightnCandy;

class Handlebars extends AbstractEngine {

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * Init Handlebars
	 */
	protected function initialize(){
		$helpers = apply_filters('MBT/Engine/Handlebars/Helpers', array(), $this);
		$flags = apply_filters('MBT/Engine/Handlebars/Flags', LightnCandy::FLAG_HANDLEBARSJS_FULL | LightnCandy::FLAG_RUNTIMEPARTIAL | LightnCandy::FLAG_NAMEDARG | LightnCandy::FLAG_ADVARNAME, $this);
		$partials = apply_filters('MBT/Engine/Handlebars/Partials', $this->getPartials(), $this);
		foreach ($partials as $base => &$partial) {
			$compiledPartial = $this->compilePartial($partial);
			$partial = include($compiledPartial);
		}
		$this->options = array(
			'partialresolver' => [$this, 'loadTemplate'],
			'helpers' => $helpers,
			'flags' => $flags,
			'partials' => $partials
		);
		$this->extension = apply_filters('MBT/Engine/Handlebars/Extension', 'hbs');
		do_action('MBT/Engine/Handlebars/Init', $this);
	}

	/**
	 * Loads Templates
	 *
	 * @param object $context
	 * @param string $template
	 * @return string
	 */
	public function loadTemplate($context, $template) {
		if (!strpos($template, '.' . $this->extension)) {
			$template .= '.' . $this->extension;
		}
		$this->template = $this->templatesDirectory.$template;
		if (file_exists($this->template)) {
			return file_get_contents($this->template);
		}
		return sprintf(__("<!-- Template %s not found -->", 'mbt'), $template);
	}

	/**
	 * Loads Partial
	 *
	 * @param string $partial
	 * @return string
	 */
	private function loadPartial($partial) {
		if (file_exists($partial)) {
			return file_get_contents($partial);
		}
		return sprintf(__("<!-- Partial %s not found -->", 'mbt'), $partial);
	}

	/**
	 * Get partials from $this->templatesDirectory recursively
	 *
	 * @param bool|string $dir
	 * @param bool|string $base
	 * @return array
	 */
	private function getPartials($dir = false, $base = false) {
		$partials = array();
		if (false === $dir) {
			$dir = $this->templatesDirectory.'partials/';
			$base = trailingslashit(basename($dir));
		}
		foreach (new \DirectoryIterator($dir) as $filePointer) {
			if($filePointer->isDot()) continue;
			if($filePointer->isDir()) {
				$partials = array_merge($partials, $this->getPartials($dir.'/'.$filePointer->getFilename().'/', $base.'/'.$filePointer->getFilename().'/'));
			} else {
				$partials[$base.$filePointer->getFilename()] = $dir.$filePointer->getFilename();
			}
		}
		return $partials;
	}

	/**
	 * Compile Template on Demand
	 *
	 * @param string $template
	 * @return string path to compiled and cached template
	 */
	private function compile($template) {
		$templateContent = $this->loadTemplate(null, $template);
		$compiledTemplate = $this->cacheDirectory.md5_file($this->template).'.php';
		if (!file_exists($compiledTemplate)) {
			$php = LightnCandy::compile(
				$templateContent,
				$this->options
			);
			file_put_contents($compiledTemplate, '<?php ' . $php . '?>');
		}
		return $compiledTemplate;
	}

	/**
	 * Compile Partial on Demand
	 *
	 * @param string $partial
	 * @return string path to compiled and cached partial
	 */
	private function compilePartial($partial) {
		$partialContent = $this->loadPartial($partial);
		$compiledPartial = $this->cacheDirectory.md5_file($partial).'.php';
		if (!file_exists($compiledPartial)) {
			$php = LightnCandy::compilePartial($partialContent);
			file_put_contents($compiledPartial, '<?php ' . $php . '?>');
		}
		return $compiledPartial;
	}

	/**
	 * Parses the $template with $data
	 *
	 * @param string $template
	 * @param array $data
	 *
	 * @return string
	 */
	public function parse($template, $data = array()) {
		$compiledTemplate = $this->compile($template);
		$renderer = include($compiledTemplate);

		$defaultData = apply_filters("MBT/Engine/Handlebars/DefaultData", array(), $template);
		$data = array_merge($data, $defaultData);
		$data = apply_filters("MBT/Engine/Handlebars/Data", $data, $template);

		return $renderer($data);
	}

	/**
	 * Renders an Instance of the Handlebars Engine
	 *
	 * @param $template
	 * @param array $data
	 * @param bool $echo
	 * @param bool|string $templateDir
	 * @param bool|string $cacheDir
	 *
	 * @return bool
	 */
	static function render($template, $data = array(), $echo = true, $templateDir = false, $cacheDir = false) {
		if (false === $templateDir) {
			$templateDir = get_stylesheet_directory().'/src/templates/';
		}
		if (false === $cacheDir) {
			$cacheDir = MBT_ENGINE_DIR.'cache/';
		}

		$hbs = new Handlebars($templateDir, $cacheDir);
		$html = $hbs->parse($template, $data);
		$html = apply_filters("MBT/Engine/Handlebars/Html", $html, $template, $data);
		if (true === $echo) {
			echo $html;
		}
		return $echo;
	}
}
