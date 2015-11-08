<?php
/* @var $form TbActiveForm */
/* @var $poll Poll */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary(array_merge(array($poll),$choices));
?>

<fieldset>
	<legend><?php t('Poll details'); ?></legend>
	<?php	echo $form->textFieldRow($poll,'keyword',array('class'=>'input-medium','hint'=>g('When using SMS a vote is casted using this keyword, e.g. "<em>keyword</em> A"'))); ?>
	<?php echo $form->textFieldRow($poll,'title',array('class'=>'input-xxlarge','hint'=>g('The title is used in menus'))); ?>
	<?php echo $form->textFieldRow($poll,'question',array('class'=>'input-xxlarge')); ?>
	<?php echo $form->textFieldRow($poll,'position',array('class'=>'input-mini','hint'=>g('Position determines the order in which polls are shown in the menu.'))); ?>
	<?php
	if( $this->wall->enablesms ) {
		echo $form->checkBoxRow($poll,'smsdefault',array('hint'=>g('SMS-votes are directed to this poll by default (if no other poll is specified).')));
	}
	?>
	<?php echo $form->checkBoxRow($poll,'allowChoices',array('hint'=>g('Voters can vote for any number of given choices.'))); ?>
	<?php echo $form->checkBoxRow($poll,'allowVotes',array('hint'=>g('Voters can vote a choice multiple times.'))); ?>
  <?php echo $form->checkBoxRow($poll,'closed',array('hint'=>g('If the voting is closed, no more votes are accepted.'))); ?>
</fieldset>
<fieldset>
	<legend><?php t('Choices'); ?></legend>
	<?php
	if( !$poll->isNewRecord ) {
		echo $form->checkBoxRow($poll,'clearVotes',array('hint'=>g('Clear the the current voting results for this poll.')));
	}
	?>
	<?php
	foreach( $choices as $pollChoice ) {
		echo $form->textFieldRow($pollChoice,"[{$pollChoice->choice}]text",array('class'=>'input-xxlarge'));
	}
	?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl($this->id.'/admin'))); ?>
</div>

<?php $this->endWidget(); ?>