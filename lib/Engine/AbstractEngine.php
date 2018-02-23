<?php

namespace MBT\Engine;

/**
 * Class AbstractEngine
 * @package wp-hbs-engine
 */

if (!class_exists('AbstractEngine')) {
	abstract class AbstractEngine {
		/**
		 * @var string
		 */
		protected $templatesDirectory;
		/**
		 * @var string
		 */
		protected $cacheDirectory;

		/**
		 * Initialize engine.
		 *
		 * @param string $templatesDirectory The directory with views.
		 * @param string $cacheDirectory The directory for cache (Optional)
		 */
		public function __construct($templatesDirectory, $cacheDirectory = '')
		{
			$subDirectory = str_replace('MBT\\Engine\\', '', get_class($this));
			$subDirectory = strtolower($subDirectory);
			$this->templatesDirectory = trailingslashit($templatesDirectory);
			$this->cacheDirectory = trailingslashit($cacheDirectory) . $subDirectory . '/';
			if (!file_exists($this->cacheDirectory)) {
				mkdir($this->cacheDirectory, 0755, true);
			}
			$this->initialize();
		}

		/**
		 * Initializes the templating engine.
		 */
		abstract protected function initialize();

		/**
		 * Parses the template as HTML.
		 *
		 * @param string $template
		 * @param array $data
		 * @return string of generated template
		 */
		abstract public function parse($template, $data = array());
	}
}
