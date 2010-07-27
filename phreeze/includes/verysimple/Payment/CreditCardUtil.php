<?php

/** @package    verysimple::Payment */

/**
 * CreditCardUtil is a standard class used to work in general
 * with credit card processing, including clean output.
 *
 * @package    verysimple::Payment
 * @author     VerySimple Inc.
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */

class CreditCardUtil
{
	/** 
	 * Formats the given credit card number with dashes.
	 * 
	 * @param string credit card number
	 * @return string
	 */
	static function FormatWithDashes($cc_num)
	{
		$dashSeparatedNumber = "";

		if(strlen($cc_num) == 15)
		{
			$firstFour = substr($cc_num,0,4);
			$secondSix = substr($cc_num,4,6);
			$thirdFive = substr($cc_num,10,5);
			$dashSeparatedNumber = $firstFour . "-" . $secondSix . "-" . $thirdFive;
		}
		else
		{
			$firstFour = substr($cc_num,0,4);
			$secondFour = substr($cc_num,4,4);
			$thirdFour = substr($cc_num,8,4);
			$fourthFour = substr($cc_num,12,4);
			$dashSeparatedNumber = $firstFour . "-" . $secondFour . "-" . $thirdFour . "-" . $fourthFour;					
		}
		
		return $dashSeparatedNumber;
	}
	
	/**
	 * Returns a number with all non-numeric characters removed
	 * @param unknown_type $num
	 */
	static function StripNonNumeric($num)
	{
		return preg_replace('{\D}', '', $num);
	}
	
	/**
	 * Returns true if the card meets a valid mod10 (Luhn Algorithm) check
	 * @param bool
	 */
	static function IsValidMod10($str)
	{
	   if (strspn($str, "0123456789") != strlen($str)) 
	   {
	      return false;
	   }
	   
	   $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, // for even indices
	                0, 2, 4, 6, 8, 1, 3, 5, 7, 9); // for odd indices
	   $sum = 0;
	   $last = strlen($str) - 1;
	   
	   for ($i = 0; $i <= $last; $i++) 
	   {
	      $sum += $map[$str[$last - $i] + ($i & 1) * 10];
	   }
	   
	   return $sum % 10 == 0;
	}
	
	/**
	 * Returns the Credit Card type based on the card number using the info
	 * at http://en.wikipedia.org/wiki/Credit_card_numbers as a reference
	 * @return string
	 */
	static function GetType($num)
	{
		if (strlen($num) < 4) return "";
		if (substr($num,0,1) == 4) return "VISA";
		if (substr($num,0,2) == 34 || substr($num,0,2) == 37) return "AMEX";
		if (substr($num,0,2) >= 51 && substr($num,0,2) <= 55) return "MASTERCARD";
		return "UNKNOWN";
	}
	
}

?>