<?php /* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<fieldset>
	<legend><?php t('Voucher details'); ?></legend>
	<?php echo $form->textFieldRow($voucher,'name'); ?>
	<?php echo $form->textFieldRow($voucher,'code'); ?>
	<?php echo $form->textFieldRow($voucher,'walllength'); ?>
	<?php echo $form->textFieldRow($voucher,'wallremovedafter'); ?>
	<?php echo $form->textFieldRow($voucher,'wallsmscredit'); ?>
	<?php echo $form->textFieldRow($voucher,'expirationTime',array('type'=>'datetime')); ?>
	<?php echo $form->textFieldRow($voucher,'count'); ?>
	<?php echo $form->textFieldRow($voucher,'countperclient'); ?>
	<?php echo $form->checkBoxRow($voucher,'active'); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/voucher'))); ?>
</div>

<?php $this->endWidget(); ?>