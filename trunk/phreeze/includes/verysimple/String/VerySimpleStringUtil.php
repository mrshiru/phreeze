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

	/** populated with InitStaticVars */
	static $SPECIAL_CHARS;
	static $REPLACEMENT_CHARS;
	static $XML_SPECIAL_CHARS;
	static $XML_REPLACEMENT_CHARS;
	
	/**
	 * VerySimpleStringUtil::InitStaticVars(); is called at the bottom of this file
	 */
	static function InitStaticVars()
	{
		self::$SPECIAL_CHARS = array(
					chr(226) . chr(128) . chr(156),
					chr(226) . chr(128) . chr(157),
					chr(226) . chr(128) . chr(153),
					chr(167),
					chr(195),
					chr(212),
					chr(213),
					chr(149),
					chr(145),
					chr(146),
					chr(147),
					chr(148),
					chr(210),
					chr(211),
					chr(151)
		);
					
		self::$REPLACEMENT_CHARS = array(
					"\"",
					"\"",
					"'",
					" ",
					"",
					"'",
					"'",
					"'",
					"'",
					'"',
					'"',
					'"',
					'"',
					'"',
					'-'
		);

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
	 * This replaces fancy quotes and encodes non-ascii characters so that they
	 * can be displayed and stored in places that do not support non-ascii characters
	 *
	 * @param string string to parse
	 * @param bool true to htmlencode non characters above ascii 127, otherwise they will be replaced with a ~
	 * @param bool true to additionally escape XML special characters <>&"'
	 * @return string
	 *
	 */
	static function StripSpecialCharacters($string, $htmlencode_non_western = true, $escape_xml = false)
	{
		$original = $string;
		
		// check for empty string
		if (strlen($string) == 0) return "";
		
		// replace fancy quotes with regular quotes
		$string = str_replace(self::$SPECIAL_CHARS,self::$REPLACEMENT_CHARS,$string);
				
		// loop through every character and strip out any that are not in the normal ascii range
		$chararray = preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY); 
		for ( $i = 0; $i < count($chararray); $i++ ) 
		{ 
			$ord = ord($chararray[$i]); 
			if ( $ord < 9 || ($ord > 13 && $ord < 32) ) $chararray[$i] = ''; // wipe out these control chars
			
			// TODO: for now simply don't allow non-western chars
			if ( $ord > 127 ) $chararray[$i] = $htmlencode_non_western ? "~" : '&#' . $ord . ';';
		} 
		
		$string = implode('',$chararray); 
		
		if ($escape_xml)
		{
			$string = str_replace(self::$XML_SPECIAL_CHARS,self::$XML_REPLACEMENT_CHARS,$string);
		}
		else
		{
			// this would escape all html characters
			// $string = htmlentities( $string, ENT_NOQUOTES, "UTF-8" );
		}
		
		// if ($original != $string) print "<p>ORIGINAL = ". $original . " NEW = " . $string."</p>";
		
		return $string;
	}
}

// this will be executed only once
VerySimpleStringUtil::InitStaticVars();


?>