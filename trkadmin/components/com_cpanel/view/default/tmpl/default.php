<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JToolbarHelper::title(JText::_('COM_CPANEL'), 'cpanel.png');

// JToolbarHelper::help('screen.cpanel');

// $user = JFactory::getUser();

?>
<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav">
			<ul class="nav nav-list">
                <li class="nav-header"><?php echo JText::_('COM_CPANEL_HEADER_SYSTEM'); ?></li>
                <li class="active">
                <a href="<?php echo JUri::base(true); ?>">
	                <?php echo JText::_('COM_CPANEL_LINK_DASHBOARD'); ?>
                </a>
                </li>
				<?php /*
                <li class="nav-header"><?php echo JText::_('COM_CPANEL_HEADER_SUBMENU'); ?></li>
			<?php if($user->authorise('core.admin')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_config"><?php echo JText::_('COM_CPANEL_LINK_GLOBAL_CONFIG'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_admin&view=sysinfo"><?php echo JText::_('COM_CPANEL_LINK_SYSINFO'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.manage', 'com_cache')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_cache"><?php echo JText::_('COM_CPANEL_LINK_CLEAR_CACHE'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.admin', 'com_checkin')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_checkin"><?php echo JText::_('COM_CPANEL_LINK_CHECKIN'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.manage', 'com_installer')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_installer"><?php echo JText::_('COM_CPANEL_LINK_EXTENSIONS'); ?></a></li>
			<?php endif;?>
				*/ ?>
			</ul>
		</div>
	</div>
	<div class="span6">
		<?php
		foreach (JCmsExtensionHelperModule::getModules('cpanel') as $module) :
			$output = JCmsExtensionHelperModule::renderModule($module, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($module->params);
			echo $output;
		endforeach;
		?>
	</div>
	<div class="span4">
		<?php
		// Display the submenu position modules
		foreach (JCmsExtensionHelperModule::getModules('icon') as $module) :
			$output = JCmsExtensionHelperModule::renderModule($module, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($module->params);
			echo $output;
		endforeach;
		?>
	</div>
</div>