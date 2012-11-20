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
	// Lista de grupos
	echo 'var groups = new Array();';
	//echo 'groups[0] = new Array();';
	for ($i=0; $i<count($this->groups); $i++) {
		$g = $this->groups[$i];
		echo 'groups['.$g->id.'] = new Array('.implode(',', $g->members).', 0);';
	}
?>

// Confirmación de salir de pantalla
var pedirConfirmacion = true;
window.onbeforeunload = confirmExit;
function confirmExit() {
	if (pedirConfirmacion) return "";
}
//

function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// Comprobar datos
	if (form.id_to.selectedIndex == -1) {
		return alert('<?php echo JText::_('SE DEBE INCLUIR ALGUN DESTINATARIO', true ); ?>');
	}
	if (form.subject.value == '') {
		return alert('<?php echo JText::_('SE DEBE INCLUIR ALGUN ASUNTO', true ); ?>');
	}
	var text = <?php echo $this->editor->getContent('text'); ?>
	if (text == '') {
		return alert('<?php echo JText::_('SE DEBE INCLUIR ALGUN TEXTO', true ); ?>');
	}


	for (i=0; i<form.id_to.length; i++) {
		if (form.id_to.options[i].selected && form.id_to.options[i].disabled) {
			form.id_to.options[i].disabled = false;
		}
	}

	pedirConfirmacion = false;
	submitform(pressbutton);
}

function seleccionar_abrir() {
	document.getElementById('_id_to').style.display = 'none';
	document.getElementById('_id_to_select').style.display = '';
}

function seleccionar_anadir() {
	var form = document.adminForm;
	var txt = '';
	for (i=0; i<form.id_to.length; i++) {
		if (form.id_to.options[i].selected) {
			txt += form.id_to.options[i].text + ', '
		}
	}
	txt = txt.substr(0, txt.length<102 ? txt.length-2 : 100);
	document.getElementById('_id_to').value = txt;
	if (txt == '') {
		document.getElementById('_id_to').style.display = 'none';
		document.getElementById('_id_to_select').style.display = '';
	} else {
		document.getElementById('_id_to').style.display = '';
		document.getElementById('_id_to_select').style.display = 'none';
	}
}

Array.prototype.in_array=function(elem){
	for(var j in this){
		if(this[j]==arguments[0]){
			return true;
		}
	}
	return false;

}

function seleccionar_grupo() {
	var form = document.adminForm;
	var id_group = form.id_group.options[form.id_group.selectedIndex].value;

	for (i=0; i<form.id_to.length; i++) {
		form.id_to.options[i].selected = groups[id_group].in_array(form.id_to.options[i].value);
	}
	
	$("#id_to").trigger("liszt:updated");
}
//-->
</script>

<div class="<?php echo COM_COMPONENT; ?>">

<div class="componentheading cim_menu">
	<?php include('components/'.COM_COMPONENT.'/views/menu.php'); ?>

	<?php echo JText::_('NUEVO MENSAJE'); ?>
</div>

<div class="cim_form">
<form action="index.php" method="post" enctype="multipart/form-data" name="adminForm" id="form">

<table class="adminform">
 	<tr>
		<td class="key"><label for="id_from"><?php echo JText::_('DE'); ?>: </label></td>
		<td><input readonly="readonly" class="inputbox" id="id_from" name="id_from" type="text" value="<?php echo $this->escape($this->user->name); ?>" size="70" /></td>
	</tr>
	<tr>
		<td class="key"><label for="value"><?php echo JText::_('A'); ?>: </label></td>
		<td>
			<input onfocus="seleccionar_abrir();" class="inputbox" id="_id_to" name="_id_to" type="text" value="" size="70" />
			<div id="_id_to_select" name="_id_to_select" style="display:none;">
				<!--select name="id_to[]" id="id_to" class="Xinputbox" size="10" multiple="multiple" ondblclick="seleccionar_anadir();"-->
				<select name="id_to[]" id="id_to" data-placeholder="..." class="chzn-select" style="width:450px" size="10" multiple="multiple" ondblclick="seleccionar_anadir();">
				<?php
				for ($i=0; $i<count($this->users); $i++) {
					$u = $this->users[$i];
					if ($this->user->id == $u->id) continue; //<==
					$selected = @$this->items->id_from == @$u->id ? ' selected="selected"' : '';
					if (!$selected && $this->replyall) {
						$selected = in_array($u->id, $this->replyall) ? ' selected="selected"' : '';
					}
					echo '<option value="'.$u->id.'"'.$selected.'>'.$this->escape($u->name). ' ('.$u->username.')</option>';
				}
				$disabled = $this->isadmin ? '' : ' disabled="disabled"';
				for ($i=0; $i<count($this->nomailusers); $i++) {
					$u = $this->nomailusers[$i];
					if ($this->user->id == $u->id) continue; //<==
					$selected = $this->items->id_from == $u->id ? ' selected="selected"' : '';
					if (!$selected && $this->replyall) {
						$selected = in_array($u->id, $this->replyall) ? ' selected="selected"' : '';
					}
					echo '<option value="'.$u->id.'"'.$selected.$disabled.'>'.$this->escape($u->name). ' ('.$u->username.')</option>';
				}
				?>
				</select>
				<br />
				<select name="id_group" id="id_group" class="inputbox" size="1" onchange="seleccionar_grupo();">
				<?php
				echo '<option value="0">'.JText::_('NO HAY SELECCIONADO NINGUN GRUPO').'</option>';
				for ($i=0; $i<count($this->groups); $i++) {
					$g = $this->groups[$i];
					$selected = $this->id_group == $g->id ? ' selected="selected"' : '';
					echo '<option value="'.$g->id.'"'.$selected.'>'.$this->escape($g->name).'</option>';
				}
				?>
				</select>
				<script type="text/javascript">seleccionar_anadir();</script>
				<br />
				<button type="button" class="button" onclick="seleccionar_anadir();"><?php echo JText::_('SELECCIONAR') ?></button>
			</div>
		</td>
	</tr>
	<tr>
		<td class="key"><label for="value"><?php echo JText::_('ASUNTO'); ?>: </label></td>
		<td><input class="inputbox" id="subject" name="subject" type="text" value="<?php if (@$this->items->subject) { echo 'Re: '.$this->escape($this->items->subject); } ?>" size="70" /></td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('TEXTO'); ?>: </label></td>
		<td><textarea id="text" name="text" cols="60" rows="5" style="width:515px; height:100px;" class="mce_editable"><?php if (@$this->items->subject) { echo '<br /><br />========<br />'.$this->escape(@$this->items->text); } ?></textarea></td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('ADJUNTO'); ?>: </label></td>
		<td><input class="inputbox" id="attachment" name="attachment" type="file" size="50" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<button type="reset" class="button validate"><?php echo JText::_('CANCELAR') ?></button>
			<button type="button" class="button validate" onclick="submitbutton('send')"><?php echo JText::_('ENVIAR') ?></button>
		</td>
	</tr>
</table>

<!-- Do not edit after this, these objects don't influence how the page looks -->
<input type="hidden" name="option" value="<?php echo COM_COMPONENT; ?>" />
<input type="hidden" name="controller" value="<?php echo COMPONENT; ?>" />
<input type="hidden" name="view" value="" />
<input type="hidden" name="task" value="send" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</div>

</div>
