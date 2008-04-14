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
	// replacement variable for inner text and for attribute values
	static $reservedAttrib = Array("\"","'","&","<",">");
	static $replacementsAttrib = Array("&quot;","&apos;","&amp;","&lt;","&gt;");
	static $reservedText = Array("&","<",">");
	static $replacementsText = Array("&amp;","&lt;","&gt;");
	
	/**
	 * Parses the given XML using SimpleXMLElement, however traps PHP errors and
	 * warnings and converts them to an Exception that you can catch.  Surround
	 * this statement with try/catch and you can handle parsing exceptions instead
	 * of allowing PHP to terminate or write errors to the browser
	 *
	 * @param $xml string
	 * @return SimpleXMLElement
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
	* @param bool (default false) true if you are escaping an attribute (ie <field attribute="" />)
	*/
	static function Escape($str, $escapeQuotes = false)
	{
		if ($escapeQuotes)
		{
			return str_replace(XmlUtil::$reservedAttrib,XmlUtil::$replacementsAttrib,$str);
		}
		else
		{
			return str_replace(XmlUtil::$reservedText,XmlUtil::$replacementsText,$str);
		}
	}
	
	/**
	 * UnEscapes special characters from XML that were Escaped
	 * @param string to be unescaped
	 * @return string
	 */
	static function UnEscape($str)
	{
		return str_replace(XmlUtil::$replacements,XmlUtil::$reserved,$str);
	}

	/**
	 * converts a string containing xml into an array
	 * @param string to be unescaped
	 * @return array
	 */
	static function ToArray($xmlstring)
	{
		$xml = XMLUtil::SafeParse($xmlstring);
		$array = array();
		
        foreach ($xml as $key => $val)
        {
			$array[strval($key)]=strval($val);
        }

		return $array;

    }
	
	
	/**
	* Recurses value and serializes it as an XML string
	* @param variant $var object, array or value to convert
	* @param string $root name of the root node (optional)
	* @return string XML
	*/
	static function ToXML($var, $root="")
	{
		$xml = "";
		
		if (is_object($var))
		{
			// object have properties that we recurse
			$name = strlen($root) > 0 && is_numeric($root) == false ? $root : get_class($var);
			$xml .= "<".$name.">\n";
			
			$props = get_object_vars($var);
			foreach (array_keys($props) as $key)
			{
				$xml .= XmlUtil::ToXML( $props[$key], $key );
			}
			
			$xml .= "</".$name.">\n";
		}
		elseif (is_array($var))
		{
			$name = strlen($root) > 0 ? (is_numeric($root) ? "Array_".$root : $root) : "Array";
			$xml .= "<".$name.">\n";
			
			foreach (array_keys($var) as $key)
			{
				$xml .= XmlUtil::ToXML( $var[$key], $key );
			}
			
			$xml .= "</".$name.">\n";
		}
		else
		{
			$name = strlen($root) > 0 ? (is_numeric($root) ? "Value_".$root : $root) : "Value";
			$xml .= "<".$name.">" . XmlUtil::Escape($var) . "</".$name.">\n";
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