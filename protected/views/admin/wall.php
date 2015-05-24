<?php 
/* @var $form TbActiveForm */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($wall);
?>

<fieldset>
	<legend><?php t('Wall administration'); ?></legend>
	<?php echo $form->dropDownListRow($wall,'clientid',array(''=>'-')+CHtml::listData(Client::model()->findAll(),'clientid','str')); ?>
	<?php echo $form->textFieldRow($wall,'publishingTime',array('type'=>'datetime')); ?>
	<?php echo $form->textFieldRow($wall,'expirationTime',array('type'=>'datetime')); ?>
	<?php echo $form->textFieldRow($wall,'dyingTime',array('type'=>'datetime')); ?>
	<?php echo $form->checkBoxRow($wall,'premium'); ?>
	<?php echo $form->checkBoxRow($wall,'hidden'); ?>
	<?php echo $form->textFieldRow($wall,'smscredit'); ?>
</fieldset>

<fieldset>
	<legend><?php t('Wall settings'); ?></legend>
	<?php echo $form->textFieldRow($wall,'name',array('prepend'=>$this->createAbsoluteUrl('wall/index',array('wall'=>'','language'=>null)))); ?>
	<?php echo $form->textFieldRow($wall,'title'); ?>
	<?php echo $form->checkBoxRow($wall,'index'); ?>
</fieldset>

<fieldset>
	<legend><?php t('Conversation'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'threaded'); ?>
</fieldset>

<fieldset>
	<legend><?php t('Passwords'); ?></legend>
	<?php echo $form->textFieldRow($wall,'adminpassword'); ?>
	<?php echo $form->textFieldRow($wall,'password'); ?>
</fieldset>
<fieldset>
	<legend><?php t('SMS'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'enablesms',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),
		$form->dropDownListRow($wall,'smskeyword',Yii::app()->user->getKeywordChoices(true))
		.$form->textFieldRow($wall,'smsprefix')
	); ?>
</fieldset>
<fieldset>
	<legend><?php t('Twitter'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'enabletwitter',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),$form->textFieldRow($wall,'hashtag',array('prepend'=>'#'))); ?>
	<div class="toggled">
		<div class="control-group">
			<label class="control-label" for="twitter_account"><?php t('Twitter account'); ?></label>
			<div class="controls">
				<p id="twitter_account">
				<?php
				if( $wall->TwitterUser ) {
					echo '<strong>'.$wall->TwitterUser->screen_name.'</strong> ';
				}
				?>
				</p>
			</div>
		</div>
	</div>
</fieldset>
<fieldset>
	<legend><?php t('Stream'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'enablestream',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),
		$form->dropDownListRow($wall,'streamprovider',Yii::app()->params['streamProviders'])
		.$form->textFieldRow($wall,'streamid',array('hint'=>g('Consult stream provider help to aquire stream id')))
	); ?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/viewWall',array('id'=>$wall->primaryKey)))); ?>
</div>

<?php $this->endWidget(); ?>