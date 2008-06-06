<?php
/** @package    verysimple::Payment */

/**
 * PaymentRequest is a generic object containing information necessary
 * to process a payment through a payment gateway.  To ensure that
 * your PaymentRequest can be processed independantly of payment gateway,
 * it is best not to extend this class, rather write a PaymentProcessor
 * that will work correctly for the particular payment gateway.
 *
 * @package    verysimple::Payment
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class PaymentRequest
{
	public $SerialNumber = "";
	public $DeveloperSerialNumber = "";
	public $CustomerFirstName = "";
	public $CustomerLastName = "";
	public $CustomerStreetAddress = "";
	public $CustomerCity = "";
	public $CustomerState = ""; 
	public $CustomerZipCode = ""; 
	public $CustomerCountry = "USA"; 
	public $CustomerPhone = "";
	public $CustomerEmail = "";
	public $OrderNumber = "";
	public $CustomerIP = "";
	public $Comment;
	public $TransactionAmount = "";
	public $CCNumber = "";
	public $CCExpMonth = "";
	public $CCExpYear = "";
	public $CCSecurityCode = "";
	
	// authorize.net specific
	public $TransactionType = "AUTH_CAPTURE";

	// skipjack specific
	public $OrderString = "1~None~0.00~0~N~||";
	public $CustomerName = "";
	public $OrderDescription; // 22 chars (xxx*xxxxxxxxxxxxxxxxxx || xxxxxxx*xxxxxxxxxxxxxx || xxxxxxxxxxxx*xxxxxxxxx)

	/**
	 * Constructor
	 */
	final function PaymentRequest()
	{
		$this->Init();
	}
	
	/**
	 * Called by base object on construction.  override this
	 * to handle any special initialization
	 */
	function Init()
	{
		$this->CustomerIP = $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * This will populate all properties from the provided array argument
	 * Example: $req->Read($_REQUEST)
	 *
	 * @param array $arr
	 */
	function Read($arr)
	{
		foreach (get_object_vars($this) as $prop)
		{
			if (array_key_exists($prop,$arr))
			{
				$this->$prop = $arr[$prop];
			}
		}
	}
}

?>