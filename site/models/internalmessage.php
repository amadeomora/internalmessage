<?php
/**
 * @version 12.11
 * @package Joomla
 * @subpackage Internal Message
 * @copyright (C) 2011 - 2012 Amadeo Mora
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

jimport('joomla.application.component.model');
jimport('joomla.application.component.helper');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class InternalMessageModelInternalMessage extends JModel {
	/**
	 * Base de datos
	 *
	 * @var dbidentifier
	 */
	var $db;
	/**
	 * Identificador del usuario actual
	 *
	 * @var int
	 */
	var $userid;
	/**
	 * Administrador
	 *
	 * @var boolean
	 */
	var $isadmin;
	/**
	 * Usuario actual
	 *
	 * @var object
	 */
	var $user;
	/**
	 * Directorio de descargas
	 *
	 * @var string
	 */
	var $attachment_dir;
	/**
	 * Aviso por email
	 *
	 * @var string
	 */
	var $send_mail;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		parent::__construct();

		$this->db = JFactory::getDBO();

		$this->user = JFactory::getUser();
		$this->userid = $this->user->get('id');
		$this->isadmin = $this->user->get('gid') > 24;

		$params = JComponentHelper::getParams(COM_COMPONENT);
		$this->attachment_dir = $params->get('attachment_dir', DIR_ATTACHMENT);
		$this->send_mail = $params->get('send_mail', 1);
	}

	/**
	 * Devuelve la lista de mensajes
	 *
	 * @access public
	 * @return array Número de mensajes y Lista de mensajes de una página
	 */
	function getList() {
		$scope = JRequest::getString('scope', SCP_RECIBIDOS);
		$page = JRequest::getInt('page', 1);
		$who  = JRequest::getInt('who', 0);

		switch ($scope) {
		case SCP_TODOS:
			$where = '(i.id_to = '.$this->userid.' AND i.id_from = u.id AND NOT hidden_to)'
				.'  OR (i.id_from = '.$this->userid
				.'	   AND i.id_to = u.id AND NOT hidden_from AND (i.id_refered = 0 OR i.id_refered = i.id))';
			break;
		case SCP_RECIBIDOS:
			$where = 'i.id_to = '.$this->userid.' AND i.id_from = u.id'
				.'     AND NOT hidden_to';
			break;
		case SCP_NO_LEIDOS:
			$where = 'i.id_to = '.$this->userid.' AND i.id_from = u.id'
				.'     AND NOT hidden_to'
				.'     AND NOT readed';
			break;
		case SCP_ENVIADOS:
			$where = 'i.id_from = '.$this->userid
				.'	   AND i.id_to = u.id AND (i.id_refered = 0 OR i.id_refered = i.id)'
				.'     AND NOT hidden_from';
			break;
		case SCP_OCULTOS:
			$where = '(i.id_to = '.$this->userid.' AND i.id_from = u.id AND hidden_to)'
				.'  OR (i.id_from = '.$this->userid
				.'	   AND i.id_to = u.id AND hidden_from AND (i.id_refered = 0 OR i.id_refered = i.id))';
			break;
		}
		if ($who) {
			$where = '('.$where.') AND (i.id_to = '.$who.' OR i.id_from = '.$who.')';
		}

		// Cuenta el número de registros
		$query = 'SELECT count(*) AS contador FROM '.COM_DB.' i, #__users u WHERE '.$where;
		$this->db->setQuery($query);
		$ol = $this->db->loadObject();
		$contador = $ol->contador;

		// Obtiene los datos de la página
		$minLimit = (max($page, 1) - 1) * MSG_PER_PAGE;
		$maxLimit = MSG_PER_PAGE;
		$query = 'SELECT i.id, i.id_refered, i.id_from, i.id_to, i.subject, i.date'
			.',i.attachment_name, i.attachment_size, i.readed, u.name, u.username'
			.' FROM '.COM_DB.' i, #__users u WHERE '
			.$where
			.' ORDER BY i.date DESC'
			.' LIMIT '.$minLimit.','.$maxLimit;
		$this->db->setQuery($query);
		$ol = $this->db->loadObjectList();
		for ($i=0; $i<count($ol); $i++) {
			$o = $ol[$i];
			$o->subject = base64_decode($o->subject);
			if ($o->id_refered) {
				if ($o->id == $o->id_from) {
					$o->id_to = $this->_getRefered($o->id_refered, $o->id);
				} else {
					$o->id_to = $this->_getRefered($o->id_refered);
				}
			}
			unset($o->id_refered);
			$ol[$i] = $o;
		}
		return array('count' => $contador, 'list' => $ol);
	}

	/**
	 * Devuelve un mensaje
	 *
	 * @access public
	 * @return object Mensaje
	 */
	function getMessage() {
		$id = JRequest::getInt('id', 0);
		if (!$id) {
			return false;
		}

		$query = 'SELECT i.id, i.id_refered, i.id_from, i.id_to, i.subject, i.text, i.date, i.attachment_name, i.attachment_size, i.readed, i.hidden_from, i.hidden_to'
				.',uf.username as fromusername, uf.name as fromname'
				.',ut.username as tousername, ut.name as toname'
				.' FROM '.COM_DB.' i, #__users uf, #__users ut'
				.' WHERE i.id = '.$id.' AND i.id_from = uf.id AND i.id_to = ut.id';
		$this->db->setQuery($query);
		$o = $this->db->loadObject();

		if ($this->user->id != $o->id_from) {
			$this->_readedMessage($id);
		}

		$o->subject = base64_decode($o->subject);
		$o->text = base64_decode($o->text);

		if ($o->id_refered) {
			$o->id_to = $this->_getRefered($o->id_refered);
		} else {
			$o->id_refered = $o->id;
		}

		if ($o->attachment_size) {
			$o->attachment_filename = $this->attachment_dir.DS.'file_'.$o->id_refered;
		}

		return $o;
	}


	/**
	 * Obtiene los usuarios destinatarios y si han leido el mensaje
	 *
	 * @access private
	 * @param int $id_refered Identificador del mensaje de cabecera
	 * @param int $id (optional) Identificado del mensaje que no se incluye
	 * @return objectList Lista de destinatarios
	 */
	function _getRefered($id_refered, $id = 0) {
		$query = 'SELECT i.id, i.id_to, i.readed, u.name, u.username'
				.' FROM '.COM_DB.' i, #__users u'
				.' WHERE i.id_refered = '.$id_refered.' AND i.id_to = u.id';
		$this->db->setQuery($query);
		$ol = $this->db->loadObjectList();
		if ($id) {
			for ($i=0; $i<count($ol); $i++) {
				if ($ol[$i]->id == $id) {
					unset($ol[$i]); //<== Elimino ese registro
					break;
				}
			}
		}
		return $ol;
	}

	/**
	 * Marca como leído un mensaje
	 *
	 * @access public
	 * @return void
	 */
	function _readedMessage($id) {
		$query = 'UPDATE '.COM_DB
				.' SET readed = 1'
				.' WHERE id = '.$id;
		$this->db->setQuery($query);
		$this->db->query();

		return $id;
	}

	/**
	 * Send the message and display a notice
	 *
	 * @access public
	 * @param object
	 * @return boolean True si se ha enviado el mensaje
	 */
	function _sendMail($o) {
		if (!$this->send_mail) {
			return true;
		}

		jimport('joomla.mail.helper');

		$app = JFactory::getApplication();
		$SiteName 	= $app->getCfg('sitename');
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

		$toUser = JFactory::getUser($o->id_to);
		$email 				= $toUser->get('email');
		$from 				= $MailFrom;
		$fromname 			= $SiteName;
		$subject 			= $SiteName;
		$noreply = 'no-reply';

		// Check for a valid to address
		if (! $email  || ! JMailHelper::isEmailAddress($email) ) {
			$this->setError(JText::sprintf('EMAIL INVALIDO' , $email));
			return false;
		}

		// Check for a valid from address
		if (! $from || ! JMailHelper::isEmailAddress($from) ) {
			$this->setError(JText::sprintf('EMAIL INVALIDO' , $from));
			return false;
		}

		// Build the message to send
		$msg	= JText::_('EMAIL MENSAJE');
		$body	= sprintf($msg, $SiteName, $this->user->get('name'), base64_decode($o->subject), base64_decode($o->text), JURI::base());

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$sender	 = JMailHelper::cleanAddress($sender);

		// Send the email
		if (JUtility::sendMail($from, $fromname, $email, $subject, $body, true, null, null, null, $noreply) !== true ) {
			$this->setError(JText::_('EMAIL NO ENVIADO'));
			return false;
		}

		return true;
	}

	/**
	 * Envia un mensaje
	 *
	 * @access public
	 * @return boolean True si se ha realizado
	 */
	function sendMessage() {
		if (count($_POST['id_to']) == 0) {
			return false;
		}

		$o = new stdClass;
		$o->id				= 0;
		$o->id_from 		= $this->userid;
		$o->subject 		= base64_encode(stripslashes($_POST['subject']));
		$o->text  			= base64_encode(stripslashes($_POST['text']));
		$o->date			= date('Y-m-d H:i:s'); //gmdate('Y-m-d H:i:s');
		$o->attachment_name = $_FILES['attachment']['name'];
		$o->attachment_size = $_FILES['attachment']['size'];
		$o->readed			= 0;
		$o->hidden_from		= 0;
		$o->hidden_to		= 0;
		$o->id_refered 		= 0;

		// Creo el mensaje para el primer destinatario
		$o->id_to = $_POST['id_to'][0];
		if (!$this->store($o)) {
			return false;
		}

		// Obtengo el identificador
		$o->id = $o->id_refered = $this->db->insertid();
		$this->_sendMail($o);

		// Guardo el adjunto si lo hay
		if ($o->attachment_size) {
			if (!$this->_attachmentSave($o->id)) {
				return $this->_sendMessageClean($o->id);
			}
		}
		// Envio el mensaje al resto de destinatarios
		if (count($_POST['id_to']) > 1) {
			// Actualizo el identificador de referencia
			if (!$this->store($o)) {
				return $this->_sendMessageClean($o->id);
			}
			// Añado el resto de destinatarios
			$o->id = 0;
			for ($i = 1; $i < count($_POST['id_to']); $i++) {
				$o->id_to = $_POST['id_to'][$i];
				if (!$this->store($o)) {
					return $this->_sendMessageClean($o->id);
				}
				$this->_sendMail($o);
			}
		}

		return true;
	}

	/**
	 * Renombra el fichero temporal al nombre definitivo
	 *
	 * @access private
	 * @param int $newId Identificador del mensaje
	 * @return boolean True si se ha realizado
	 */
	function _attachmentSave($newId) {
		if (!$newId) {
			return false;
		}
		if (!JFolder::exists($this->attachment_dir) && !JFolder::create($this->attachment_dir)) {
			$this->setError('folder exists');
			return false;
		}
		$this->setError('upload');
		return JFile::upload($_FILES['attachment']['tmp_name'], $this->attachment_dir.DS.'file_'.$newId);
	}

	/**
	 * Descarga un fichero adjunto
	 *
	 * @access public
	 */
	function downloadAttachment() {
		$id = JRequest::getInt('id', 0, 'GET');
		if (!$id) {
			return false;
		}

		$query = 'SELECT attachment_name, attachment_size'
				.' FROM ' .COM_DB
				.' WHERE id = '.$id;
		$this->db->setQuery($query);
		$o = $this->db->loadObject();

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$o->attachment_name.'"');
		header('Content-Length: '.$o->attachment_size);
		echo(JFile::read($this->attachment_dir.DS.'file_'.$id));
		exit;
	}

	/**
	 * Devuelve el usuario actual
	 *
	 * @return objeto
	 */
	function getUser() {
		return $this->user;
	}


	/**
	 * Devuelve la lista de usuarios activos
	 *
	 * @param string Campo por el que se ordena la lista
	 * @return lista
	 */
	function getUsers($order = 'name') {
		$grpnomail = $this->_getNoMailUsers();
		$query = $grpnomail ? 'AND id NOT IN ('.$grpnomail.')' : '';

		$query = 'SELECT id, name, username'
				.' FROM #__users'
				.' WHERE block = 0 '.$query
				.' ORDER BY '. $order;
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	 * Devuelve la lista de usuarios no activos
	 *
	 * @param string Campo por el que se ordena la lista
	 * @return lista
	 */
	function getNoMailUsers($order = 'name') {
		$grpnomail = $this->_getNoMailUsers();
		$query = 'SELECT id, name, username'
				.' FROM #__users'
				.' WHERE block = 0 AND id IN ('.$grpnomail.')'
				.' ORDER BY '. $order;
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	 * Devuelve la lista de usuarios que no usan este componente
	 *
	 * @return lista
	 */
	function _getNoMailUsers() {
		$query = 'SELECT members'
				.' FROM '.COM_DB_GROUPS
				.' WHERE id = '.GRP_NOMAIL;
		$this->db->setQuery($query);
		$o = $this->db->loadObject();
		return $o->members;
	}

	/**
	 * Devuelve mi lista de grupos
	 *
	 * @param bool Se incluyen los públicos
	 * @param string Campo por el que se ordena la lista
	 * @return lista
	 */
	function getGroups($public = false, $order = 'name') {
		$query = '';
		if ($this->isadmin) {
			$owner = 0;
		} else {
			$owner = $this->userid;
			if ($public) {
				$regexp = '^'.$owner.'$|^'.$owner.',|,'.$owner.'$|,'.$owner.',';
				$query = ' OR (public = 1 AND (membersonly = 0 OR (membersonly = 1 AND members REGEXP "'.$regexp.'")))';
			}
		}
		$query = 'SELECT *'
				.' FROM '.COM_DB_GROUPS
				.' WHERE id_owner = '.$owner.''.$query
				.' ORDER BY '. $order;
		$this->db->setQuery($query);
		$ol = $this->db->loadObjectList();
		for ($i=0; $i<count($ol); $i++) {
			$o = $ol[$i];
			$o->name = base64_decode($o->name);
			$o->description = base64_decode($o->description);
			$o->members = $o->members ? @explode(',', $o->members) : array();
			$ol[$i] = $o;
		}
		return $ol;
	}

	/**
	 * Marca un mensaje
	 *
	 * @access private
	 */
	function _mark($field, $value) {
		// Un único mensaje
		$id = JRequest::getInt('id', 0);
		if ($id) {
			if (!$field) {
				return false; // <==
			}
			$query = 'UPDATE '.COM_DB.' SET '.$field.' = '.$value.' WHERE id = '.$id;
			$this->db->setQuery($query);
			return $this->db->query(); // <==
		}

		// Una selección de mensajes
		$ids = JRequest::getVar('id', array());
		if ($ids) {
			list($action, $field) = explode('_', $field); // get action
			foreach ($ids as $val) {
				list($field, $id) = explode(',', $val);	// get to|from and id
				$field = $action.'_'.$field;
				$query = 'UPDATE '.COM_DB.' SET '.$field.' = '.$value.' WHERE id = '.$id;
				$this->db->setQuery($query);
				$this->db->query();
			}
			return true;
		}

		// Ningún mensaje
		return false;
	}

	/**
	 * Marca un mensaje como no leído
	 *
	 * @return boolean True si se ha conseguido
	 */
	function markUnreaded() {
		return $this->_mark('readed', 0);
	}

	/**
	 * Desmarca un mensaje como oculto
	 *
	 * @return boolean True si se ha conseguido
	 */
	function markUnhidden($target) {
		if ($target == 'from') 
			return $this->_mark('hidden_from', 0);
		else
			return $this->_mark('hidden_to', 0);
	}

	/**
	 * Desmarca un mensaje como oculto
	 *
	 * @return boolean True si se ha conseguido
	 */
	function markHidden($target) {
		if ($target == 'from') 
			return $this->_mark('hidden_from', 1);
		else
			return $this->_mark('hidden_to', 1);
	}

	/**
	 * Store a record
	 *
	 * @access	public
	 * @return	boolean	true on success
	 */
	function store($o) {
		$fields = array();
		$fields['id'] = $o->id;
		$fields['id_refered'] = $o->id_refered;
		$fields['id_from'] = $o->id_from;
		$fields['id_to'] = $o->id_to;
		$fields['date'] = $o->date;
		$fields['subject'] = $o->subject;
		$fields['text'] = $o->text;
		$fields['attachment_name'] = $o->attachment_name;
		$fields['attachment_size'] = $o->attachment_size;
		$fields['readed'] = $o->readed;
		$fields['hidden_to'] = $o->hidden_to;
		$fields['hidden_from'] = $o->hidden_from;

		if ($o->id) { // update
			$sql = 'UPDATE '.COM_DB.' SET ';
			foreach ($fields as $field => $value) {
				$sql .= $field."='".$value."',";
			}
			$sql = substr($sql, 0, -1);
			$sql .= ' WHERE id = '.$o->id;
		} else { // insert
			unset($fields['id']);
			$values = '';
			$sql = 'INSERT INTO '.COM_DB.' (';
			foreach ($fields as $field => $value) {
				$sql .= $field.',';
				$values .= "'".$value."',";
			}
			$sql = substr($sql, 0, -1);
			$values = substr($values, 0, -1);
			$sql .= ') VALUES ('.$values.')';
		}
		$this->db->setQuery($sql);
		if (!$this->db->query()) {
			$this->setError('insert/update');
			return false;
		}

		return true;
	}

	/**
	 * Actualiza un grupo
	 *
	 * @access public
	 * @return boolean True si se ha realizado
	 */
	function addGroup() {
		return $this->updateGroup('new');
	}

	/**
	 * Actualiza un grupo
	 *
	 * @access public
	 * @return boolean True si se ha realizado
	 */
	function updateGroup($action = 'update') {
		$o = new stdClass;
		$o->id				= ($action == 'new') ? 0 : $_POST['id'];
		$o->id_owner 		= $this->userid;
		$o->name	 		= base64_encode(stripslashes($_POST['name']));
		$o->description		= base64_encode(stripslashes($_POST['description']));
		$o->members			= implode(',', $_POST['users']);
		$o->membersonly		= 0;
		$o->public		 	= 0;

		if ($this->isadmin) {
			$o->id_owner	= 0;
			$o->membersonly	= $_POST['membersonly'] ? 1 : 0;
			$o->public		= $_POST['public'] ? 1 : 0;
		}

		if (!$this->store_group($o)) {
			return false;
		}

		return true;
	}

	/**
	 * Store a record
	 *
	 * @access	public
	 * @return	boolean	true on success
	 */
	function store_group($o) {
		if ($o->id) { // update
			$fields = array();
			//$fields['id'] = $o->id;
			//$fields['id_owner'] = $o->id_owner;
			//$fields['name'] = $o->name;
			//$fields['description'] = $o->description;
			$fields['members'] = $o->members;
			$fields['membersonly'] = $o->membersonly;
			$fields['public'] = $o->public;

			$sql = 'UPDATE '.COM_DB_GROUPS.' SET ';
			foreach ($fields as $field => $value) {
				$sql .= $field."='".$value."',";
			}
			$sql = substr($sql, 0, -1);
			$sql .= 'WHERE id = '.$o->id;
		} else { // insert
			$fields = array();
			$fields['id'] = $o->id;
			$fields['id_owner'] = $o->id_owner;
			$fields['name'] = $o->name;
			$fields['description'] = $o->description;
			$fields['members'] = $o->members;
			$fields['membersonly'] = $o->membersonly;
			$fields['public'] = $o->public;

			$values = '';
			$sql = 'INSERT INTO '.COM_DB_GROUPS.' (';
			foreach ($fields as $field => $value) {
				$sql .= $field.',';
				$values .= "'".$value."',";
			}
			$sql = substr($sql, 0, -1);
			$values = substr($values, 0, -1);
			$sql .= ') VALUES ('.$values.')';
		}
		$this->db->setQuery($sql);
		if (!$this->db->query()) {
			$this->setError('insert/update');
			return false;
		}

		return true;
	}

	/**
	 * Elimina un grupo
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function deleteGroup() {
		if ($_POST['id'] == 1) { // Usuario que no usan Internal Mail
			return true;
		}

		if (!$this->remove_group($_POST['id'])) {
			return false;
		}

		return true;
	}

	/**
	 * Remove a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function remove_group($id) {
		if (!$id) {
			return true;
		}

		$sql = 'DELETE FROM '.COM_DB_GROUPS.' WHERE id ='.$id;
		$this->db->setQuery($sql);
		if (!$this->db->query()) {
			$this->setError('delete');
			return false;
		}

		return true;
	}

}
