<?php
require_once('IRouter.php');

class GenericRouter implements IRouter
{
	/**
	 * Contructor sets up the router allowing for patters to be
	 * instantiated
	 *
	 * @param array associative array of $patterns
	 */
	public function __construct($patterns)
	{

	}

	/**
	* @inherit-docs
	*/
	public function GetUrl($controller,$method,$params = '')
	{
		throw new Exception('Not Implemented');
	}

	/**
	 * @inherit-docs
	 */
	public function GetRoute($uri = "")
	{
		throw new Exception('Not Implemented');
	}

	/**
	 * @inherit-docs
	 */
	public function GetUri()
	{
		throw new Exception('Not Implemented');
	}

}

?>