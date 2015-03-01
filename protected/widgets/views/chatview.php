<?php
/* @var $this ChatView */
$baseUrl = Yii::app()->baseUrl;
$basePath = Yii::app()->basePath;
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.timeago.js?v='.filemtime($basePath.'/../js/jquery.timeago.js'));
Yii::app()->clientScript->registerScriptFile($baseUrl.'/js/jquery.chat.js?v='.filemtime($basePath.'/../js/jquery.chat.js'));
Yii::app()->clientScript->registerCssFile($baseUrl.'/css/jquery.chat.css?v='.filemtime($basePath.'/../css/jquery.chat.css'));

$chatScript = '$("#'.$this->id.'").chat('.CJavaScript::encode($this->options).');';
Yii::app()->clientScript->registerScript('chat_'.$this->id,$chatScript);
?>
<div class="chat" id="<?php echo $this->id; ?>">
	<?php if( $this->showImages ) { ?>
	<div class="image-box">
		<div class="images"></div>
	</div>
	<?php } ?>
	
	<?php if( $this->showMsg ) { ?>
	<div class="msg">
		<span class="my_username username"><?php echo CHtml::encode(Yii::app()->user->nickname); ?></span>: <a href="#" class="change-username"><?php t('Change nickname'); ?></a>
		<div class="input-append <?php if( $this->prependMsg ) echo 'input-prepend'; ?>">
			<?php if( $this->prependMsg ) echo '<span class="add-on hidden-phone">'.$this->prependMsg.'</span>'; ?>
			<input class="messageText" style="width: 85%;" type="text" name="text" placeholder="<?php t('Write your message here'); ?>" />
			<button class="send btn" style="" type="button"><i class="icon-share"></i> <span class="hidden-phone"><?php t('Send'); ?></span></button>
		</div>
	</div>
	<div class="msginfo"></div>
	<?php } ?>

	<?php if( $this->showSearch ) { ?>
	<div>
		<input class="search input-xlarge" data-content=".message-content" type="text" name="search" placeholder="<?php t('Search messages'); ?>" />
		<input class="search input-medium" data-content=".sender" type="text" name="search" placeholder="<?php t('Search usernames'); ?>" />
	</div>
	<?php } ?>

	<?php if( $this->showPinnedBox ) { ?>
		<div class="pinnedFeed"></div>
	<?php } ?>
	<div class="feed"></div>

	<?php if( $this->showMore ) { ?>
	<p class="show-more"><a class="readmore" href="#"><?php t('Show previous messages'); ?></a></p>
	<?php } ?>
</div>
