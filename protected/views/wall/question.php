<h1><?php echo $this->wall->title; ?></h1>
<h2><?php echo CHtml::encode($question->question) ?></h2>
<?php
if( $this->wall->enablesms && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Answer by SMS to <b class="example">{number}</b> using format <b class="example">{prefix} <i>Your answer...</i></b> (notice spaces!)',array(
		'{number}'=>$question->Wall->smsnumber,
		'{prefix}'=>trim($question->Wall->sms.' '.$question->smsPrefix)
	)));
}
if( $this->wall->enabletwitter && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Answer by Twitter using format <b class="example">{hashtag} {prefix} <i>Your answer...</i></b>',array(
		'{hashtag}'=>$question->Wall->twitter,
		'{prefix}'=>$question->keyword
	)));
}
?>
<?php
$this->widget('ChatView',array(
	'prependMsg'=>$question->keyword,
	'options'=>array(
		'url'=>strtr($this->createUrl('wall/chat',array('question'=>$question->primaryKey,'cmd'=>'commandPlaceholder')),array('commandPlaceholder'=>'{cmd}')),
		'isAdmin'=>$this->isWallAdmin(),
		'refreshRate'=>$this->wall->premium ? 10 : 20,
		'threaded'=>(bool)$this->wall->threaded,
	),
	'showMsg'=>!$this->wall->isExpired
));
?>
