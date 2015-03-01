<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Create new {type}',array('{type}'=>lcfirst(g('Wall')))),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/wall',array('id'=>0))
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'filter'=>$filter,
	'columns'=>array(
		array(
			'name'=>'name',
			'value'=>'CHtml::link($data->name,$data->url).($data->premium ? " <i class=\"icon-star-empty\" title=\"'.g('Premium wall').'\"></i>" : "").($data->published ? "" : " <i class=\"icon-eye-close\" title=\"'.g('Wall is not published').'\"></i>")',
			'type'=>'raw',
			'cssClassExpression'=>'$data->deleted ? "deleted" : ""',
		),
		array(
			'name'=>'created',
			'value'=>'$data->creationDate',
		),
		array(
			'name'=>'expires',
			'value'=>'$data->expirationDate',
		),
		'sms',
		array(
			'name'=>'hashtag',
			'value'=>'$data->twitter',
		),
		array(
			'name'=>'clientid',
			'header'=>g('Client'),
			'value'=>'$data->Client->str'
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewWall",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
