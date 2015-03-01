<?php
/* @var $this Controller */
$this->jsFile('common');
$this->jsFile(Yii::app()->language);
Yii::app()->bootstrap->register();

$cs = Yii::app()->clientScript;

// Font Awesome
$cs->registerCssFile('//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');

$this->cssFile('common');
$this->cssFile('theme',true);
$this->cssFile($this->layout);
$this->cssFile($this->layout,true);

$cs->registerMetaTag('Conexting',null,null,array('property'=>'og:site_name'));
$cs->registerMetaTag($this->createAbsoluteDefaultUrl('/images/conexting_og.png'),null,null,array('property'=>'og:image'));
$cs->registerMetaTag('image/png',null,null,array('property'=>'og:image:type'));
$cs->registerMetaTag('450',null,null,array('property'=>'og:image:width'));
$cs->registerMetaTag('260',null,null,array('property'=>'og:image:height'));
$cs->registerLinkTag('shortcut icon','image/png',$this->createDefaultUrl('/images/conexting_icon.png'));

if( $this->pageKeywords && !empty($this->pageKeywords) ) {
	if( is_array($this->pageKeywords) ) {
		$keywords = implode(',',$this->pageKeywords);
	} else {
		$keywords = $this->pageKeywords;
	}
	$cs->registerMetaTag($keywords,'keywords');
}
if( $this->pageDescription ) {
	$cs->registerMetaTag($this->pageDescription,'description');
	$cs->registerMetaTag($this->pageDescription,null,null,array('property'=>'og:description'));
}
?><!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="<?php echo Yii::app()->language; ?>" />
	<title><?php echo CHtml::encode($this->pageTitle); ?> - Conexting</title>
</head>
<body>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-38521965-1', 'conexting.com');
ga('require', 'linkid', 'linkid.js');
ga('send', 'pageview');
</script>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<div class="container<?php echo $this->layout == 'embed' || $this->layout == 'view' ? '-fluid' : ''; ?>" id="container">
	<?php echo $content; ?>
</div>
</body>
</html>
