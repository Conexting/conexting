<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($question);
?>

<fieldset>
	<legend><?php t('Question details'); ?></legend>
	<?php
	echo $form->textFieldRow($question,'keyword',array('class'=>'input-medium','hint'=>g('The question is answered using this keyword, e.g. "<em>keyword</em> my example answer"')));
	if( !$question->isNewRecord ) {
		//echo '<p class="alert alert-warning"><strong>'.g('Note!').'</strong> '.g('If the keyword is changed, previous answers will not be visible in this question.').'</p>';
	}
	?>
	<?php echo $form->textFieldRow($question,'title',array('class'=>'input-xxlarge','hint'=>g('The title is used in menus'))); ?>
	<?php echo $form->textFieldRow($question,'question',array('class'=>'input-xxlarge')); ?>
	<?php echo $form->textFieldRow($question,'position',array('class'=>'input-mini','hint'=>g('Position determines the order in which questions are shown in the menu.').' '.g('Set position to empty to hide the link to this question from the menu.'))); ?>
	<?php
	if( $this->wall->enablesms ) {
		echo $form->checkBoxRow($question,'smsdefault',array('hint'=>g('All SMS-messages are directed to this question.')));
	}
	?>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl($this->id.'/admin'))); ?>
</div>

<?php $this->endWidget(); ?>