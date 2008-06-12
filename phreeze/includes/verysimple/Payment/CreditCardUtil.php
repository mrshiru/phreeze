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
}

?>