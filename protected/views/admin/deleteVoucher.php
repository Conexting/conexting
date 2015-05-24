<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
	'attributes'=>array(
		array('name'=>'name'),
		array('name'=>'code'),
		array('name'=>'active'),
	),
)); ?>

<div class="form-actions">
<?php echo $form->hiddenField($record,'voucherid'); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Remove voucher'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/voucher'))); ?>
</div>

<?php $this->endWidget(); ?>