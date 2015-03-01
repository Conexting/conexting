<?php
class Verkkomaksut_Module_Rest_Payment_S1 extends Verkkomaksut_Module_Rest_Payment
{
	private $_price;
	
	
	public function __construct($orderNumber, $urlset, $price)
	{
		parent::__construct($orderNumber, $urlset);
		$this->_price = $price;
	}
	
	
	public function getPrice()
	{
		return $this->_price;
	}
	
	
	/**
	 * Get payment data as array
	 * 
	 * @return array REST API compatible payment data
	 * @throws Verkkomaksut_Exception
	 */
	public function getJsonData()
	{
		$data = array(
			"orderNumber" => $this->getOrderNumber(),
			"referenceNumber" => $this->getCustomReferenceNumber(),
			"description" => $this->getDescription(),
			"currency" => $this->getCurrency(),
			"locale" => $this->getLocale(),
			"urlSet" => array(
				"success" => $this->getUrlset()->successUrl,
				"failure" => $this->getUrlset()->failureUrl,
				"pending" => $this->getUrlset()->pendingUrl,
				"notification" => $this->getUrlset()->notificationUrl
			),
			"price" => $this->getPrice()
		);
		
		return $data;
	}
}
