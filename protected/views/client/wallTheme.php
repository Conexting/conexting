<?php
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'type'=>'horizontal',
	'htmlOptions'=>array(
		'enctype'=>'multipart/form-data'
	)
)); ?>

<?php if( $wall->theme ) { ?>

<?php echo $form->errorSummary($wall->ThemeModel); ?>

<fieldset>
	<legend><?php t('Wall theme'); ?></legend>
	<p><?php t('To use this theme in another wall: <a href="{url}">create a new wall using this theme</a>.',array('{url}'=>$this->createUrl("client/createWall",array("copyFromWall"=>$wall->name)))); ?></p>
	<?php require_once(dirname(__FILE__).'/themeVariables/'.$wall->theme.'.php'); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Cancel'),'htmlOptions'=>array('name'=>'cancel'))); ?>
</div>

<?php } else { ?>
<div class="alert">
	<?php t('No theme available.'); ?>
</div>
<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Back'),'htmlOptions'=>array('name'=>'cancel'))); ?>
</div>
<?php } ?>

<?php $this->endWidget(); ?>