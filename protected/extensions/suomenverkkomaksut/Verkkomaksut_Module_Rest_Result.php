<?php
/**
 * This object is returned when a payment is processed to Suomen Verkkomaksut
 * It allows you to query for token or url
 */
class Verkkomaksut_Module_Rest_Result
{
	private $_token;
	private $_url;
	
	
	public function __construct($token, $url)
	{
		$this->_token = $token;
		$this->_url = $url;
	}
	
	
	public function getToken()
	{
		return $this->_token;
	}
	
	
	public function getUrl()
	{
		return $this->_url;
	}
}
