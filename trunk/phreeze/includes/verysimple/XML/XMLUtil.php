<?php
/** @package    verysimple::XML */

/**
 * XMLUtil provides a collection of static methods that are useful when
 * dealine with XML
 *
 * @package    verysimple::XML
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class XMLUtil
{

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
}

?>