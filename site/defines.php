<?php
/**
 * @version 11.10
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

/**
 * Definiciones para todo el componente
 */
define('COMPONENT', 		'internalmessage');

define('COM_COMPONENT',		'com_'.COMPONENT);
define('COM_DB',			'#__'.COMPONENT);
define('COM_DB_GROUPS',		'#__'.COMPONENT.'_groups');
define('COM_OPTION',		'index.php?option='.COM_COMPONENT);
define('DIR_ATTACHMENT',    '.'.DS.COM_COMPONENT.'_attachment');
define('DIR_CSS', 			'components/'.COM_COMPONENT.'/media/css/');
define('DIR_IMAGES', 		'components/'.COM_COMPONENT.'/media/images/');
define('GRP_NOMAIL',		1);
define('MSG_PER_PAGE',		10);
define('SCP_TODOS',			JText::_('AMBITO TODOS'));
define('SCP_RECIBIDOS',		JText::_('AMBITO RECIBIDOS'));
define('SCP_ENVIADOS',		JText::_('AMBITO ENVIADOS'));
define('SCP_NO_LEIDOS',		JText::_('AMBITO NO LEIDOS'));
define('SCP_OCULTOS',		JText::_('AMBITO OCULTOS'));
?>
