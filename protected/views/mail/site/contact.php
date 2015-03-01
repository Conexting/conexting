<h3><?php t('New contact from Conexting online form'); ?></h3>
<p>
	<strong><?php t('Name'); ?>:</strong> <?php echo CHtml::encode($contact->name) ?><br />
	<strong><?php t('Email'); ?>:</strong> <?php echo CHtml::encode($contact->email) ?>
</p>
<p>
	<?php echo nl2br(CHtml::encode($contact->message)); ?>
</p>
