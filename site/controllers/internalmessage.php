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

jimport('joomla.application.component.controller');

class InternalMessageControllerInternalMessage extends InternalMessageController {

	/**
	 * Enviar un mensaje
	 */
	function send() {
		$model = $this->getModel();

		if ($model->sendMessage()) {
			$msg = JText::_('MENSAJE ENVIADO');
		} else {
			$msg = JText::_('send ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$link = COM_OPTION.'&view=list&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}

	/**
	 * Descarga un fichero adjunto
	 *
	 * @param integer
	 * @param string
	 */
	function download() {
		$model = $this->getModel();

		if ($model->downloadAttachment()) {
			$msg = JText::_('ADJUNTO DESCARGADO');
		} else {
			$msg = JText::_('download ERROR').': '.$model->getError();
		}
	}

	/**
	 * Marca un mensaje como no leído
	 */
	function unread() {
		$model = $this->getModel();

		if ($model->markUnreaded()) {
			$msg = JText::_('MENSAJE NO LEIDO');
		} else {
			$msg = JText::_('unread ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who = JRequest::getInt('who', 0);
		$link = COM_OPTION.'&view=list&scope='.$scope.'&page='.$page.'&who='.$who.'&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}
	
	/**
	 * Marca un mensaje como oculto
	 */
	function hiddenfrom() {
		$model = $this->getModel();

		if ($model->markHidden('from')) {
			$msg = JText::_('MENSAJE OCULTADO');
		} else {
			$msg = JText::_('hidden ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who = JRequest::getInt('who', 0);
		$link = COM_OPTION.'&view=list&scope='.$scope.'&page='.$page.'&who='.$who.'&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}
	function hiddento() {
		$model = $this->getModel();

		if ($model->markHidden('to')) {
			$msg = JText::_('MENSAJE OCULTADO');
		} else {
			$msg = JText::_('hidden ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who = JRequest::getInt('who', 0);
		$link = COM_OPTION.'&view=list&scope='.$scope.'&page='.$page.'&who='.$who.'&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}

	/**
	 * Desmarca un mensaje como oculto
	 */
	function unhiddenfrom() {
		$model = $this->getModel();

		if ($model->markUnhidden('from')) {
			$msg = JText::_('MENSAJE VISIBLE');
		} else {
			$msg = JText::_('unhidden ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who = JRequest::getInt('who', 0);
		$link = COM_OPTION.'&view=list&scope='.$scope.'&page='.$page.'&who='.$who.'&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}
	function unhiddento() {
		$model = $this->getModel();

		if ($model->markUnhidden('to')) {
			$msg = JText::_('MENSAJE VISIBLE');
		} else {
			$msg = JText::_('unhidden ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who = JRequest::getInt('who', 0);
		$link = COM_OPTION.'&view=list&scope='.$scope.'&page='.$page.'&who='.$who.'&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}

	/**
	 * Añadir un grupo
	 */
	function addGroup() {
		$model = $this->getModel();

		if ($model->addGroup()) {
			$msg = JText::_('GRUPO ANADIDO');
		} else {
			$msg = JText::_('add group ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$link = COM_OPTION.'&view=groups&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}

	/**
	 * Borrar un grupo
	 */
	function deleteGroup() {
		$model = $this->getModel();

		if ($model->deleteGroup()) {
			$msg = JText::_('GRUPO ELIMINADO');
		} else {
			$msg = JText::_('delete group ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$link = COM_OPTION.'&view=groups&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}

	/**
	 * Actualizar un grupo
	 */
	function updateGroup() {
		$model = $this->getModel();

		if ($model->updateGroup()) {
			$msg = JText::_('GRUPO ACTUALIZADO');
		} else {
			$msg = JText::_('update group ERROR').': '.$model->getError();
		}

		$Itemid = JRequest::getInt('Itemid', 0);
		$link = COM_OPTION.'&view=groups&Itemid='.$Itemid;
		$this->setRedirect($link, $msg);
	}
}
