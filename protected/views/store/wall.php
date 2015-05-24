<h1><?php echo $this->pageTitle; ?></h1>
<?php
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($contact);
?>

<fieldset>
	<legend><?php t('Contact information'); ?></legend>
	<p class="note"><?php t('The information you provide here will be saved to your account used as your contact information.'); ?></p>
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
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Proceed').' »')); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$returnUrl)); ?>
</div>

<?php $this->endWidget(); ?>