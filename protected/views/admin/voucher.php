<?php /* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<fieldset>
	<legend><?php t('Coupon details'); ?></legend>
	<?php echo $form->textFieldRow($coupon,'code'); ?>
	<?php echo $form->textFieldRow($coupon,'discount'); ?>
	<?php echo $form->dropDownListRow($coupon,'resellerid',array(''=>'-')+CHtml::listData(Reseller::model()->findAll(),'resellerid','name')); ?>
	<?php echo $form->checkBoxRow($coupon,'active'); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/coupon'))); ?>
</div>

<?php $this->endWidget(); ?>