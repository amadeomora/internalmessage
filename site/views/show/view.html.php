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

class InternalMessageViewShow extends JView {
	function display($tpl = null) {
		$this->assign('user', $this->get('User'));
		$this->assign('Itemid', JRequest::getInt('Itemid', 0));
		$this->assign('scope', JRequest::getString('scope', 'Recibidos'));
		$this->assign('page', JRequest::getInt('page', 1));
		$this->assign('who', JRequest::getInt('who', 0));

		// Pasar datos
		$items = $this->get('Message');
		$this->assignRef('items', $items);

		// Directorio de adjuntos
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		$this->assign('attachment_dir', 'attachment_dir');

		// Lista de usuarios
		$nomailusers = $this->get('NoMailUsers');
		for($i=0; $i<count($nomailusers); $i++) {
			$nomailusers[$i] = $nomailusers[$i]->id;
		}
		$this->assignRef('nomailusers', $nomailusers);

		// Activar tinyMCE
		$editor = JFactory::getEditor();
		$this->assignRef('editor', $editor);

		parent::display($tpl);
	}
}
