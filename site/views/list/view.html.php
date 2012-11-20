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

jimport('joomla.application.component.view');

class InternalMessageViewList extends JView {
	function display($tpl = null) {
		// Datos de request
		$this->assign('Itemid', JRequest::getInt('Itemid', 0));
		$this->assign('user', $this->get('User'));
		$this->assign('scope', JRequest::getString('scope', SCP_RECIBIDOS));
		$this->assign('page', JRequest::getInt('page', 1));
		$this->assign('who', JRequest::getInt('who', 0));

		// Lista de mensajes
		$items = $this->get('List');
		$this->assignRef('items', $items['list']);
		$this->assign('count', $items['count']);

		// Lista de usuarios
		$users = $this->get('Users');
		$nomailusers = $this->get('NoMailUsers');
		$this->assignRef('users', $users);
		$this->assignRef('nomailusers', $nomailusers);

		// Activar tinyMCE
		$editor = JFactory::getEditor();
		$this->assignRef('editor', $editor);

		parent::display($tpl);
	}
}
