<h1><?php t('Payment history'); ?></h1>
<h2><?php t('Confirmed payments'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$payments,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array(
			'name'=>'paid',
			'value'=>'$data->paidTime'
		),
		'title',
		'price',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'$data->nextStepUrl',
		),
	),
)); ?>

<?php if( !empty($pending->data) ) { ?>
<h2><?php t('Pending payments'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$pending,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array(
			'name'=>'created',
			'value'=>'$data->creationTime'
		),
		'title',
		'price',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'$data->nextStepUrl',
		),
	),
)); ?>
<?php } ?>