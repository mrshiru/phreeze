<?php
/** @package    verysimple::String */

/**
 * A set of utility functions for working with strings
 *
 * @package    verysimple::String
 * @author Jason Hinkle
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class VerySimpleStringUtil
{
	/** @var the character set used when converting non ascii characters */
	static $DEFAULT_CHARACTER_SET = 'UTF-8';
	
	/** @var list of fancy/smart quote characters */
	static $SMART_QUOTE_CHARS;
	
	/** @var list of xml reserved characters */
	static $XML_SPECIAL_CHARS;
	
	/** @var replacements for xml reserved characters */
	static $XML_REPLACEMENT_CHARS;
	
	/** @var associative array containing the html translation for special characters with their numeric equivilant */
	static $HTML_ENTITIES_TABLE;
	
	/** @var common characters, especially on windows systems, that are technical not valid */
	static $INVALID_CODE_CHARS;

	/** @var characters used as control characters such as escape, backspace, etc */
	static $CONTROL_CODE_CHARS;
	
	/**
	 * VerySimpleStringUtil::InitStaticVars(); is called at the bottom of this file
	 */
	static function InitStaticVars()
	{
		
		self::$HTML_ENTITIES_TABLE = array();
		foreach (get_html_translation_table(HTML_ENTITIES, ENT_QUOTES) as $char => $entity)
		{
		    self::$HTML_ENTITIES_TABLE[$entity] = '&#' . ord($char) . ';';
		}
		
		self::$SMART_QUOTE_CHARS = 
			array(
				chr(226) . chr(128) . chr(156) => "\"",
				chr(226) . chr(128) . chr(157) => "\"",
				chr(226) . chr(128) . chr(153) => "'",
				chr(226) . chr(128) . chr(152) => "'",
				chr(167) => " ",
				chr(212) => "'",
				chr(213) => "'",
				chr(149) => "'",
				chr(145) => "'",
				chr(146) => "'",
				chr(147) => "\"",
				chr(148) => "\"",
				chr(210) => "\"",
				chr(211) => "\"",
				chr(151) => "-"
			);
			
		// 195 was something that appeared in a lot of fancy quotes, but causes problems
		// chr(195) => "'",
			
		self::$CONTROL_CODE_CHARS = 
			array(
				chr(0) => "&#0;",
				chr(1) => "&#1;",
				chr(2) => "&#2;",
				chr(3) => "&#3;",
				chr(4) => "&#4;",
				chr(5) => "&#5;",
				chr(6) => "&#6;",
				chr(7) => "&#7;",
				chr(8) => "&#8;",
				chr(14) => "&#14;",
				chr(15) => "&#15;",
				chr(16) => "&#16;",
				chr(17) => "&#17;",
				chr(18) => "&#18;",
				chr(19) => "&#19;",
				chr(20) => "&#20;",
				chr(21) => "&#21;",
				chr(22) => "&#22;",
				chr(23) => "&#23;",
				chr(24) => "&#24;",
				chr(25) => "&#25;",
				chr(26) => "&#26;",
				chr(27) => "&#27;",
				chr(28) => "&#28;",
				chr(29) => "&#29;",
				chr(30) => "&#30;",
				chr(31) => "&#31;"
			);
			
		self::$INVALID_CODE_CHARS = array(
			chr(128) => '&#8364;',
			chr(130) => '&#8218;',
			chr(131) => '&#402;',
			chr(132) => '&#8222;',
			chr(133) => '&#8230;',
			chr(134) => '&#8224;',
			chr(135) => '&#8225;',
			chr(136) => '&#710;',
			chr(137) => '&#8240;',
			chr(138) => '&#352;',
			chr(139) => '&#8249;',
			chr(140) => '&#338;',
			chr(142) => '&#381;',
			chr(145) => '&#8216;',
			chr(146) => '&#8217;',
			chr(147) => '&#8220;',
			chr(148) => '&#8221;',
			chr(149) => '&#8226;',
			chr(150) => '&#8211;',
			chr(151) => '&#8212;',
			chr(152) => '&#732;',
			chr(153) => '&#8482;',
			chr(154) => '&#353;',
			chr(155) => '&#8250;',
			chr(156) => '&#339;',
			chr(158) => '&#382;',
			chr(159) => '&#376;');

		self::$XML_SPECIAL_CHARS = array("&","<",">","\"","'");
		
		self::$XML_REPLACEMENT_CHARS = array("&amp;","&lt;","&gt;","&quot;","&apos;");
	}
			
	/**
	 * Takes the given text and converts any email address into mailto links,
	 * returning HTML content.
	 * 
	 * @param string $text
	 * @param bool true to sanitize the text before parsing for display security
	 * @return string HTML
	 */
	static function ConvertEmailToMailTo($text,$sanitize = false)
	{
		if ($sanitize) $text = VerySimpleStringUtil::Sanitize($text);
		$regex = "/([a-z0-9_\-\.]+)". "@" . "([a-z0-9-]{1,64})" . "\." . "([a-z]{2,10})/i"; 
		return preg_replace($regex, '<a href="mailto:\\1@\\2.\\3">\\1@\\2.\\3</a>', $text);
	}
	
	/**
	 * Takes the given text and converts any URLs into links,
	 * returning HTML content.
	 * 
	 * @param string $text
	 * @param bool true to sanitize the text before parsing for display security
	 * @return string HTML
	 */
	static function ConvertUrlToLink($text,$sanitize = false)
	{
		if ($sanitize) $text = VerySimpleStringUtil::Sanitize($text);
		$regex = "/[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]/i"; 
		return preg_replace($regex, '<a href=\"\\0\">\\0</a>', $text);
	}
	
	/**
	 * Sanitize any text so that it can be safely displayed as HTML without
	 * allowing XSS or other injection attacks
	 * @param string $text
	 * @return string 
	 */
	static function Sanitize($text)
	{
		return htmlspecialchars($text);
	}
	
	/**
	 * HTML encode all non-ascii characters (above char code 127)
	 * and optionall all control characters (backspace, escape, etc)
	 * 
	 * Note that this is not the same as html encoding, the
	 * characters such as <>&"' are not encoded.
	 * @param string $string
	 * @param bool $numericEncodingOnly true = return only numeric encoding (ie $oslash; gets converted to &#248;) (default true)
	 * @param bool $encodeInvalidCharacters false = wipe illegal ascii chars. (default true)
	 * * @param bool $encodeControlCharacters false = wipe control chars.  true = encode control characters (default false)
	 * @return string
	 */
	static function EncodeNonAscii($string, $numericEncodingOnly = true, $encodeInvalidCharacters = true, $encodeControlCharacters = false)
	{

		if (strlen($string) == 0) return "";
		
		$val = $string;
		
		// note - these encodings happen in a specific order to prevent double-encoding
		
		if ($encodeInvalidCharacters) $val = self::ReplaceInvalidCodeChars($val);

		if ($encodeControlCharacters) $val = self::ReplaceControlCodeChars($val); 

		// this will get all non-ascii characters, but will not encode &"'<>
		
		
		// mb_detect_order("UTF-8, ASCII, JIS, EUC-JP, SJIS");  // this apparently does nothing
		
		// first attempt to encode using auto-detect
		$encoded = mb_convert_encoding($val, 'HTML-ENTITIES');
		
		// @TODO @HACK existance of &AElig; char(198) is an indicator that auto-detect failed
		if (strpos($encoded,"&AElig;") !== false)
		{
			// $encoded = mb_detect_encoding($val);
			$encoded = mb_convert_encoding($val, 'HTML-ENTITIES', self::$DEFAULT_CHARACTER_SET);
		}
		
		// finally, if only numeric encodings are required
		if ($numericEncodingOnly) $encoded = self::ReplaceNonNumericEntities($encoded);
		
		return $encoded;
	}
	
	/**
	 * @TODO: is there any hope of making this work right?
	 * Decode string that has been encoded using EncodeNonAscii
	 * @param string $string
	 * @param destination character set (default = $DEFAULT_CHARACTER_SET)
	 */
	static function DecodeNonAscii($string, $charset = null)
	{
		// this only gets named characters
		// return html_entity_decode($string);
		
		// this seems to work but doesn't get the encodings right
		// $name = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $name);
    	// $name = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $name);
    	// $name = html_entity_decode($name);
    		
		// this way at least somebody could specify a character set.  UTF will work some of the time
		if ($charset == null) $charset = VerySimpleStringUtil::$DEFAULT_CHARACTER_SET;
		return mb_convert_encoding($string, $charset, 'HTML-ENTITIES');
	}
	
	/**
	 * Converts a string into a character array
	 * @param string $string
	 * @return array
	 */
	static function GetCharArray($string)
	{
		return preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY);
	}

	/**
	 * This HTML encodes special characters and returns an ascii safe version.
	 * This function extends EncodeNonAscii to additionally strip
	 * out characters that may be disruptive when used in HTML or XML data
	 *
	 * @param string value to parse
	 * @param bool $escapeQuotes true to additionally escape ENT_QUOTE characters <>&"' (default = true)
	 * @param bool $numericEncodingOnly set to true to only use numeric html encoding (default false)
	 * @param bool $replaceSmartQuotes true to replace "smart quotes" with standard ascii ones, can be useful for stripping out windows-only codes (default = false)
	 * @return string
	 */
	static function EncodeSpecialCharacters($string, $escapeQuotes = true, $numericEncodingOnly = false, $replaceSmartQuotes = false)
	{
		if (strlen($string) == 0) return "";
		$val = $string;
		
		// do this first before encoding
		if ($replaceSmartQuotes) $val = self::ReplaceSmartQuotes($val);

		// this method does not double-encode, but replaces single-quote with a numeric entity
		if ($escapeQuotes) $val = htmlspecialchars($val, ENT_QUOTES, null, false);
		
		// this method double-encodes values but uses the special character entity for single quotes
		// if ($escapeQuotes) $val = str_replace(self::$XML_SPECIAL_CHARS, self::$XML_REPLACEMENT_CHARS, $val);
		
		// for special chars we don't need to insist on numeric encoding only
		return self::EncodeNonAscii($val,$numericEncodingOnly);

	}
	
	/**
	 * This replaces smart (fancy) quote characters with generic ascii versions
	 * @param string $string
	 * @return string
	 */
	static function ReplaceSmartQuotes($string)
	{
		return strtr($string,self::$SMART_QUOTE_CHARS);
	}

	/**
	 * This replaces control characters characters with generic ascii versions
	 * @param string $string
	 * @return string
	 */
	static function ReplaceControlCodeChars($string)
	{
		return strtr($string,self::$CONTROL_CODE_CHARS);
	}

	/**
	 * This replaces all non-numeric html entities with the numeric equivilant
	 * @param string $string
	 * @return string
	 */
	static function ReplaceNonNumericEntities($string)
	{
		return strtr($string,self::$HTML_ENTITIES_TABLE);
	}
	
	/**
	 * This replaces illegal ascii code values $INVALID_CODE_CHARS
	 * @param string $string
	 * @return string
	 */
	static function ReplaceInvalidCodeChars($string)
	{
		return strtr($string,self::$INVALID_CODE_CHARS);
		
		// less efficient way of handling the replacements...
//		$chararray =  self::GetCharArray($string);
//		$illegal = array_keys(self::$INVALID_CODE_CHARS);
//	
//		for ( $i = 0; $i < count($chararray); $i++ ) 
//		{ 
//			$ord = ord($chararray[$i]); 
//			if ( in_array($ord,$illegal) ) $chararray[$i] = self::$INVALID_CODE_CHARS[$ord];
//		}
//		
//		return implode('',$chararray); 
	}
}

// this will be executed only once
VerySimpleStringUtil::InitStaticVars();


?>