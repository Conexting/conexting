<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary(array($client,$contact));
?>

<fieldset>
	<legend><?php t('Account details'); ?></legend>
	<?php echo $form->uneditableRow($client,'email'); ?>
	<?php echo $form->textFieldRow($client,'name'); ?>
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
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('client/index'))); ?>
</div>

<?php $this->endWidget(); ?>