<?php
/**
 * @package    JTracker\View
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tracker\View;

use Joomla\Factory;
use Joomla\Language\Text;
use Joomla\Model\ModelInterface;
use Joomla\Tracker\Application\TrackerApplication;
use Joomla\Tracker\Authentication\GitHub\GitHubLoginHelper;
use Joomla\View\AbstractView;
use Joomla\Tracker\View\Renderer\TrackerExtension;

/**
 * Tracker Html View class.
 *
 * @package Joomla\Tracker\View
 */
abstract class AbstractTrackerHtmlView extends AbstractView
{
	/**
	 * The view layout.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $layout = 'index';

	/**
	 * The view template engine.
	 *
	 * @var    RendererInterface
	 * @since  1.0
	 */
	protected $renderer = null;

	/**
	 * Method to instantiate the view.
	 *
	 * @param   ModelInterface  $model           The model object.
	 * @param   string|array    $templatesPaths  The templates paths.
	 *
	 * @throws \RuntimeException
	 * @since   1.0
	 */
	public function __construct(ModelInterface $model, $templatesPaths = '')
	{
		parent::__construct($model);

		/* @var TrackerApplication $app */
		$app = Factory::$application;

		$renderer = $app->get('renderer.type');

		$className = 'Joomla\\Tracker\\View\\Renderer\\' . ucfirst($renderer);

		if (false == class_exists($className))
		{
			throw new \RuntimeException(sprintf('Invalid renderer: %s', $renderer));
		}

		$config = array();

		switch ($renderer)
		{
			case 'twig':
				$config['templates_base_dir'] = JPATH_TEMPLATES;
				$config['environment']['debug'] = JDEBUG ? true : false;

				break;
		}

		// Load the renderer.
		$this->renderer = new $className($config);

		// Register tracker's extension.
		$this->renderer->addExtension(new TrackerExtension);

		// Register additional paths.
		if (!empty($templatesPaths))
		{
			$this->renderer->setTemplatesPaths($templatesPaths, true);
		}

		$gitHubHelper = new GitHubLoginHelper($app->get('github.client_id'), $app->get('github.client_secret'));

		$this->renderer
			->set('loginUrl', $gitHubHelper->getLoginUri())
			->set('user', $app->getUser())
			->set('uri', $app->get('uri'))
			->set('jdebug', JDEBUG);

		switch ($renderer)
		{
			case 'twig':
				// Register Text for translation.

				$this->renderer->addExtension(new \Twig_Extension_Debug);

				break;
		}
	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     ViewInterface::escape()
	 * @since   1.0
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 *
	 * @since   1.0
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		return $this->renderer->render($this->layout);
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  AbstractTrackerHtmlView  Method supports chaining.
	 *
	 * @since   1.0
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}
}
