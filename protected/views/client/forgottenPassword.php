<?php /** @var BootActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'inline',
	'htmlOptions'=>array('class'=>'well'),
)); ?>

<fieldset>
	<?php echo $form->textFieldRow($client,'email'); ?>
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Send email'))); ?>
</fieldset>

<?php $this->endWidget(); ?>