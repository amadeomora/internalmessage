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

jimport('joomla.application.component.controller');

class InternalMessageController extends JController {

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct();

		// Vista (por defecto list)
		$view = JRequest::getVar('view');
		switch ($view) {
			case 'list':
			case 'new':
			case 'show':
			case 'groups':
				break;
			default:
				$view = 'list';
		}

		// Modelo por defecto
		$model = $this->getModel();
		$rview = $this->getView($view, 'html', 'InternalMessageView');
		$rview->setModel($model, true);

		// Aplico la hoja de estilo a cualquier salida del componente
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base().DIR_CSS.'index.css');
	}

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display($cachable = false, $urlparams = false) {
		parent::display();
	}
}
