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
	static $SPECIAL_CHARS;
	
	/** @var list of xml reserved characters */
	static $XML_SPECIAL_CHARS;
	
	/** @var replacements for xml reserved characters */
	static $XML_REPLACEMENT_CHARS;
	
	/** @var associative array containing the html translation for special characters with their numeric equivilant */
	static $HTML_ENTITIES_TABLE;
	
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
		
		self::$SPECIAL_CHARS = 
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
			
			// chr(195) => "'",

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
	 * @param bool $encodeControlCharacters false = wipe control chars.  true = encode control characters (default false)
	 * @return string
	 */
	static function EncodeNonAscii($string, $numericEncodingOnly = true, $encodeControlCharacters = false)
	{
		if (strlen($string) == 0) return "";
		
		$val = $string;
		
		if ($encodeControlCharacters)
		{
			$chararray =  self::GetCharArray($string);
		
			for ( $i = 0; $i < count($chararray); $i++ ) 
			{ 
				$ord = ord($chararray[$i]); 
				if ( $ord < 9 || ($ord > 13 && $ord < 32) ) $chararray[$i] = '&#'.$ord.';';
			}
			
			$val = implode('',$chararray); 
		}
		
		// this will get all non-ascii characters
		$val = mb_convert_encoding($val, 'HTML-ENTITIES', VerySimpleStringUtil::$DEFAULT_CHARACTER_SET);
		
		// replace the special character encodings if necessary
		if ($numericEncodingOnly) $val = str_replace(array_keys(self::$HTML_ENTITIES_TABLE), self::$HTML_ENTITIES_TABLE, $val);
		
		return $val;
	}
	
	/**
	 * Decode string that has been encoded using EncodeNonAscii
	 * @param string $string
	 * @param destination character set (default = $DEFAULT_CHARACTER_SET)
	 */
	static function DecodeNonAscii($string, $charset = null)
	{
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
	 * @param bool true to additionally escape ENT_QUOTE characters <>&"' (default = true)
	 * @param bool true to replace "smart quotes" with standard ascii ones, can be useful for stripping out windows-only codes (default = false)
	 * @return string
	 */
	static function EncodeSpecialCharacters($string, $escapeQuotes = true, $replaceSmartQuotes = false)
	{
		if (strlen($string) == 0) return "";
		$val = $string;
		
		// do this first before encoding
		if ($replaceSmartQuotes) $val = self::ReplaceSmartQuotes($val);

		// do this next because the simple replace method would otherwise double-encode
		if ($escapeQuotes) $val = str_replace(self::$XML_SPECIAL_CHARS, self::$XML_REPLACEMENT_CHARS, $val);
		// if ($escapeQuotes) $val = htmlspecialchars($val, ENT_QUOTES, null, false); // this replaces single quotes with a numeric entry
		
		// for special chars we don't need to insist on numeric encoding only
		return self::EncodeNonAscii($val,false);

	}
	
	/**
	 * This replaces smart (fancy) quote characters with generic ascii versions
	 * @param unknown_type $string
	 */
	static function ReplaceSmartQuotes($string)
	{
		return str_replace(array_keys(self::$SPECIAL_CHARS), array_values(self::$SPECIAL_CHARS), $string);
	}
}

// this will be executed only once
VerySimpleStringUtil::InitStaticVars();


?>