<div class="row-fluid">
	<div class="span6">
		<h2><?php t('Account details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
    'name',
    'email',
    array(
      'name'=>'created',
      'value'=>date('j.n.Y H:i:s',$record->created)
    ),
    array(
      'name'=>'modified',
      'value'=>$record->modified ? date('j.n.Y H:i:s',$record->modified) : null
    ),
    array(
      'name'=>'accessed',
      'value'=>$record->accessed ? date('j.n.Y H:i:s',$record->accessed) : null
    ),
  ),
)); ?>
	</div>

	<div class="span6">
	<h2><?php t('Contact information'); ?></h2>
<?php
if( $record->Contact ) {
	$this->widget('bootstrap.widgets.TbDetailView',array(
		'data'=>$record->Contact,
		'nullDisplay'=>'<span class="muted">-</span>',
		'attributes'=>array(
			'forname',
			'surname',
			'street',
			'zipcode',
			'zip',
			'phone',
			'mobile',
			'organization',
			array(
				'name'=>'country',
				'value'=>Yii::app()->params["countries"][$record->Contact->country]
			),
      array(
        'name'=>'modified',
        'value'=>date('j.n.Y H:i:s',$record->Contact->modified)
      )),
	));
} else {
	echo '<div class="alert">';
	echo g('No contact information available.');
	echo '</div>';
}
?>
	</div>
</div>

<h2><?php t('Walls'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>new CActiveDataProvider('Wall',array(
		'criteria'=>array(
			'condition'=>'clientid=:clientid',
			'params'=>array(
				':clientid'=>$record->primaryKey
			),
		),
		'sort'=>array(
			'defaultOrder'=>array(
				'expires'=>CSort::SORT_DESC,
				'created'=>CSort::SORT_DESC,
			)
		),
	)),
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
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewWall",array("id"=>$data->primaryKey))',
		),
	),
)); ?>

<?php if( $record->Licenses ) { ?>
<h2><?php t('Licenses'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>new CActiveDataProvider('License',array(
		'criteria'=>array(
			'condition'=>'clientid=:clientid',
			'params'=>array(
				':clientid'=>$record->primaryKey
			),
		),
		'sort'=>array(
			'defaultOrder'=>array(
				'expires'=>CSort::SORT_DESC,
			)
		),
	)),
	'columns'=>array(
		array(
			'name'=>'expires',
			'value'=>'$data->expirationDate',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {update} {delete}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewWall",array("id"=>$data->primaryKey))',
			'updateButtonUrl'=>'Yii::app()->createUrl("admin/wall",array("id"=>$data->primaryKey))',
			'deleteButtonUrl'=>'Yii::app()->createUrl("admin/deleteWall",array("id"=>$data->primaryKey))',
			'buttons'=>array(
				'delete'=>array(
					'click'=>'function(){}'
				)
			)
		),
	),
)); ?>
<?php } ?>

<?php if( $record->Contact ) { ?>
<h2><?php t('Payments'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>new CActiveDataProvider('Payment',array(
		'criteria'=>array(
			'condition'=>'contactid=:contactid',
			'params'=>array(':contactid'=>$record->Contact->primaryKey),
			'order'=>'created DESC'
		),
	)),
	'columns'=>array(
		array('name'=>'paymentid','header'=>'#'),
		array(
			'name'=>'created',
			'value'=>'$data->creationTime'
		),
		array(
			'name'=>'paid',
			'value'=>'$data->paidTime'
		),
		array(
			'name'=>'total',
			'footer'=>$record->Contact->paymentSum
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view}',
			'viewButtonUrl'=>'Yii::app()->createUrl("admin/viewPayment",array("id"=>$data->primaryKey))',
		),
	),
)); ?>
<?php } ?>
