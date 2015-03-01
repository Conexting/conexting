<div class="well">
<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'inline',
)); ?>

<fieldset>
	<legend><?php t('Password required'); ?></legend>
	<?php echo $form->passwordFieldRow($model,'password'); ?>
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Ok'))); ?>
</fieldset>

<?php $this->endWidget(); ?>
</div>
