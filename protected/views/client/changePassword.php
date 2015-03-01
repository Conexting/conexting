<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<fieldset>
	<legend><?php t('Change password'); ?></legend>
	<?php echo $form->passwordFieldRow(Yii::app()->user->client,'password_new'); ?>
	<?php echo $form->passwordFieldRow(Yii::app()->user->client,'password_confirm'); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('client/index'))); ?>
</div>

<?php $this->endWidget(); ?>