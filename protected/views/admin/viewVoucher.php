<h2><?php t('Voucher details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
		'name',
    'code',
		'expirationTime',
		'count',
		'countperclient',
		array(
			'name'=>'active',
			'value'=>$record->active ? g('Yes') : g('No')
		),
  ),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Edit voucher'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/voucher',array('id'=>$record->primaryKey))
)); ?>

<h2><?php t('Walls'); ?></h2>
<?php
$wallModel = new Wall;
$wallModel->showDeleted = true;
$this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>new CActiveDataProvider($wallModel,array(
		'criteria'=>array(
			'condition'=>'voucherid=:voucherid',
			'params'=>array(':voucherid'=>$record->primaryKey)
		),
		'sort'=>array(
			'defaultOrder'=>array(
				'created'=>CSort::SORT_DESC,
			)
		),
	)),
	'columns'=>array(
		array(
			'name'=>'wallid',
			'header'=>'#'
		),
		array(
			'name'=>'name',
			'cssClassExpression'=>'$data->deleted ? "deleted" : ""',
		),
		array(
			'name'=>'created',
			'value'=>'$data->creationDate',
		),
		array(
			'name'=>'client',
			'value'=>'$data->Client->email',
			'filter'=>''
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewWall",array("id"=>$data->primaryKey))',
		),
	),
));
$wallModel->showDeleted = false;
?>
