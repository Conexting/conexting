<h2><?php t('Coupon details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
		array(
			'name'=>'resellerid',
			'value'=>$record->Reseller ? $record->Reseller->name : null
		),
    'code',
		array(
			'name'=>'active',
			'value'=>$record->active ? g('Yes') : g('No')
		),
    array(
			'name'=>'discount',
			'value'=>$record->discount.' %'
		),
  ),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Edit coupon'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/coupon',array('id'=>$record->primaryKey))
)); ?>

<h2><?php t('Payments'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>new CActiveDataProvider('Payment',array('criteria'=>array(
		'condition'=>'couponid=:couponid AND paid IS NOT NULL',
		'params'=>array(':couponid'=>$record->primaryKey)
	))),
	'columns'=>array(
		array('name'=>'paymentid','header'=>'#'),
		'code',
		array(
      'name'=>'paid',
      'value'=>'date("j.n.Y H:i:s",$data->paid)'
    ),
		array(
			'name'=>'total',
			'footer'=>$record->paymentSum
		),
	),
)); ?>
