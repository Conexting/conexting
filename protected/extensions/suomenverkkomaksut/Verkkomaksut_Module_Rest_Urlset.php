<?php
/**
 * Urlset object describes all return urls used with the service
 */
class Verkkomaksut_Module_Rest_Urlset
{
	public $successUrl;
	public $failureUrl;
	public $notificationUrl;
	public $pendingUrl;
	
	
	public function __construct($successUrl, $failureUrl, $notificationUrl, $pendingUrl = null)
	{
		$this->successUrl = $successUrl;
		$this->failureUrl = $failureUrl;
		$this->notificationUrl = $notificationUrl;
		$this->pendingUrl = $pendingUrl;
	}
}
