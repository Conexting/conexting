<?php
/* @var $this PollView */
$baseUrl = Yii::app()->baseUrl;
$basePath = Yii::app()->basePath;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.poll.js?v='.filemtime($basePath.'/../js/jquery.poll.js'));
Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.poll.css?v='.filemtime($basePath.'/../css/jquery.poll.css'));

$pollScript = '$("#'.$this->id.'").poll('.CJavaScript::encode($this->options).');';
Yii::app()->clientScript->registerScript('poll_'.$this->id,$pollScript);
?>
<div class="poll" id="<?php echo $this->id; ?>">
<?php
foreach( $this->poll->Choices as $choice ) {
	echo '<div class="choice '.(in_array($choice->choice,$this->myChoice) ? 'selected' : '').'" data-choice="'.$choice->choice.'">';
	echo '<span class="choiceText">';
	if( $this->showChoiceChar ) {
		echo $choice->char.': ';
	}
	echo CHtml::encode($choice->text);
	echo '</span>';
	echo '<span class="choiceCount">0</span>';
	$this->widget('bootstrap.widgets.TbProgress',array(
		'percent'=>0,
		'striped'=>in_array($choice->choice,$this->myChoice),
	));
	echo '</div>';
}
?>
</div>
