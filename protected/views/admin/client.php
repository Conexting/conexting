<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<fieldset>
	<legend><?php t('Client details'); ?></legend>
	<?php echo $form->textFieldRow($client,'name'); ?>
	<?php echo $form->textFieldRow($client,'email'); ?>
</fieldset>
<fieldset>
	<legend><?php t('Contact information'); ?></legend>
	<?php echo $form->textFieldRow($contact,'forname'); ?>
	<?php echo $form->textFieldRow($contact,'surname'); ?>
	<?php echo $form->textFieldRow($contact,'street'); ?>
	<?php echo $form->textFieldRow($contact,'zipcode'); ?>
	<?php echo $form->textFieldRow($contact,'zip'); ?>
	<?php echo $form->textFieldRow($contact,'phone'); ?>
	<?php echo $form->textFieldRow($contact,'mobile'); ?>
	<?php echo $form->textFieldRow($contact,'organization'); ?>
	<?php echo $form->dropDownListRow($contact,'country',Yii::app()->params['countries']); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/client'))); ?>
</div>

<?php $this->endWidget(); ?>