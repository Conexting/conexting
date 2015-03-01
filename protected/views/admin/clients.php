<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Create new client'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/client',array('id'=>0))
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'filter'=>$filter,
	'columns'=>array(
		array('name'=>'clientid','header'=>'#','filter'=>CHtml::activeTextField($filter,'clientid',array('size'=>2))),
		'name',
		'email',
		array(
			'name'=>'created',
			'value'=>'$data->creationTime'
		),
		array(
			'name'=>'accessed',
			'value'=>'$data->accessTime'
		),
		array(
			'name'=>'WallCount',
			'filter'=>''
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewClient",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
