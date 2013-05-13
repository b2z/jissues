<?php
/**
 * @package     JTracker\View
 * @subpackage  Renderer
 *
 * @copyright   Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tracker\View\Renderer;

/**
 * Twig class for rendering output.
 *
 * @package Joomla\Tracker\View\Renderer
 *
 * @since  1.0
 */
class Twig extends \Twig_Environment
{
	/**
	 * The renderer default configuration parameters.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $config = array(
		'themes_base_dir'   => 'templates/',
		'template_file_ext' => '.twig',
		'twig_cache_dir'    => 'cache/twig/',
		'delimiters'        => array(
			'tag_comment'  => array('{#', '#}'),
			'tag_block'    => array('{%', '%}'),
			'tag_variable' => array('{{', '}}')
		),
		'environment'       => array()
	);

	/**
	 * The data for the renderer.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $data = array();

	/**
	 * The array of templates location paths.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $templateLocations = array();

	/**
	 * Current template name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $template;

	/**
	 * Loads template from the filesystem.
	 *
	 * @var \Twig_Loader_Filesystem
	 * @since  1.0
	 */
	private $twigLoader;

	/**
	 * Instantiate the renderer.
	 *
	 * @param  array  $config  The array of configuration parameters.
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		// Merge the config.
		$this->config = array_merge($this->config, $config);

		// Set the templates location path.
		$this->setTemplateLocations();

		try
		{
			$this->twigLoader = new \Twig_Loader_Filesystem($this->templateLocations);
		}
		catch (\Twig_Error_Loader $e)
		{
			echo $e->getRawMessage();
		}

		parent::__construct($this->twigLoader, $this->config['environment']);
 	}

	/**
	 * Get the Lexer instance.
	 *
	 * @return  \Twig_LexerInterface  A Twig_LexerInterface instance.
	 *
	 * @since   1.0
	 */
 	public function getLexer()
	{
		if (null === $this->lexer)
		{
			$this->lexer = new \Twig_Lexer($this, $this->config['delimiters']);
		}

		return $this->lexer;
	}

	/**
	 * Set the data for the renderer.
	 *
	 * @param   mixed    $key     The variable name or an array of variable names with values.
	 * @param   mixed    $value   The value.
	 * @param   boolean  $global  Is this a global variable?
	 *
	 * @return  object   Instance of this class.
	 *
	 * @since   1.0
	 */
	public function set($key, $value = null, $global = false)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v) $this->set($k, $v, $global);
		}
		else
		{
			if ($global)
			{
				$this->addGlobal($key, $value);
			}
			else
			{
				$this->data[$key] = $value;
			}
		}

		return $this;
	}

	/**
	 * Unset a particular variable.
	 *
	 * @param   mixed  $key  The variable name.
	 *
	 * @return  object  Instance of this class.
	 *
	 * @since   1.0
	 */
	public function unset_data($key)
	{
		if (array_key_exists($key, $this->data))
		{
			unset($this->data[$key]);
		}

		return $this;
	}

	/**
	 * Set the template.
	 *
	 * @param   string  $name  The name of the template file.
	 *
	 * @return  object  Instance of this class.
	 *
	 * @since  1.0
	 */
	public function setTemplate($name)
	{
		$this->template = $name;

		return $this;
	}

	/**
	 * Render and return compiled HTML.
	 *
	 * @param   string  $template  The template file name.
	 * @param   array   $data      An array of data to pass to the template.
	 *
	 * @return  string  Compiled HTML.
	 *
	 * @since   1.0
	 */
	public function render($template = '', array $data = array())
	{
		if (!empty($template))
		{
			$this->setTemplate($template);
		}

		if (!empty($data))
		{
			$this->set($data);
		}

		try
		{
			return $this->load()->render($this->data);
		}
		catch (\Twig_Error_Loader $e)
		{
			echo $e->getRawMessage();
		}
	}

	/**
	 * Display the compiled HTML content.
	 *
	 * @param   string  $template  The template file name.
	 * @param   array   $data      An array of data to pass to the template.
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function display($template = '', array $data = array())
	{
		if (!empty($template))
		{
			$this->setTemplate($template);
		}

		if (!empty($data))
		{
			$this->set($data);
		}

		try
		{
			$this->load()->display($this->data);
		}
		catch (\Twig_Error_Loader $e)
		{
			echo $e->getRawMessage();
		}
	}

	/**
	 * Get the current template name.
	 *
	 * @return  string  The name of the currently loaded template file (without the extension).
	 *
	 * @since  1.0
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Set the templates location paths.
	 *
	 * @param   string  $path             Templates location path.
	 * @param   bool    $overrideBaseDir  If true path can be outside themes base directory.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setTemplateLocations($path = '', $overrideBaseDir = false)
	{
		if (!$overrideBaseDir)
		{
			$this->templateLocations[] = $this->config['themes_base_dir'] . $path;
		}
		else
		{
			$this->templateLocations[] = $path;
		}

		// Reset the paths if needed.
		if (is_object($this->twigLoader))
		{
			$this->twigLoader->setPaths($this->templateLocations);
		}
	}

	/**
	 * Load the template and return an output object.
	 *
	 * @return  object  Output object.
	 *
	 * @since  1.0
	 */
	private function load()
	{
		return $this->loadTemplate($this->getTemplate() . $this->config['template_file_ext']);
	}
}
