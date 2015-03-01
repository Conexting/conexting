<h2><?php t('Payment details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
		array(
			'name'=>'contactid',
			'type'=>'raw',
			'value'=>$record->Contact->Client->name.' '.CHtml::link('<i class="icon-eye-open"></i>',$this->createUrl('admin/viewClient',array('id'=>$record->Contact->Client->primaryKey)))
		),
    'code',
		'title',
		'price',
		'amount',
		array(
			'name'=>'coupon',
			'value'=>$record->Coupon->code,
		),
    array(
			'name'=>'discount',
			'value'=>$record->discount.' %'
		),
		'total'
  ),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Edit payment'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/payment',array('id'=>$record->primaryKey))
)); ?>
