<h2><?php t('Send test notification'); ?>: <?php echo CHtml::encode($wall->name); ?></h2>
<?php 
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($notification);
?>

<?php echo $form->dropDownListRow($notification, 'notification', $notifications); ?>

<?php echo $form->textFieldRow($notification,'to'); ?>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Send'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/viewWall',array('id'=>$wall->primaryKey)))); ?>
</div>

<?php $this->endWidget(); ?>