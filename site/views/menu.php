	<div>
		<a href="<?php echo JRoute::_(COM_OPTION.'&view=list&Itemid='.$this->Itemid); ?>" title="<?php echo JText::_('LISTA DE MENSAJES'); ?>">
			<?php echo JHTML::_('image.site', 'outbox.gif', DIR_IMAGES, null, null, JText::_('LISTA DE MENSAJES')); ?>
		</a>
		<a href="<?php echo JRoute::_(COM_OPTION.'&view=new&Itemid='.$this->Itemid); ?>" title="<?php echo JText::_('NUEVO MENSAJE'); ?>">
			<?php echo JHTML::_('image.site', 'mail_write.gif', DIR_IMAGES, null, null, JText::_('NUEVO MENSAJE')); ?>
		</a>
		<a href="<?php echo JRoute::_(COM_OPTION.'&view=groups&Itemid='.$this->Itemid); ?>" title="<?php echo JText::_('GRUPOS'); ?>">
			<?php echo JHTML::_('image.site', 'users2.gif', DIR_IMAGES, null, null, JText::_('GRUPOS')); ?>
		</a>
	</div>
