<div class="well span5 offset2">
<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'vertical',
));

echo $form->errorSummary($client);
?>
<fieldset>
	<legend><?php t('Sign up for your free Conexting account'); ?></legend>
	<?php echo $form->textFieldRow($client,'email'); ?>
	<?php echo $form->textFieldRow($client,'name'); ?>
</fieldset>
<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Sign up'))); ?>
</div>
<?php $this->endWidget(); ?>
	<p>
		<?php t('Already have an account?'); ?>
		<?php echo CHtml::link(g('Log in'),$this->createUrl('client/login')); ?>
	</p>
</div>