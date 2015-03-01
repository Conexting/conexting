<?php
/**
 * Payment object represents the actual payment to be transmitted
 * to Suomen Verkkomaksut interface
 * 
 * E1 references to Suomen Verkkomaksut interface version E1, which
 * is extended and recommended version.
 */
class Verkkomaksut_Module_Rest_Payment_E1 extends Verkkomaksut_Module_Rest_Payment
{
	private $_contact;
	private $_products = array();
	private $_includeVat = 1;
	
	
	public function __construct($orderNumber, Verkkomaksut_Module_Rest_Urlset $urlset, Verkkomaksut_Module_Rest_Contact $contact)
	{
		parent::__construct($orderNumber, $urlset);
		
		$this->_orderNumber = $orderNumber;
		$this->_contact = $contact;
		$this->_urlset = $urlset;
	}
	
	
	/**
	 * Use this function to add each order product to payment.
	 * 
	 * Please group same products using $amount. Verkkomaksut
	 * supports up to 500 product rows in a single payment.
	 * 
	 * @param string $title
	 * @param string $no
	 * @param float $amount
	 * @param float $price
	 * @param float $tax
	 * @param flaot $discount
	 * @param int $type
	 */
	public function addProduct($title, $no, $amount, $price, $tax, $discount, $type = 1)
	{
		if(sizeof($this->_products)>=500) {
			throw new Verkkomaksut_Exception("Verkkomaksut can only handle up to 500 different product rows. Please group products using product amount.");
		}
		
		$this->_products[] = new Verkkomaksut_Module_Rest_Product($title, $no, $amount, $price, $tax, $discount, $type);
	}
	
	
	/**
	 * @return Verkkomaksut_Module_E1_Contact contact data for this payment
	 */
	public function getContact()
	{
		return $this->_contact;
	}
	
	
	/**
	 * @return array List of Verkkomaksut_Module_E1_Product objects for this payment
	 */
	public function getProducts()
	{
		return $this->_products;
	}

	
	/**
	 * You can decide whether you wish to use taxless prices (mode=0) or
	 * prices which include taxes. Default mode is 1 (taxes are in prices).
	 * 
	 * You should always use the same mode that your web shop uses - otherwise
	 * you will get problems with rounding since SV supports prices with only
	 * 2 decimals.
	 * 
	 * @param int $mode
	 */
	public function setVatMode($mode)
	{
		$this->_includeVat = $mode;
	}
	
	
	/**
	 * @return int Vat mode attached to this payment
	 */
	public function getVatMode()
	{
		return $this->_includeVat;
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
			"orderDetails" => array(
				"includeVat" => $this->getVatMode(),
				"contact" => array(
					"telephone" => $this->getContact()->telNo,
					"mobile" => $this->getContact()->cellNo,
					"email" => $this->getContact()->email,
					"firstName" => $this->getContact()->firstName,
					"lastName" => $this->getContact()->lastName,
					"companyName" => $this->getContact()->company,
					"address" => array(
						"street" => $this->getContact()->addrStreet,
						"postalCode" => $this->getContact()->addrPostalCode,
						"postalOffice" => $this->getContact()->addrPostalOffice,
						"country" => $this->getContact()->addrCountry
					)
				),
				"products" => array()
			)
		);

		foreach($this->getProducts() as $product) {
			$data["orderDetails"]["products"][] = array(
				"title" => $product->title,
				"code" => $product->code,
				"amount" => $product->amount,
				"price" => $product->price,
				"vat" => $product->vat,
				"discount" => $product->discount,
				"type" => $product->type
			);
		}
		
		return $data;
	}
}
