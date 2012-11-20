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
?>

<script language="javascript" type="text/javascript">
<!--
<?php
	// Monto un array de grupos para javascript
	echo 'var groups = new Array();';
	echo 'g_name = 0; g_description = 1; g_membersonly = 2; g_public = 3; g_members = 4;';
	for ($i=0; $i<count($this->groups); $i++) {
		$g = $this->groups[$i];
		echo 'groups['.$g->id.']=new Array();';
		echo 'groups['.$g->id.'][0]="'.$this->escape($g->name).'";';
		echo 'groups['.$g->id.'][1]="'.$this->escape($g->description).'";';
		echo 'groups['.$g->id.'][2]='.$g->membersonly.';';
		echo 'groups['.$g->id.'][3]='.$g->public.';';
		echo 'groups['.$g->id.'][4]=new Array();';

		for ($j=0; $j<count($g->members); $j++) {
			echo 'groups['.$g->id.'][4]['.$j.']='.$g->members[$j].';';
		}
	}
?>

function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// Comprobar datos
	if (pressbutton == 'add') {
		if (form.name.value == '') {
			return alert('<?php echo JText::_('SE DEBE DAR UN NOMBRE AL GRUPO', true ); ?>');
		}
	}

	if (pressbutton == 'delete') {
		if (form.id.value == '') {
			alert('<?php echo JText::_('SE DEBE SELECCIONAR UN GRUPO', true ); ?>');
			return;
		}
		if (!confirm("<?php echo JText::_('¿ESTA SEGURO?'); ?>")) {
			return;
		}
	}

	if (pressbutton == 'update') {
		if (form.id.value == '') {
			return alert('<?php echo JText::_('SE DEBE SELECCIONAR UN GRUPO', true ); ?>');
		}
	}

	// Enviar formulario
	submitform(pressbutton+'Group');
}

Array.prototype.in_array=function(elem){
	for(var j in this){
		if(this[j]==arguments[0]){
			return true;
		}
	}
	return false;

}

function selectgroup() {
	var form = document.adminForm;
	var id = 0;

	if (form.id.selectedIndex > 0) {
		id = form.id.options[form.id.selectedIndex].value;
		for (i=0; i<form.users.options.length; i++) {
			form.users.options[i].selected = groups[id][g_members].in_array(form.users.options[i].value);
		}
		form.name.value = groups[id][g_name];
		document.getElementById('tr_name').style.display = '';
		<?php if ($this->isadmin): ?>
		form.membersonly.checked = groups[id][g_membersonly];
		form.public.checked = groups[id][g_public];
		document.getElementById('tr_membersonly').style.display = '';
		document.getElementById('tr_public').style.display = '';
		<?php endif; ?>
		document.getElementById('tr_users').style.display = '';
		document.getElementById('tr_update').style.display = '';
	} else {
		for (i=0; i<form.users.options.length; i++) {
			form.users.options[i].selected = false;
		}
		form.name.value = '';
		document.getElementById('tr_name').style.display = 'none';
		<?php if ($this->isadmin): ?>
		form.membersonly.checked = false;
		form.public.checked = false;
		document.getElementById('tr_membersonly').style.display = 'none';
		document.getElementById('tr_public').style.display = 'none';
		<?php endif; ?>
		document.getElementById('tr_users').style.display = 'none';
		document.getElementById('tr_update').style.display = 'none';
	}

	$("#users").trigger("liszt:updated");
}

function addgroup() {
	var form = document.adminForm;

	for (i=0; i<form.id.options.length; i++) {
		form.id.options[i].selected = false;
	}
	document.getElementById('tr_id').style.display = 'none';
	form.name.value = '';
	document.getElementById('tr_name').style.display = '';
	<?php if ($this->isadmin): ?>
	for (i=0; i<form.id.options.length; i++) {
		form.id.options[i].disabled = false;
	}
	form.membersonly.checked = false;
	form.public.checked = false;
	document.getElementById('tr_membersonly').style.display = '';
	document.getElementById('tr_public').style.display = '';
	<?php endif; ?>
	for (i=0; i<form.users.options.length; i++) {
		form.users.options[i].selected = false;
	}
	document.getElementById('tr_users').style.display = '';
	document.getElementById('tr_update').style.display = 'none';
	document.getElementById('tr_add').style.display = '';

	$("#users").trigger("liszt:updated");
}
//-->
</script>

<style type="text/css">
.nousersmail { display: none; }
</style>

<div class="<?php echo COM_COMPONENT; ?>">

<div class="componentheading cim_menu">
	<?php include('components/'.COM_COMPONENT.'/views/menu.php'); ?>

	<?php
		if ($this->isadmin) {
			echo JText::_('ADMINISTRACION DE GRUPOS');
		} else {
			echo JText::_('GESTION DE GRUPOS');
		}
	?>
</div>


<div id="cim_actions" class="cim_actions">
	<a href="javascript:submitbutton('delete');" title="<?php echo JText::_('ELIMINAR GRUPO'); ?>">
		<?php echo JHTML::_('image.site', 'data_delete.gif', DIR_IMAGES, null, null, JText::_('ELIMINAR GRUPO')); ?>
	</a>
	<br />
	<a href="javascript:addgroup();" title="<?php echo JText::_('ANADIR GRUPO'); ?>">
		<?php echo JHTML::_('image.site', 'data_add.gif', DIR_IMAGES, null, null, JText::_('ANADIR GRUPO')); ?>
	</a>
</div>

<form action="index.php" method="post" name="adminForm" id="form">

<div id="cim_update" class="cim_form">
<table class="adminform">
 	<tr id="tr_id">
		<td class="key"><label for="id"><?php echo JText::_('GRUPO'); ?>: </label></td>
		<td>
			<select name="id" id="id" class="inputbox" size="1" onchange="selectgroup();">
			<?php
			echo '<option value="">'.JText::_('SELECCIONE UN GRUPO').'</option>';
			for ($i=0; $i<count($this->groups); $i++) {
				$g = $this->groups[$i];
				echo '<option value="'.$g->id.'">'.$this->escape($g->name).'</option>';
				if ($g->id == 1) {
					$NOUSERSMAIL = $this->escape($g->name);
				}
			}
			?>
			</select>
		</td>
	</tr>
 	<tr id="tr_name" style="display:none;">
		<td class="key"><label for="name"><?php echo JText::_('NOMBRE DEL GRUPO'); ?> :</label></td>
		<td><input type="text" class="inputbox" name="name" id="name" value="" /></td>
	</tr>
	<?php if ($this->isadmin): ?>
	<tr id="tr_membersonly" style="display:none;">
		<td class="key"><label for="membersonly"><?php echo JText::_('SOLO PARA MIEMBROS'); ?> :</label></td>
		<td><input type="checkbox" class="inputbox" name="membersonly" id="membersonly" value="1" /></td>
	</tr>
	<tr id="tr_public" style="display:none;">
		<td class="key"><label for="public"><?php echo JText::_('PUBLICO'); ?> :</label></td>
		<td><input type="checkbox" class="inputbox" name="public" id="public" value="1" /></td>
	</tr>
	<?php endif; ?>
	<tr id="tr_users" style="height:200px;display:none;">
		<td class="key"><label for="users"><?php echo JText::_('USUARIOS'); ?>: </label></td>
		<td>
			<!--select name="users[]" id="users" class="Xinputbox" size="10" multiple="multiple"-->
			<select name="users[]" id="users" data-placeholder="..." class="chzn-select" size="10" style="width:450px;" multiple="multiple">			
			<?php
			for ($i=0; $i<count($this->users); $i++) {
				$u = $this->users[$i];
				echo '<option value="'.$u->id.'">'.$this->escape($u->name). ' ('.$u->username.')</option>';
			}
			if ($this->isadmin) {
				echo '<optgroup label="'.$NOUSERSMAIL.'">';
				for ($i=0; $i<count($this->nomailusers); $i++) {
					$u = $this->nomailusers[$i];
					echo '<option value="'.$u->id.'">'.$this->escape($u->name). ' ('.$u->username.')</option>';
				}
				echo '</optgroup>';
			}
			?>
			</select>
		</td>
	</tr>
	<tr id="tr_update" style="display:none;">
		<td colspan="2">
			<button type="button" class="button" onclick="submitbutton('update');"><?php echo JText::_('ACTUALIZAR GRUPO') ?></button>
		</td>
	</tr>
	<tr id="tr_add" style="display:none;">
		<td colspan="2">
			<button type="button" class="button" onclick="submitbutton('add');"><?php echo JText::_('ANADIR GRUPO') ?></button>
		</td>
	</tr>
</table>

</div>

<!-- Do not edit after this, these objects don't influence how the page looks -->
<input type="hidden" name="option" value="<?php echo COM_COMPONENT; ?>" />
<input type="hidden" name="controller" value="<?php echo COMPONENT; ?>" />
<input type="hidden" name="view" value="groups" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>

</div>
