<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Create new voucher'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/voucher',array('id'=>0))
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'filter'=>$filter,
	'columns'=>array(
		array('name'=>'voucherid','header'=>'#','filter'=>CHtml::activeTextField($filter,'voucherid',array('size'=>2))),
		'code',
		'walllength',
		array(
			'name'=>'expires',
			'value'=>'$data->expirationDate',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewCoupon",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
