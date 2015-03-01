<?php
/**
 * Verkkomaksut exception is a normal PHP exception. Using an inherited
 * class allows catching only Verkkomaksut exceptions with try-catch clause.
 */
class Verkkomaksut_Exception extends Exception
{
	public function __construct($message)
	{
		parent::__construct("Verkkomaksut exception: ".$message);
	}
}
