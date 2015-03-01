<div class="well span7 offset1">
<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'inline',
)); ?>
<fieldset>
	<legend><?php t('Log in with your client credentials'); ?></legend>
	<?php echo $form->textFieldRow($client,'email',array('class'=>'input-large')); ?>
	<?php echo $form->passwordFieldRow($client,'password',array('class'=>'input-small')); ?>
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Log in'))); ?>
</fieldset>
<?php $this->endWidget(); ?>
<p>
	<?php t('Forgot your password?'); ?>
	<?php echo CHtml::link(g('Reset forgotten password'),$this->createUrl('client/forgottenPassword')); ?>
</p>
<p>
	<?php t('Not registered yet?'); ?>
	<?php echo CHtml::link(g('Create your account'),$this->createUrl('client/signup')); ?>
</p>
</div>