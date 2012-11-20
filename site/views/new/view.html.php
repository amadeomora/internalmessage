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

jimport('joomla.application.component.view');

class InternalMessageViewNew extends JView {
	function display($tpl = null) {

		// Pasar datos en caso de reply
		$items = $this->get('Message');
		$this->assignRef('items', $items);

		// Pasar destinatarios múltiples
		if (JRequest::getString('reply', '') == 'all') {
			$replyall = array();
			foreach ($items->id_to as $id_to) {
				$replyall[] = $id_to->id_to;
			}
			$this->assign('replyall', $replyall);
		} else {
			$this->assign('replyall', false);
		}

		// Administrador ?
		$user = $this->get('User');
		$this->assign('isadmin', $user->get('gid') > 24);
		$this->assignRef('user', $user);

		// Pasar datos generales
		$model = $this->getModel();
		$groups = $model->getGroups(true);
		$users = $this->get('Users');
		$nomailusers = $this->get('NoMailUsers');

		$this->assignRef('groups', $groups);
		$this->assignRef('users', $users);
		$this->assignRef('nomailusers', $nomailusers);

		// Para el formulario
		$this->assign('Itemid', JRequest::getInt('Itemid', 0));

		// Activar tinyMCE
		$editor = JFactory::getEditor();
		$this->assignRef('editor', $editor);

		parent::display($tpl);

	}

}
