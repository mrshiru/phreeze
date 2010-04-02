<?php
/** @package    verysimple::Payment */

/** import supporting libraries */
require_once("PaymentProcessor.php");

/**
 * PayPal extends the generic PaymentProcessor object to process
 * a PaymentRequest through the PayPal direct payment API.
 *
 * @package    verysimple::Payment
 * @author     VerySimple Inc.
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.1
 */
class PayPal extends PaymentProcessor
{
	// used by paypal - 'sandbox' or 'beta-sandbox' or 'live'
	private $environment = 'sandbox';	

	
	/**
	* Called on contruction
	* @param bool $test  set to true to enable test mode.  default = false 
	*/
	function Init($testmode)
	{
		// set the post url depending on whether we're in test mode or not
		$this->environment = $testmode ? 'sandbox' : 'live'; 
	}
	
	/**
	* Process a PaymentRequest
	* @param PaymentRequest $req Request object to be processed
	* @return PaymentResponse
	*/
	function Process(PaymentRequest $req)
	{
		$resp = new PaymentResponse();
		$resp->OrderNumber = $req->OrderNumber;
		
		// post to paypal service
		// Set request-specific fields.
		$paymentType = $req->TransactionType == PaymentRequest::$TRANSACTION_TYPE_AUTH_CAPTURE 
			? urlencode('Sale') 
			: urlencode('Authorization') ;
		$firstName = urlencode($req->CustomerFirstName);
		$lastName = urlencode($req->CustomerLastName);
		$creditCardType = urlencode($req->CCType);
		$creditCardNumber = urlencode($req->CCNumber);
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($req->CCExpMonth, 2, '0', STR_PAD_LEFT));
		
		$expDateYear = urlencode($req->CCExpYear);
		
		if ( strlen($expDateYear) < 4 )
		{
			$expDateYear = "20" . $expDateYear;
		}
		
		$cvv2Number = urlencode($req->CCSecurityCode);
		$address1 = urlencode($req->CustomerStreetAddress);
		$address2 = urlencode($req->CustomerStreetAddress2);
		$city = urlencode($req->CustomerCity);
		$state = urlencode($req->CustomerState);
		$zip = urlencode($req->CustomerZipCode);
		$country = urlencode($req->CustomerCountry);	// US or other valid country code
		$amount = urlencode($req->TransactionAmount);
		$currencyID = urlencode($req->TransactionCurrency);		// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// Add request-specific fields to the request string.
		$nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
					"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
					"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
		
		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr);
		
		if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		{
			$resp->IsSuccess = true;
			$resp->TransactionId = urldecode( $this->GetArrayVal($httpParsedResponseAr,"TRANSACTIONID") );
			$resp->ResponseCode = urldecode( "AVSCODE=" . $this->GetArrayVal($httpParsedResponseAr,"AVSCODE") . ",CVV2MATCH=" . $this->GetArrayVal($httpParsedResponseAr,"CVV2MATCH"));
			$resp->ResponseMessage = urldecode( "Charge of " . $this->GetArrayVal($httpParsedResponseAr,"AMT") . " Posted" );
		} 
		else  
		{
			$resp->IsSuccess = false;
			$resp->ResponseCode = urldecode( $this->GetArrayVal($httpParsedResponseAr,"L_SEVERITYCODE0")  . " " . $this->GetArrayVal($httpParsedResponseAr,"L_ERRORPARAMVALUE0") );
			$resp->ResponseMessage = urldecode($this->GetArrayVal($httpParsedResponseAr,"L_SHORTMESSAGE0")  . ": " .  $this->GetArrayVal($httpParsedResponseAr,"L_LONGMESSAGE0") );

			// paypal's response is a bit wordy.  remove this part of the message
			$resp->ResponseMessage = str_replace("Invalid Data: This transaction cannot be processed.","",$resp->ResponseMessage);
		}
				
		$resp->RawResponse = "";
		$resp->ParsedResponse = $httpParsedResponseAr;
		
		return $resp;
	}
	
	/**
	 * Util to return array values without throwing and undefined message
	 *
	 * @param Array $arr
	 * @param variant $key
	 * @param variant $not_defined_val value to return if array key doesn't exist
	 * @return variant
	 */
	private function GetArrayVal($arr,$key,$not_defined_val = "")
	{
		return array_key_exists($key,$arr) 
			? $arr[$key]
			: $not_defined_val;
	}
		

	/**
	 * Send HTTP POST Request.  Throws an Exception if the server communication failed
	 *
	 * @param	string	The API method name
	 * @param	string	The POST Message fields in &name=value pair format
	 * @return	array	Parsed HTTP Response body
	 */
	private function PPHttpPost($methodName_, $nvpStr_) {
	
		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName = urlencode($this->Username);
		$API_Password = urlencode($this->Password);
		$API_Signature = urlencode($this->Signature);
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
		
		if ("sandbox" === $this->environment || "beta-sandbox" === $this->environment) 
		{
			$API_Endpoint = "https://api-3t." . $this->environment . ".paypal.com/nvp";
		}
		
		$version = urlencode('51.0');
	
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
	
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
	
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
		// Get response from the server.
		$httpResponse = curl_exec($ch);
	
		if(!$httpResponse) {
			throw new Exception("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
	
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
	
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
	
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			throw new Exception("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	
		return $httpParsedResponseAr;
	}
	

}

?>