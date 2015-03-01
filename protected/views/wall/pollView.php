<h1><?php echo $this->wall->title; ?></h1>
<h2><?php echo CHtml::encode($poll->question) ?></h2>
<?php
if( $this->wall->enablesms && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Vote by SMS to <b class="example">{number}</b> using format <b class="example">{prefix} <i>Choice letter</i></b> (notice spaces!)',array(
		'{number}'=>$poll->Wall->smsnumber,
		'{prefix}'=>trim(trim($poll->Wall->sms).' '.$poll->smsPrefix)
	)));
}
?>
<?php
$this->widget('PollView',array(
	'poll'=>$poll,
	'myChoice'=>$myChoice,
	'options'=>array(
		'url'=>strtr($this->createUrl('wall/poll',array('search'=>$poll->keyword,'cmd'=>'commandPlaceholder')),array('commandPlaceholder'=>'{cmd}')),
		'allowVoting'=>false,
	)
));
?>
