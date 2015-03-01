<?php /* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<fieldset>
	<legend><?php t('Payment details'); ?></legend>
	<?php echo $form->textFieldRow($payment,'title'); ?>
	<?php echo $form->textFieldRow($payment,'amount'); ?>
	<?php echo $form->textFieldRow($payment,'price'); ?>
	<?php echo $form->textFieldRow($payment,'confirmed',array('value'=>$payment->confirmed ? date('j.n.Y H:i:s',$payment->confirmed) : '')); ?>
	<?php echo $form->textFieldRow($payment,'paid',array('value'=>$payment->paid ? date('j.n.Y H:i:s',$payment->paid) : '')); ?>
</fieldset>
<fieldset>
	<legend><?php t('Contact information'); ?></legend>
	<p><?php echo CHtml::encode($payment->Contact->name); ?></p>
	<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Edit client'),'url'=>$this->createUrl('admin/client',array('id'=>$payment->Contact->clientid)))); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/payment'))); ?>
</div>

<?php $this->endWidget(); ?>