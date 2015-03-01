<p class="reportSelector">
	<a href="#" onclick="window.print(); return false;"><?php t('Print'); ?></a>
	|
	<?php if( !$showQueued ) {
		echo CHtml::link(g('Show queued messages'),$this->createUrl('',CMap::mergeArray($this->actionParams,array('showQueued'=>true))));
	} else {
		echo CHtml::link(g('Hide queued messages'),$this->createUrl('',CMap::mergeArray($this->actionParams,array('showQueued'=>false))));
	} ?>
	|
	<?php if( !$showDeleted ) {
		echo CHtml::link(g('Show deleted messages'),$this->createUrl('',CMap::mergeArray($this->actionParams,array('showDeleted'=>true))));
	} else {
		echo CHtml::link(g('Hide deleted messages'),$this->createUrl('',CMap::mergeArray($this->actionParams,array('showDeleted'=>false))));
	} ?>
</p>
<div class="report">
<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
<?php
foreach( $items as $item ) {
	echo '<h2>'.CHtml::encode($item['title']).'</h2>';
	if( $item['type'] == 'poll' ) {
		echo '<div class="poll" style="width: 100%;">';
		$max = 1;
		foreach( $item['poll']->Choices as $choice ) {
			$max = max($choice->voteCount,$max);
		}
		foreach( $item['poll']->Choices as $choice ) {
			echo '<p>';
			echo '<strong>'.CHtml::encode($choice->text).'</strong>';
			echo ' ('.$choice->voteCount.')<br />';
			echo '<img style="width: '.(round($choice->voteCount*90/$max)).'%; height: 16px;" src="'.Yii::app()->baseUrl.'/images/choiceBar.png" />';
			echo '</p>';
		}
		echo '</div>';
	} else {
		foreach( $item['messages'] as $message ) {
			echo '<p class="message">';
			echo '<span class="time">'.date('j.n. H:i',$message['timestamp']).'</span>';
			if( $message['twitter_username'] ) {
				echo ' <span class="username">@'.CHtml::encode($message['twitter_username']).'</span>';
			} else if( $message['username'] ) {
				echo ' <span class="username">'.CHtml::encode($message['username']).'</span>';
			}
			echo ' - ';
			echo '<span class="text">'.CHtml::encode($message['text']).'</span>';
			foreach( $message['images'] as $image ) {
				$src = $image['media_url'];
				if( in_array('thumb',$image['sizes']) ) {
					$src .= ':thumb';
				}
				echo '<span class="image">'.CHtml::image($src,$message['text']).'</span>';
			}
			echo '</p>';
		}
	}
}
?>
</div>