<?php
/**
 * Product object acts as a payment products. There is one product object
 * for each product row. Product objects are automatically generated when
 * payment function addProduct is called. You never need to directly work
 * with product objects.
 */
class Verkkomaksut_Module_Rest_Product
{
	const TYPE_NORMAL = 1;
	const TYPE_POSTAL = 2;
	const TYPE_HANDLING = 3;
	
	
	public $title;
	public $code;
	public $amount;
	public $price;
	public $vat;
	public $discount;
	public $type;
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $title
	 * @param string $code
	 * @param float $amount
	 * @param float $price
	 * @param flaot $vat
	 * @param float $discount
	 * @param int $type
	 */
	public function __construct($title, $code, $amount, $price, $vat, $discount, $type)
	{
		$this->title = $title;
		$this->code = $code;
		$this->amount = $amount;
		$this->price = $price;
		$this->vat = $vat;
		$this->discount = $discount;
		$this->type = $type;
	}
}
