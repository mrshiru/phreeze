<?php
/** @package    verysimple::XML */

require_once("ParseException.php");

/**
 * XMLUtil provides a collection of static methods that are useful when
 * dealing with XML
 *
 * @package    verysimple::XML
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    3.1
 */
class XMLUtil
{
	static $reserved = Array("\"","'","&","<",">");
	static $replacements = Array("&quot;","&apos;","&amp;","&lt;","&gt;");
	
	/**
	 * Parses the given XML using SimpleXMLElement, however traps PHP errors and
	 * warnings and converts them to an Exception that you can catch.  Surround
	 * this statement with try/catch and you can handle parsing exceptions instead
	 * of allowing PHP to terminate or write errors to the browser
	 *
	 * @param $xml string
	 */
	static function SafeParse($xml)
	{
		// re-route error handling temporarily so we can convert PHP errors to an exception
		set_error_handler(array("XMLUtil", "HandleParseException"),E_ALL);
		
		$element = new SimpleXMLElement($xml);
		
		// reset error handling back to whatever it was
		restore_error_handler();
		
		return $element;
	}
	
	/**
	* Escapes special characters that will corrupt XML
	* @param String to be escaped
	*/
	static function Escape($str)
	{
		return str_replace(XmlUtil::$reserved,XmlUtil::$replacements,$str);
	}
	
	/**
	 * UnEscapes special characters from XML that were Escaped
	 * @param String to be unescaped
	 */
	static function UnEscape($str)
	{
		return str_replace(XmlUtil::$replacements,XmlUtil::$reserved,$str);
	}
	
	/**
	* Recurses an associative array and returns an XML string
	* @param $arr Array to recurse
	*/
	static function ArrayToXML($arr)
	{
		$xml = "";
		foreach (array_keys($arr) as $key)
		{
			if (is_array($arr[$key]))
			{
				$xml .= "<".$key.">";
				$xml .= XmlUtil::ArrayToXML($arr[$key]);
				$xml .= "</".$key.">\n";
			}
			elseif (is_object($arr[$key]))
			{
				$props = get_object_vars($arr[$key]);
				$xml .= "<".$key.">";
				$xml .= XmlUtil::ArrayToXML( $props );
				$xml .= "</".$key.">\n";
			}
			else
			{
				$xml .= "<".$key.">" . XmlUtil::Escape($arr[$key]) . "</".$key.">\n";
			}
		}
		
		return $xml;
	}
	
	/**
	 * For a node that is known to have inner text and no other child nodes, 
	 * this returns the inner text and advances the reader curser to the next
	 * element.  If there is no text, the curser is not advanced
	 * @param XMLReader
	 * @param string $default (optional) if the value is blank
	 * @return string
	 */
	static function GetInnerText(XMLReader $xml, $default = "")
	{
		if ($xml->isEmptyElement == false && $xml->read() && $xml->nodeType == XMLReader::TEXT)
		{
			return $xml->value;
		}
		else
		{
			return $default;
		}
	}
	
	/**
	 * Given an XMLReader, returns an object that can be inspected using print_r
	 * @param XMLReader
	 * @return object
	 */
	static function ConvertReader(XMLReader $xml) 
	{
		
		$node_types = array (
			0=>"XMLReader::NONE",
			1=>"XMLReader::ELEMENT",
			2=>"XMLReader::ATTRIBUTE",
			3=>"XMLReader::TEXT",
			4=>"XMLReader::CDATA",
			5=>"XMLReader::ENTITY_REF",
			6=>"XMLReader::ENTITY",
			7=>"XMLReader::PI",
			8=>"XMLReader::COMMENT",
			9=>"XMLReader::DOC",
			10=>"XMLReader::DOC_TYPE",
			11=>"XMLReader::DOC_FRAGMENT",
			12=>"XMLReader::NOTATION",
			13=>"XMLReader::WHITESPACE",
			14=>"XMLReader::SIGNIFICANT_WHITESPACE",
			15=>"XMLReader::END_ELEMENT",
			16=>"XMLReader::END_ENTITY",
			17=>"XMLReader::XML_DECLARATION"
		);

		$obj;
		$obj->attributeCount =  $xml->attributeCount ;
		$obj->baseURI = $xml->baseURI;
		$obj->depth = $xml->depth;
		$obj->hasAttributes = ( $xml->hasAttributes ? 'TRUE' : 'FALSE' );
		$obj->hasValue = ( $xml->hasValue ? 'TRUE' : 'FALSE' );
		$obj->isDefault = ( $xml->isDefault ? 'TRUE' : 'FALSE' );
		$obj->isEmptyElement = ( @$xml->isEmptyElement ? 'TRUE' : 'FALSE' );
		$obj->localName = $xml->localName;
		$obj->name = $xml->name;
		$obj->namespaceURI = $xml->namespaceURI;
		$obj->nodeType = $xml->nodeType;
		$obj->nodeTypeDescription = $node_types[$xml->nodeType];
		$obj->prefix = $xml->prefix;
		$obj->value = $xml->value;
		$obj->xmlLang = $xml->xmlLang;
		
		return $obj;
	}
	
	/**
	* Handler for catching ParseException errors
	*/
	public static function HandleParseException($code, $string, $file, $line, $context)
	{
		throw new ParseException($string,$code);
	}
	
}

?>