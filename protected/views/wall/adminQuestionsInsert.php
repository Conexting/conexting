<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));

echo $form->errorSummary($questions);

?>

<fieldset>
	<legend><?php t('Question details'); ?></legend>
	<div class="row">
		<p class="hint"><?php t('One question per line.'); ?> <?php t('Format: <pre>keyword;title;question</pre>'); ?></p>
		<?php echo CHtml::textArea('questions',$_REQUEST['questions']); ?>
	</div>
</fieldset>

<div class="form-actions">
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Save'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','label'=>g('Cancel'),'url'=>$this->createUrl($this->id.'/admin'))); ?>
</div>

<?php $this->endWidget(); ?>