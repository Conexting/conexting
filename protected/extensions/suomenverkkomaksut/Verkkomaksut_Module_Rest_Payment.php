<?php
abstract class Verkkomaksut_Module_Rest_Payment
{
	private $_orderNumber;
	private $_urlset;
	private $_referenceNumber = "";
	private $_description = "";
	private $_currency = "EUR";
	private $_locale = "fi_FI";
	
	
	public function __construct($orderNumber, $urlset)
	{
		$this->_orderNumber = $orderNumber;
		$this->_urlset = $urlset;
	}
	
	
	/**
	 * @return string order number for this payment
	 */
	public function getOrderNumber()
	{
		return $this->_orderNumber;
	}
	
	
	/**
	 * @return Verkkomaksut_Module_E1_Urlset payment return url object for this payment
	 */
	public function getUrlset()
	{
		return $this->_urlset;
	}
	
	
	/**
	 * You can set a reference number for a payment but it is *not* recommended.
	 * 
	 * Reference number set using this function will only be used for interface payments.
	 * Interface payment means a payment done with such a payment method that is used
	 * with own contract (using Verkkomaksut only as a technical API). If payment is made
	 * with payment method that is used directly with Verkkomaksut contract, this value
	 * is not used - instead Verkkomaksut uses auto generated reference number.
	 * 
	 * Using custom reference number may be useful if you need to automatically confirm
	 * payments paid directly to your own account with your own contract. With custom
	 * reference number you can match payments with it.
	 * 
	 * @param $referenceNumber Customer reference number
	 */
	public function setCustomReferenceNumber($referenceNumber)
	{
		$this->_referenceNumber = $referenceNumber;
	}
	
	
	/**
	 * @return string Custom reference number attached to this payment
	 */
	public function getCustomReferenceNumber()
	{
		return $this->_referenceNumber;
	}
	
	
	/**
	 * Change used locale. Locale affects language and number and date presentation formats.
	 * 
	 * Verkkomaksut supports currently three locales: Finnish (fi_FI), English (en_US)
	 * and Swedish (sv_SE). Default locale is fi_FI.
	 * 
	 * @param string $locale
	 */
	public function setLocale($locale)
	{
		if(!in_array($locale, array("fi_FI", "en_US", "sv_SE"))) {
			throw new Verkkomaksut_Exception("Given locale is unsupported.");
		}
		
		$this->_locale = $locale;
	}
	
	
	/**
	 * @return string Locale attached to this payment
	 */
	public function getLocale()
	{
		return $this->_locale;
	}
	
	
		
	/**
	 * Set non-default currency. Currently the default currency (EUR) is only supported
	 * value.
	 * 
	 * @param $currency Currency in which product prices are given
	 */
	public function setCurrency($currency)
	{
		if($currency != "EUR") {
			throw new Verkkomaksut_Exception("Currently EUR is the only supported currency.");
		}
		
		$this->_currency = $currency;
	}
	
	
	/**
	 * @return string Currency attached to this payment
	 */
	public function getCurrency()
	{
		return $this->_currency;
	}
	
	
	/**
	 * You may optionally set description for the payment. This message
	 * will only be visible in merchant's panel with the payment - nowhere else.
	 * It allows you to save additional data with payment when necessary.
	 * 
	 * @param string $description Private payment description
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
	}
	
	
	/**
	 * @return string Description attached to this payment
	 */
	public function getDescription()
	{
		return $this->_description;
	}
	
	
	/**
	 * Get payment data as array
	 * 
	 * @return array REST API compatible payment data
	 * @throws Verkkomaksut_Exception
	 */
	public function getJsonData()
	{
		throw new Verkkomaksut_Exception("Verkkomaksut_Module_Rest_Payment is not meant to be used directly. Use E1 or S1 module instead.");
	}
}
