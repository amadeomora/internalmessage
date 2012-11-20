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

<div class="<?php echo COM_COMPONENT; ?>">

<div class="componentheading cim_menu">
	<?php include('components/'.COM_COMPONENT.'/views/menu.php'); ?>
	<?php echo JText::_('LISTA DE MENSAJES'); ?>
</div>

<div class="cim_nav">
	<form action="<?php echo COM_OPTION ?>" method="post">
	<input type="hidden" name="option" value="<?php echo COM_COMPONENT ?>" />
	<input type="hidden" name="controller" value="<?php echo COMPONENT ?>" />
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />

	<div style="float:right;">
	<?php echo JText::_('PAGINA'); ?>
	<select name="page" id="page" onchange="submit();" class="inputbox">
	<?php
		for ($i=1; $i<=$this->count/MSG_PER_PAGE+1; $i++) {
			$selected = $i == $this->page ? 'selected="selected"' : '';
			echo '<option '.$selected.'>'.$i.'</option>';
		}
	?>
	</select>
	</div>

	<?php echo $this->count.' '.JText::_('MENSAJES'); ?>
	<?php
		$ambitos = array(SCP_TODOS, SCP_RECIBIDOS, SCP_NO_LEIDOS, SCP_ENVIADOS, SCP_OCULTOS);
	?>
	<select name="scope" onchange="document.getElementById('page').value=1;submit();" class="inputbox">
	<?php
		for ($i=0; $i<count($ambitos); $i++) {
			$selected = $ambitos[$i] == $this->scope ? 'selected="selected"' : '';
			echo '<option value="'.$ambitos[$i].'" '.$selected.'>'.JText::_($ambitos[$i]).'</option>';
		}
	?>
	</select>

	<?php echo JText::_('CON'); ?>
	<select name="who" onchange="document.getElementById('page').value=1;submit();" class="inputbox">
	<?php
	$selected = $this->who == 0 ? ' selected="selected"' : '';
	echo '<option value="0"'.$selected.'>'.JText::_('Cualquiera').'</option>';
	for ($i=0; $i<count($this->users); $i++) {
		$u = $this->users[$i];
		if ($this->user->id == $u->id) continue; //<==
		$selected = $u->id == $this->who ? ' selected="selected"' : '';
		//echo '<option value="'.$u->id.'"'.$selected.'>'.$this->escape($u->name).' ('.$u->username.')</option>';
		echo '<option value="'.$u->id.'"'.$selected.'>'.$this->escape($u->name).'</option>';
	}
	echo '<optgroup label=""></optgroup>';
	for ($i=0; $i<count($this->nomailusers); $i++) {
		$u = $this->nomailusers[$i];
		if ($this->user->id == $u->id) continue; //<==
		$selected = $u->id == $this->who ? ' selected="selected"' : '';
		//echo '<option value="'.$u->id.'"'.$selected.'>'.$this->escape($u->name).' ('.$u->username.')</option>';
		echo '<option value="'.$u->id.'"'.$selected.'>'.$this->escape($u->name).'</option>';
	}
	?>
	</select>

	</form>
</div>

<div class="cim_list">
	<table cellpadding="3" width="100%">
	<thead>
		<tr>
			<th width="20" class="sectiontableheader"></th>
			<th class="sectiontableheader">
				<?php echo JText::_('DE'); ?> / <?php echo JText::_('A'); ?>
			</th>
			<th class="sectiontableheader"><?php echo JText::_('ASUNTO'); ?></th>
			<th class="sectiontableheader"><?php echo JText::_('FECHA'); ?></th>
		</tr>
	</thead>

<?php
	for ($i=0; $i<count($this->items); $i++) {
		$row = &$this->items[$i];
		$linkShow = JRoute::_(COM_OPTION.'&view=show&id='.$row->id.'&scope='.$this->scope.'&page='.$this->page.'&who='.$this->who.'&Itemid='.$this->Itemid);
		$class = 'sectiontableentry'.(($i%2)+1);
		$field = $row->id_from == $this->user->id ? 'from' : 'to';
		?>
		<tr class="<?php echo $class; ?>">
			<td width="20">
				<?php
				$readed = $row->readed;
				if ($row->id_from == $this->user->id && is_array($row->id_to)) {
					for ($j=0; $readed && $j<count($row->id_to); $j++) {
						$readed = $row->id_to[$j]->readed;
					}
				}
				if ($readed) {
					$activarCheckBox = true;
					echo '<input name="id[]" type="checkbox" value="to,'.$row->id.'" />';
				} else {
					echo JHTML::_('image.site', 'mail_new.gif', DIR_IMAGES, null, null, JText::_('>>'), array('width'=>20,'height'=>20,'title'=>JText::_('MENSAJE NO LEIDO')));
				}
				?>
			</td>
			<td>
				<?php
				if ($row->id_from != $this->user->id) {
					echo JText::_('De');
					echo ': <span title="'.$row->username.'">'.$this->escape($row->name).'</span><br />';
				}
				$alt = $row->id_from == $this->user->id ? '' : JText::_(' MI ').',';
				if (is_array($row->id_to)) {
					$alt = '';
					for ($j=0; $j<count($row->id_to); $j++) {
						$to = $row->id_to[$j];
						$alt .= $to->name.' ('.$to->username.')'.',';
					}
					$alt = substr($alt, 0, -1);
					echo JText::_('A');
					echo ': <span title="'.$this->escape($alt).'">'.JText::_(' VARIOS ').'</span>';
				} else {
					if ($row->id_from == $this->user->id) {
						echo JText::_('A');
						echo ': <span title="'.$row->username.'">'.$this->escape($row->name).'</span>';
					}
				}
				?>
			</td>
			<td>
				<?php
				if ($row->attachment_size) {
					echo '<div class="attachment">';
					echo JHTML::_('image.site', 'mail_attachment.png', DIR_IMAGES, null, null, JText::_('ADJ'), array('title'=>JText::_('TIENE ARCHIVOS ADJUNTOS')));
					echo '</div>';
				}
				?>
				<a href="<?php echo $linkShow; ?>"><?php echo $this->escape($row->subject); ?></a>
			</td>
			<td><?php echo $row->date; ?></td>
		</tr>
		<?php
	}
?>

	</table>

	<?php
		if (count($this->items) == 0) {
			echo '<span class="nomessage">'.JText::_('NO HAY NINGUN MENSAJE').'</span>';
		}
	?>

</div>

</div>
