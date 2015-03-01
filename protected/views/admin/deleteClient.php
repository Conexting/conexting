<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
	'attributes'=>array(
		array('name'=>'name'),
		array('name'=>'email'),
		array('name'=>'creationTime'),
		array('name'=>'modificationTime')
	),
)); ?>

<div class="form-actions">
<?php echo $form->hiddenField($record,'clientid'); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Remove client'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/client'))); ?>
</div>

<?php $this->endWidget(); ?>