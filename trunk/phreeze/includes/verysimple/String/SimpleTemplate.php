<?php
/** @package	verysimple::String */

/**
 * A set of utility functions for working with simple template files
 *
 * @package	verysimple::String
 * @author Jason Hinkle
 * @copyright  1997-2011 VerySimple, Inc.
 * @license	http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class SimpleTemplate
{
	
	/**
	 * Transforms HTML into formatted plain text.
	 * 
	 * @param string HTML
	 * @return string plain text
	 */
	static function HtmlToText($html)
	{
		require_once("class.html2text.php");
		$h2t = new html2text($html);
		return $h2t->get_text(); 
	}
	
	
	/**
	 * Transforms plain text into formatted HTML.
	 * 
	 * @param string plain text
	 * @return string HTML
	 */
	static function TextToHtml($txt) 
	{
		//Kills double spaces and spaces inside tags.
		while( !( strpos($txt,'  ') === FALSE ) ) $txt = str_replace('  ',' ',$txt);
		$txt = str_replace(' >','>',$txt);
		$txt = str_replace('< ','<',$txt);
	
		//Transforms accents in html entities.
		$txt = htmlentities($txt);
	
		//We need some HTML entities back!
		$txt = str_replace('&quot;','"',$txt);
		$txt = str_replace('&lt;','<',$txt);
		$txt = str_replace('&gt;','>',$txt);
		$txt = str_replace('&amp;','&',$txt);
	
		//Ajdusts links - anything starting with HTTP opens in a new window
		//$txt = str_ireplace("<a href=\"http://","<a target=\"_blank\" href=\"http://",$txt);
		//$txt = str_ireplace("<a href=http://","<a target=\"_blank\" href=http://",$txt);

		
		//Basic formatting
		$eol = ( strpos($txt,"\r") === FALSE ) ? "\n" : "\r\n";
		$html = '<p>'.str_replace("$eol$eol","</p><p>",$txt).'</p>';
		$html = str_replace("$eol","<br />\n",$html);
		$html = str_replace("</p>","</p>\n\n",$html);
		$html = str_replace("<p></p>","<p>&nbsp;</p>",$html);
	
		//Wipes <br> after block tags (for when the user includes some html in the text).
		$wipebr = Array("table","tr","td","blockquote","ul","ol","li");
	
		for($x = 0; $x < count($wipebr); $x++) 
		{
			$tag = $wipebr[$x];
			$html = str_ireplace("<$tag><br />","<$tag>",$html);
			$html = str_ireplace("</$tag><br />","</$tag>",$html);
		}
	
		return $html;
	}
	
	
	/**
	 * Merges data into a template with placeholder variables 
	 * (for example "Hello {{NAME}}").  Useful for simple templating
	 * needs such as email, form letters, etc.
	 * 
	 * Note that there is no escape character so ensure the right and
	 * left delimiters do not appear as normal text within the template
	 * 
	 * @param string $template string with placeholder variables
	 * @param array $vars an array of key/value pairs used to populate the placeholder variables
	 * @param string the left (opening) delimiter for placeholders. default = {{
	 * @param string the right (closing) delimiter for placeholders. default = }}
	 * @return string merged template
	 */
	static function Merge($template,$vars, $ldelim = "{{", $rdelim = "}}")
	{
 		$ob_size = strlen($ldelim);
		$cb_size = strlen($rdelim);
	   
		$pos = 0;
		$end = strlen($template);
	   
		while($pos <= $end)
		{
			if($pos_1 = strpos($template, $ldelim, $pos))
			{
				if($pos_1)
				{
					$pos_2 = strpos($template, $rdelim, $pos_1);
				   
					if($pos_2)
					{
						$return_length = ($pos_2-$cb_size) - $pos_1;
					   
						$var = substr($template, $pos_1+$ob_size, $return_length);
						
						// if the source value isn't in the array, just blank it out
						if (empty($source[$var])) $source[$var] = "";

						$template = str_replace($ldelim.$var.$rdelim, $source[$var], $template);
	
					   
						$pos = $pos_2 + $cb_size;
					}
					else
					{
						throw new Exception("Closing delimiter is missing");
						break;
					}
				}
			}
			else
			{
				//exit the loop
				break;
			}
		}
	   
		return $template; 
	}
	
}

?>