<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
	'attributes'=>array(
		array('name'=>'code'),
		array('name'=>'discount'),
		array('name'=>'resellerid'),
		array('name'=>'active'),
	),
)); ?>

<div class="form-actions">
<?php echo $form->hiddenField($record,'couponid'); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Remove coupon'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/coupon'))); ?>
</div>

<?php $this->endWidget(); ?>