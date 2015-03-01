<?php
if( is_null($this->width) && is_null($this->height) ) {
	$this->width = 640;
	$this->height = 480;
} else if( is_null($this->width) ) {
	$this->width = $this->height * 640 / 480;
} else if( is_null($this->height) ) {
	$this->height = $this->width * 480 / 640;
}
?>
<div class="streamContainer">
	<iframe class="stream"
		src="http://embed.bambuser.com/channel/<?php echo $this->streamId; ?>?chat=0"
		width="<?php echo $this->width; ?>"
		height="<?php echo $this->height; ?>"
		frameborder="0"></iframe>
</div>