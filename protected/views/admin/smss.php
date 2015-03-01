<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'columns'=>array(
		'timestamp'=>array(
			'value'=>function($data){
				if(!is_null($data->timestamp)){
					return date('j.n.Y H:i:s',$data->timestamp);
				} else {
					return null;
				}
			}
		),
		'operator',
		'source',
		'keyword',
		'text',
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewSms",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
