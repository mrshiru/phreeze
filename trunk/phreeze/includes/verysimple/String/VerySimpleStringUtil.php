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
	
}

?>