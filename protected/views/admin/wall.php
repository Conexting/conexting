<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($wall);
?>

<fieldset>
	<legend><?php t('Wall settings'); ?></legend>
	<?php echo $form->dropDownListRow($wall,'clientid',array(''=>'-')+CHtml::listData(Client::model()->findAll(),'clientid','str')); ?>
	<?php echo $form->textFieldRow($wall,'name',array('prepend'=>$this->createAbsoluteUrl('wall/index',array('wall'=>'')))); ?>
	<?php echo $form->checkBoxRow($wall,'premium'); ?>
	<?php echo $form->textFieldRow($wall,'title'); ?>
	<?php echo $form->dropDownListRow($wall,'theme',Yii::app()->params['themes']); ?>
	<?php echo $form->checkBoxRow($wall,'published',array('hint'=>g('Check this when you want to publish the wall and receive messages.'))); ?>
	<?php echo $form->checkBoxRow($wall,'index',array('hint'=>g('Allow this wall to show in Google search and other web search engines.'))); ?>
	<?php echo $form->checkBoxRow($wall,'premoderated',array('hint'=>g('If checked, all messages must be approved by wall admin before they are shown on the wall.'))); ?>
	<?php echo $form->textFieldRow($wall,'expirationTime'); ?>
</fieldset>
<fieldset>
	<legend><?php t('Passwords'); ?></legend>
	<?php echo $form->textFieldRow($wall,'adminpassword',array('hint'=>g('Wall admin can remove messages, add questions and polls, view reports etc using this password'))); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),$form->textFieldRow($wall,'password',array('hint'=>g('Password required to view the wall. Leave empty for no password required.')))); ?>
</fieldset>
<fieldset>
	<legend><?php t('SMS'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'enablesms',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),
		$form->dropDownListRow($wall,'smskeyword',Yii::app()->user->getKeywordChoices(true))
		.$form->textFieldRow($wall,'smsprefix',array('hint'=>g('Visitors can send SMS messages by prepending their message with keyword and prefix.')))
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
					echo CHtml::link(g('Sign out from Twitter'),$this->createUrl('client/disconnectTwitter',array('wallname'=>$wall->name)));
				} else {
					echo CHtml::link(CHtml::image(Yii::app()->baseUrl.'/images/sign-in-with-twitter-gray.png','Sign in with Twitter'),$this->createUrl('client/signInWithTwitter',array('wallname'=>$wall->name)));
				}
				?>
				</p>
				<p class="help-block">
					<?php t('Tweets from the wall website and SMS are sent using this account.'); ?>
					<?php t('You can also use the shared twitter account, but it will result in longer delay when fetching tweets.'); ?>
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
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl('admin/walls'))); ?>
</div>

<?php $this->endWidget(); ?>