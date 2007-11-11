<?php
/** @package    verysimple::Authentication */

/**
 * Passphrase is a utility class containing static functions for generating passwords
 * @package    verysimple::Authentication
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class PassPhrase
{

/**
	** GetRandomPassPhrase returns a prounoucable password of the given length.
	** @public
	** @param int $$pass_length
	** @return string $password.
	**/
	static function GetRandomPassPhrase($length)
	{
		
		srand((double)microtime()*1000000);
		
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
			"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		
		$num_vowels = count($vowels);
		$num_cons = count($cons);
		$password = "";
		
		for($i = 0; $i < $length; $i++)
		{
			$password .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
		}
		
		return substr($password, 0, $length);
	}
}

	
?>