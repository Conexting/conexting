<?php
if( !function_exists('lcfirst') ) {
	function lcfirst($str) {
		return (string)(strtolower(substr($str,0,1)).substr($str,1));
	}
}

/**
 * Echoes translated message
 * @param type $message
 * @param type $params
 * @param type $category
 * @param type $source
 * @param type $language 
 */
function t($message,$params=array(),$category='ui',$source=null,$language=null) {
	echo Yii::t($category,$message,$params,$source,$language);
}

/**
 * Returns translated message
 * @param type $message
 * @param type $params
 * @param type $category
 * @param type $source
 * @param type $language
 * @return type 
 */
function g($message,$params=array(),$category='ui',$source=null,$language=null) {
	return Yii::t($category,$message,$params,$source,$language);
}

/**
 * Sets a flash message for current user
 * @param type $message
 * @param type $category
 * @return type 
 */
function f($message,$category='info') {
	$flash = '';
	if( Yii::app()->user->hasFlash($category) ) {
		$flash .= Yii::app()->user->getFlash($category).'<br />';
	}
	$flash .= $message;
	return Yii::app()->user->setFlash($category,$flash);
}

/**
 * Handle all errors as exceptions 
 */
set_error_handler(
	create_function(
		'$severity, $message, $file, $line',
		'throw new ErrorException($message, $severity, $severity, $file, $line);'
	),E_STRICT
);
