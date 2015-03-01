<h3><?php t('Welcome to Conexting!'); ?></h3>
<p>
	<?php t('You have successfully created your Conexting account.'); ?>
	<?php t('Please use the following account name and password to log in.'); ?>
</p>
<p>
	<strong><?php t('Account name'); ?>:</strong> <?php echo $client->name ?><br />
	<strong><?php t('Account email'); ?>:</strong> <?php echo $client->email ?><br />
	<strong><?php t('Password'); ?>:</strong> <?php echo $password; ?>
</p>
<p>
	<strong><?php echo CHtml::link(g('Log in to your account'),$this->createAbsoluteUrl('client/login')); ?></strong>
</p>
<p>
	<?php t('You can try Conexting for free by creating your Free wall.'); ?>
</p>
