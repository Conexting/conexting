<?php
/**
 * This class provides a helper class for using Verkkomaksut service.
 * It is implemented in PHP but it should be quite easily portable to
 * other languages as well.
 * 
 * @example
 * 
 * require_once("Verkkomaksut_Module_Rest.php");
 * 
 * $urlset = new Verkkomaksut_Module_Rest_Urlset(
 *      "https://www.demoshop.com/sv/success",	// success url
 *      "https://www.demoshop.com/sv/failure",	// failure url
 *      "https://www.demoshop.com/sv/notify",	// notify url
 *      "https://www.demoshop.com/sv/pending"	// pending url
 * );
 * $contact = new Verkkomaksut_Module_Rest_Contact(
 * 		"Test",									// first name
 * 		"Person",								// last name
 * 		"test.person@democompany.com",			// email
 * 		"Test street 1",						// street address
 * 		"12340",								// postal code
 * 		"Helsinki",								// postal city
 * 		"FI",									// country (ISO-3166)
 * 		"040123456",							// telephone number
 * 		"",										// mobile number
 * 		"Demo Company Ltd"						// company name
 * );
 * 
 * $orderNumber = "1";							// Use unique order number
 * $payment = new Verkkomaksut_Module_Rest_Payment($orderNumber, $contact, $urlset);
 * $payment->addProduct(
 * 		"Test product"							// product title
 * 		"01234",								// product number/code
 * 		"1.00",									// number of these products
 * 		"19.90",								// Price (/one item)
 * 		"23.00",								// Tax percentage
 * 		"0.00",									// Discount percentage
 * 		Verkkomaksut_Module_Rest_Product::TYPE_NORMAL	// Normal product row					
 * );
 * // Add more product rows when necessary
 * 
 * // Submit product to Verkkomaksut
 * $module = new Verkkomaksut_Module_Rest(13466, "");
 * try {
 * 		$result = $module->processPayment($payment);
 * }
 * catch(Verkkomaksut_Exception $e) {
 * 		// handle error
 * }
 * 
 * // Use payment url and token as you wish
 * header("Location: {$result->url}");
 * 
 * @version 1.0, 2011-05-06
 * @author Jussi Kari, Suomen Verkkomaksut
 */


/**
 * Main module
 */
class Verkkomaksut_Module_Rest
{
	const SERVICE_URL = "https://payment.verkkomaksut.fi";
	
	
	private $_merchantId = "";
	private $_merchantSecret = "";

	
	/**
	 * Initialize module with your own merchant id and merchant secret.
	 * 
	 * While building and testing integration, you can use demo values
	 * (merchantId = 13466, merchantSecret = ...)
	 * 
	 * @param int $merchantId
	 * @param string $merchantSecret
	 */
	public function __construct($merchantId, $merchantSecret)
	{
		$this->_merchantId = $merchantId;
		$this->_merchantSecret = $merchantSecret;
	}

	
	/**
	 * @return Module version as a string
	 */
	public function getVersion()
	{
		return "1.0";
	}
	
	
	/**
	 * Get url for payment
	 * 
	 * @param Verkkomaksut_Module_E1_Payment $payment
	 * @throws Verkkomaksut_Exception
	 * @return Verkkomaksut_Module_E1_Result
	 */
	public function processPayment(Verkkomaksut_Module_Rest_Payment &$payment)
	{
		$url = self::SERVICE_URL."/token/json";	
		
		$data = $payment->getJsonData();
		
		// Create data array
		$url = self::SERVICE_URL."/api-payment/create";	
		
		$result = $this->_postJsonRequest($url, json_encode($data));
		
		if($result->httpCode != 201) {
			if($result->contentType == "application/xml") {
				$xml = simplexml_load_string($result->response);
				throw new Verkkomaksut_Exception($xml->errorMessage, $xml->errorCode);
			}
			else if($result->contentType == "application/json") {
				$json = json_decode($result->response);
				throw new Verkkomaksut_Exception($json->errorMessage, $json->errorCode);
			}
		}
		$data = json_decode($result->response);
		
		if(!$data) {
			throw new Verkkomaksut_Exception("Module received non-JSON answer from server", "unknown-error");
		}

		return new Verkkomaksut_Module_Rest_Result($data->token, $data->url);
	}
	
	

	
	
	/**
	 * This function can be used to validate parameters returned by return and notify requests.
	 * Parameters must be validated in order to avoid hacking of payment confirmation.
	 * This function is usually used like:
	 * 
	 * $module = new Verkkomaksut_Module_E1($merchantId, $merchantSecret);
	 * if($module->validateNotifyParams($_GET["ORDER_NUMBER"], $_GET["TIMESTAMP"], $_GET["PAID"], $_GET["METHOD"], $_GET["AUTHCODE"])) {
	 *   // Valid notification, confirm payment
	 * }
	 * else {
	 *   // Invalid notification, possibly someone is trying to hack it. Do nothing or create an alert.
	 * }
	 * 
	 * @param string $orderNumber
	 * @param int $timeStamp
	 * @param string $paid
	 * @param int $method
	 * @param string $authCode
	 */
	public function confirmPayment($orderNumber, $timeStamp, $paid, $method, $authCode)
	{
		if( $paid ) {
			$base = "{$orderNumber}|{$timeStamp}|{$paid}|{$method}|{$this->_merchantSecret}";
		} else {
			$base = "{$orderNumber}|{$timeStamp}|{$this->_merchantSecret}";
		}
		return $authCode == strtoupper(md5($base));
	}
	

	/**
	 * This method submits given parameters to given url as a post request without
	 * using curl extension. This should require minimum extensions
	 * 
	 * @param $url
	 * @param $params
	 * @throws Verkkomaksut_Exception
	 */
	private function _postJsonRequest($url, $content)
	{
		// Check that curl is available
		if(!function_exists("curl_init")) {
			throw new Verkkomaksut_Exception("Curl extension is not available. Verkkomaksut_Module_Rest requires curl.");
		}
		
		// Set all the curl options
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json',
        	'X-Verkkomaksut-Api-Version: 1'
		));
		curl_setopt($ch, CURLOPT_USERPWD, $this->_merchantId . ':' . $this->_merchantSecret);
		curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Read result, including http code
	 	$result = new StdClass();
        $result->response = curl_exec($ch);
        $result->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result->contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        // Got no status code?
        $curlError = $result->httpCode > 0 ? null : curl_error($ch).' ('.curl_errno($ch).')';

        curl_close($ch);
        
        // Connection failure
        if ($curlError) {
            throw new Verkkomaksut_Exception('Connection failure. Please check that payment.verkkomaksut.fi is reachable from your environment ('.$curlError.')');
        }
		
		return $result;
	}
}
