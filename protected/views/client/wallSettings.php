<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

$premiumBadge = '<a class="badge badge-premium pull-left" data-toggle="modal" href="#premiumInfo" '
	.'title="'.g('This is a Premium feature').'" rel="tooltip">'
	.g('Premium').'</a>';
$premiumText = '<a class="badge badge-premium" data-toggle="modal" href="#premiumInfo" '
	.'title="'.g('This is a Premium feature').'" rel="tooltip">'
	.g('Premium').'</a>';

echo $form->errorSummary($wall);
?>

<fieldset>
	<legend><?php t('Wall settings'); ?></legend>
	
	<?php if( !$wall->published ) { ?>
	<?php echo $form->textFieldRow($wall,'name',array('prepend'=>$this->createAbsoluteUrl('wall/index',array('wall'=>'','language'=>null)),'hint'=>g('You can change the wall name before publishing the wall.'))); ?>
	<?php } else { ?>
	<?php echo $form->uneditableRow($wall,'name',array('prepend'=>$this->createAbsoluteUrl('wall/index',array('wall'=>'','language'=>null)),'hint'=>g('Wall name cannot be changed after it has been published.'))); ?>
	<?php } ?>
	
	<?php echo $form->textFieldRow($wall,'title'); ?>
	<?php echo $form->checkBoxRow($wall,'index',array('hint'=>g('Allow this wall to show in Google search and other web search engines.'))); ?>
</fieldset>

<fieldset>
	<legend><?php t('Conversation'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'threaded',array('hint'=>g('Show a "reply" option and show replies under the original message.'))); ?>
</fieldset>

<fieldset>
	<legend><?php t('Passwords'); ?></legend>
	<?php echo $form->textFieldRow($wall,'adminpassword',array('hint'=>g('Wall admin can remove messages, add questions and polls, view reports etc using this password'))); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),$form->textFieldRow($wall,'password',array('hint'=>g('Password required to view the wall. Leave empty for no password required.')))); ?>
</fieldset>

<fieldset>
	<legend><?php t('Twitter'); ?></legend>
	<?php echo $form->checkBoxRow($wall,'enabletwitter',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),$form->textFieldRow($wall,'hashtag',array('prepend'=>'#'))); ?>
	<div class="toggled">
		<div class="control-group">
			<?php echo CHtml::activeLabelEx($wall,'TwitterUser',array('class'=>'control-label')); ?>
			<div class="controls">
				<p id="Wall_TwitterUser">
				<?php
				if( $wall->TwitterUser ) {
					echo '<strong>'.$wall->TwitterUser->screen_name;
					echo '</strong> ';
					$this->widget('bootstrap.widgets.TbButton',array('htmlOptions'=>array('name'=>'sign-out-twitter'),'buttonType'=>'submit','label'=>'Sign out from Twitter'));
				} else {
					$this->widget('bootstrap.widgets.TbButton',array('htmlOptions'=>array('name'=>'sign-in-twitter','class'=>'sign-in'),'buttonType'=>'submit','encodeLabel'=>false,'label'=>CHtml::image(Yii::app()->baseUrl.'/images/sign-in-with-twitter-gray.png','Sign in with Twitter')));
				}
				?>
				</p>
				<p class="help-block">
					<?php t('Tweets from the wall website and SMS are sent using this account.'); ?>
				</p>
				<p class="help-block">
					<?php echo $premiumText; ?> -  
					<?php t('You can also use the shared twitter account, but it will result in longer delay when fetching tweets.'); ?>
				</p>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend><?php t('Moderating'); ?></legend>
	<?php echo $premiumBadge; ?>
	<?php echo $form->checkBoxRow($wall,'premoderated',array('hint'=>g('If checked, all messages must be approved by wall admin before they are shown on the wall.'))); ?>
</fieldset>
<fieldset>
	<legend><?php t('SMS'); ?></legend>
	<?php echo $premiumBadge; ?>
	<?php echo $form->checkBoxRow($wall,'enablesms',array('class'=>'toggler')); ?>
	<?php echo CHtml::tag('div',array('class'=>'toggled'),
		$form->dropDownListRow($wall,'smskeyword',Yii::app()->user->getKeywordChoices())
		.$form->textFieldRow($wall,'smsprefix',array('hint'=>g('Visitors can send SMS messages by prepending their message with keyword and prefix.')))
	); ?>
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
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','label'=>g('Cancel'),'htmlOptions'=>array('name'=>'cancel'))); ?></div>

<?php $this->endWidget(); ?>