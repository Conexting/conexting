<?php
class StreamView extends CWidget {
	public $stream;
	public $width = null;
	public $height = null;
	public $providerId;
	public $streamId;
	public $allowFullscreen = true;
	
	public function run() {
		return $this->render('streamEmbed/'.$this->providerId);
	}
}
