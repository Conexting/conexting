<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'inline',
	'htmlOptions'=>array('class'=>'well'),
)); ?>
<fieldset>
	<?php echo $form->textFieldRow($login,'name',array('class'=>'input-small')); ?>
	<?php echo $form->passwordFieldRow($login,'password',array('class'=>'input-small')); ?>
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Log in'))); ?>
</fieldset>
<?php $this->endWidget(); ?>
