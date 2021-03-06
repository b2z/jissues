<?php
/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tracker\Components\System\View\Config;

use Joomla\Tracker\View\AbstractTrackerHtmlView;
use Joomla\Utilities\ArrayHelper;
use Twig_SimpleFilter;

/**
 * Config view.
 *
 * @since  1.0
 */
class ConfigHtmlView extends AbstractTrackerHtmlView
{
	/**
	 * @var    \stdClass
	 * @since  1.0
	 */
	protected $config;

	/**
	 * Method to render the view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 *
	 * @return  string  The rendered view.
	 */
	public function render()
	{
		$config = json_decode(file_get_contents(JPATH_CONFIGURATION . '/config.json'));

		// @todo Twig can not foreach() over stdclasses...
		$cfx = ArrayHelper::fromObject($config);

		$this->renderer->set('config', $cfx);

		// Format a string.
		$this->renderer->addFilter(
			new Twig_SimpleFilter(
				'tformat', function ($string)
				{
					$string = str_replace(array('_', '-'), ' ', $string);
					$string = ucfirst($string);

					return $string;
				}
			)
		);

		return parent::render();
	}
}
