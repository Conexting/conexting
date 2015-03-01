<div class="row-fluid">
	<div class="span3">
		<?php $this->widget('bootstrap.widgets.TbMenu', array(
			'type'=>'list',
			'items'=>array(
				array('label'=>g('Clients'),'url'=>array('admin/client'),'icon'=>'book'),
				'---',
				array('label'=>g('Logout'),'url'=>array('admin/logout'),'icon'=>'off'),
			),
		)); ?>
	</div>
	<div class="span9">
		<h2><?php t('Admin tools'); ?></h2>
		<p><?php t('Use the menu on the left.'); ?></p>
	</div>
</div>