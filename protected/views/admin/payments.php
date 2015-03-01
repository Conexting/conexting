<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'filter'=>$filter,
	'columns'=>array(
		array('name'=>'paymentid','header'=>'#','filter'=>CHtml::activeTextField($filter,'paymentid',array('size'=>2))),
		array(
			'name'=>'created',
			'value'=>'$data->creationTime'
		),
		array(
			'name'=>'paid',
			'value'=>'$data->paidTime'
		),
		'title',
		'amount',
		array(
			'name'=>'contact',
			'value'=>'$data->Contact->name',
			'filter'=>''
		),
		array('name'=>'total','filter'=>''),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewPayment",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
