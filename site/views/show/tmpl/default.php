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
?>
<!--
<link rel="stylesheet" href="components/com_internalmessage/media/css/index.css" type="text/css" />
-->
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'reply') {
		form.view.value = 'new';
	}

	if (pressbutton == 'replyall') {
		form.view.value = 'new';
		form.reply.value = 'all';
	}

	submitform(pressbutton);
}

function showprint() {
	var s = '<html>'
	s += '<head>'
	s += '<title> </title>'
	s += '<style type="text/css">'
	s += 'td.key {text-align: right; vertical-align: top;}'
	s += 'td.inputbox {border: 1px dotted #aaa;}'
	s += '</style>'
	s += '</head>'
	s += '<body>'
	s += document.getElementById('cim_show').innerHTML
	s += '</body>'
	s += '</html>'

	var w = window.open();

	w.document.open();
	w.document.write(s);
	w.document.close();

	w.print();
	w.close();
}
//-->
</script>

<div class="<?php echo COM_COMPONENT; ?>">

<div class="componentheading cim_menu">
	<?php include('components/'.COM_COMPONENT.'/views/menu.php'); ?>

	<?php echo JText::_('MOSTRAR UN MENSAJE'); ?>
</div>

<div class="cim_actions">
<form action="<?php echo COM_OPTION ?>" method="post" name="adminForm" id="form">
<input id="id" name="id" type="hidden" value="<?php echo $this->items->id; ?>" />

<?php $field = $this->items->fromusername === $this->user->username ? 'from' : 'to' ?>
<input id="field" name="field" type="hidden" value="<?php echo $field; ?>" />

<?php if ($field == 'to') { ?>
<button type="button" onclick="submitbutton('unread')" title="<?php echo JText::_('MARCAR COMO NO LEIDO'); ?>"><?php echo JHTML::_('image.site', 'note_pinned.gif', DIR_IMAGES, null, null, JText::_('MARCAR COMO NO LEIDO')); ?></button><br />
<?php if ($this->items->hidden_to) { ?>
<button type="button" onclick="submitbutton('unhiddento')" title="<?php echo JText::_('DESMARCAR COMO OCULTO'); ?>"><?php echo JHTML::_('image.site', 'yinyang.gif', DIR_IMAGES, null, null, JText::_('DESMARCAR COMO OCULTO')); ?></button><br />
<?php } else { ?>
<button type="button" onclick="submitbutton('hiddento')" title="<?php echo JText::_('MARCAR COMO OCULTO'); ?>"><?php echo JHTML::_('image.site', 'yinyang.gif', DIR_IMAGES, null, null, JText::_('MARCAR COMO OCULTO')); ?></button><br />
<?php } ?>
<?php if (!@in_array($this->items->id_from, $this->nomailusers)) { ?>
<button type="button" onclick="submitbutton('reply')" title="<?php echo JText::_('RESPONDER'); ?>"><?php echo JHTML::_('image.site', 'note_edit.gif', DIR_IMAGES, null, null, JText::_('RESPONDER')); ?></button><br />
<?php if (is_array($this->items->id_to)) { ?>
<button type="button" onclick="submitbutton('replyall')" title="<?php echo JText::_('RESPONDER A TODOS'); ?>"><?php echo JHTML::_('image.site', 'note_edit_all.gif', DIR_IMAGES, null, null, JText::_('RESPONDER A TODOS')); ?></button><br />
<?php } ?>
<input type="hidden" name="reply" value="only" />
<?php } ?>
<?php } else { ?>
<?php if ($this->items->hidden_from) { ?>
<button type="button" onclick="submitbutton('unhiddenfrom')" title="<?php echo JText::_('DESMARCAR COMO OCULTO'); ?>"><?php echo JHTML::_('image.site', 'yinyang.gif', DIR_IMAGES, null, null, JText::_('DESMARCAR COMO OCULTO')); ?></button><br />
<?php } else { ?>
<button type="button" onclick="submitbutton('hiddenfrom')" title="<?php echo JText::_('MARCAR COMO OCULTO'); ?>"><?php echo JHTML::_('image.site', 'yinyang.gif', DIR_IMAGES, null, null, JText::_('MARCAR COMO OCULTO')); ?></button><br />
<?php } ?>
<?php } ?>
<button type="button" onclick="showprint()" title="<?php echo JText::_('IMPRIMIR'); ?>"><?php echo JHTML::_('image.site', 'printer.gif', DIR_IMAGES, null, null, JText::_('IMPRIMIR')); ?></button><br />
<button type="submit" title="<?php echo JText::_('VOLVER'); ?>"><?php echo JHTML::_('image.site', 'back.gif', DIR_IMAGES, null, null, JText::_('VOLVER')); ?></button><br />

<input type="hidden" name="option" value="<?php echo COM_COMPONENT ?>" />
<input type="hidden" name="controller" value="<?php echo COMPONENT ?>" />
<input type="hidden" name="view" value="list" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="scope" value="<?php echo $this->scope; ?>" />
<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
<input type="hidden" name="who" value="<?php echo $this->who; ?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</div>

<div id="cim_show" class="cim_show">
<table>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('DE'); ?>: </label></td>
		<td class="inputbox"><span title="<?php echo $this->items->fromusername; ?>"><?php echo $this->escape($this->items->fromname); ?></span></td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('A'); ?>: </label></td>
		<td class="inputbox">
		<?php
			if (is_array($this->items->id_to)) {
				$comma = false;
				foreach ($this->items->id_to as $to) {
					if ($comma) {
						echo ', ';
					} else {
						$comma = true;
					}
					$bold = $to->readed ? '' : 'bold';
					echo '<span class="'.$bold.'" title="'.$to->username.'">'.$this->escape($to->name).'</span>';
				}
			} else {
				echo '<span title="'.$this->items->tousername.'">'.$this->escape($this->items->toname).'</span>';
			}
		?>
		</td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('FECHA'); ?>: </label></td>
		<td class="inputbox"><?php echo $this->items->date; ?></td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('ASUNTO'); ?>: </label></td>
		<td class="inputbox"><?php echo $this->escape($this->items->subject); ?></td>
	</tr>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('TEXTO'); ?>: </label></td>
		<td class="inputbox"><?php echo $this->items->text; ?></td>
	</tr>
	<?php if ($this->items->attachment_size) { ?>
	<tr>
		<td class="key"><label for="text"><?php echo JText::_('ADJUNTO'); ?>: </label></td>
		<td class="inputbox"><a href="<?php echo JRoute::_(COM_OPTION.'&controller='.COMPONENT.'&view=show&task=download&id='.$this->items->id_refered); ?>"><?php echo $this->escape($this->items->attachment_name); ?></a> (<?php echo $this->items->attachment_size; ?> bytes)</td>
	</tr>
	<?php } ?>
</table>
</div>

</div>
