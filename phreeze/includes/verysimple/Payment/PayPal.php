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
		$creditCardType = urlencode( trim($req->CCType) );
		$creditCardNumber = urlencode( trim($req->CCNumber) );
		
		// month needs to be two digits - padded with leading zero if necessary
		$padDateMonth = urlencode( trim(str_pad($req->CCExpMonth, 2, '0', STR_PAD_LEFT)) );
		
		// year needs to be full 4-digit
		$expDateYear = urlencode( trim($req->CCExpYear) );
		if ( strlen($expDateYear) < 4 )
		{
			/** HACK - THIS WILL BREAK IN THE YEAR 2100 **/
			$expDateYear = "20" . $expDateYear;
		}
		
		$email = (urlencode($req->CustomerEmail));
		$invoiceNum = (urlencode($req->InvoiceNumber));
		$orderDesc = (urlencode($req->OrderDescription));
		$cvv2Number = urlencode($req->CCSecurityCode);
		$address1 = urlencode($req->CustomerStreetAddress);
		$address2 = urlencode($req->CustomerStreetAddress2);
		$city = urlencode($req->CustomerCity);
		$state = urlencode($req->CustomerState);
		$zip = urlencode($req->CustomerZipCode);
		$amount = urlencode($req->TransactionAmount);
		$currencyID = urlencode($req->TransactionCurrency);		// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// soft descriptor can only be a max of 22 chars with no non-alphanumeric characters
		$softdescriptor = urlencode(substr(preg_replace("/[^a-zA-Z0-9\s]/", "", $req->SoftDescriptor),0,22 ));
		
		// legit country code list: https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_country_codes
		$country = urlencode( strtoupper($req->CustomerCountry) );	// US or other valid country code
		if ($country == "USA") $country = "US";
		
		
		// Add request-specific fields to the request string.
		$nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
					"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
					"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID".
					"&DESC=$orderDesc&INVNUM=$invoiceNum&EMAIL=$email&SOFTDESCRIPTOR=$softdescriptor";
		
		// make the post - use a try/catch in case of network errors
		try
		{
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
				// for error descriptions, refrer to https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_errorcodes
				$resp->IsSuccess = false;
				$resp->ResponseCode = urldecode( $this->GetArrayVal($httpParsedResponseAr,"L_ERRORCODE0") );
				
				// this variable contains an error code from the credit processor if one was given
				$processor_code = $this->GetArrayVal($httpParsedResponseAr,"L_ERRORPARAMVALUE0");
				
				if ($processor_code) 
				{
					// we have an error from the credit processor, so we should show that one
					// because it will indicate the root cause of the problem
					$errmsg = $this->GetArrayVal($httpParsedResponseAr,"L_ERRORPARAMID0") . ": " . $this->getProcessorResponseDescription($processor_code);
				}
				else
				{
					// there was no processor messages which means the error was caught first at the gateway
					// so we should show whatever that message is
					$longmessage = $this->GetArrayVal($httpParsedResponseAr,"L_LONGMESSAGE0");
					// paypal prepends this to every message, so strip it out if necessary
					if ($longmessage != "This%20transaction%20cannot%20be%20processed%2e") $longmessage = str_replace("This%20transaction%20cannot%20be%20processed%2e","", $longmessage );
					
					$errmsg = $this->GetArrayVal($httpParsedResponseAr,"L_SHORTMESSAGE0") . ": " . $longmessage;
					
				}
	
				$resp->ResponseMessage = urldecode( $errmsg );
				
			}
			
			$resp->RawResponse = "";
			$delim = "";
			foreach (array_keys($httpParsedResponseAr) as $key)
			{
				$resp->RawResponse .= $delim . $key . "='" . urldecode( $httpParsedResponseAr[$key]) . "'";
				$delim = ", ";
			}

		}
		catch (Exception $ex)
		{
			// this means we had a connection error talking to the gateway
			$resp->IsSuccess = false;
			$resp->ResponseCode = $ex->getCode();
			$resp->ResponseMessage = $ex->getMessage();
		}
		

		
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
		
		//$version = urlencode('51.0');
		$version = urlencode('62.0');
		
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
	
	/**
	 * Returns a possible description based on the code given.  These
	 * codes are returned by the PayPal processor.  If the code is not
	 * found, this function just returns "Error $code"
	 *
	 * @param string $code
	 * @return string possible description of error
	 */
	private function getProcessorResponseDescription($code)
	{
		$responses = Array();
      	$responses['0005'] = "The transaction was declined without explanation by the card issuer.";
		$responses['0013'] = "The transaction amount is greater than the maximum the issuer allows.";
		$responses['0014'] = "The issuer indicates that this card is not valid.";
		$responses['0051'] = "The credit limit for this account has been exceeded.";
		$responses['0054'] = "The card is expired.";
		$responses['1015'] = "The credit card number was invalid.";
		$responses['1511'] = "Duplicate transaction attempt.";
		$responses['1899'] = "Timeout waiting for host response.";
		$responses['2075'] = "Approval from the card issuer's voice center is required to process this transaction.";
		
		return array_key_exists($code,$responses) ? $responses[$code] : "Error " . $code;
		
	}
	
}

?>