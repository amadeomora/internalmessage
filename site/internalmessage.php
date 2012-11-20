<?php
/**
 * @version 11.07
 * @package Joomla
 * @subpackage Internal Message
 * @copyright (C) 2011 - 2011 Amadeo Mora
 * @license GNU/GPL, see LICENSE.php

 * Internal Message is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * Internal Message is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Internal Message; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
if($user->guest) {
	echo JText::_("LOGIN");
} else if(@$user->getParam(COMPONENT, 1) == 0) {
	echo JText::_("ACTIVATE");
} else {
	// Require defines
	require_once (JPATH_COMPONENT.DS.'defines.php');

	// Require the base and specific controller
	require_once (JPATH_COMPONENT.DS.'controller.php');
	if($controller = JRequest::getWord('controller')) {
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if (file_exists($path)) {
			require_once $path;
		} else {
			$controller = '';
		}
	}

	// Create the controller
	$classname	= 'InternalMessageController'.$controller;
	$controller = new $classname( );

	// Perform the Request task
	$controller->execute( JRequest::getVar('task') );

	// Redirect if set by the controller
	$controller->redirect();
}
