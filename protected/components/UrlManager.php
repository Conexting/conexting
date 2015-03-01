<?php
class UrlManager extends CUrlManager {
	public function createUrl($route, $params = array(), $ampersand = '&') {
		// Add language parameter for all url:s
		if( array_key_exists('language',$params) ) {
			if( is_null($params['language']) || empty($params['language']) ) {
				unset($params['language']);
			}
		} else if( array_key_exists('language', Yii::app()->controller->actionParams) ) {
			$params['language'] = Yii::app()->language;
		}
		return parent::createUrl($route,$params,$ampersand);
	}
}
