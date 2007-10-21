<?php
/** @package    verysimple::HTTP */

/**
 * Encapsulates a file upload.
 *
 * Utility class for dealing with file uploads and converting them into
 * a string that is easily insertable into a database
 *
 * @package    verysimple::HTTP 
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc. http://www.verysimple.com
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class FileUpload
{
	public $Name;
	public $Size;
	public $Type;
	public $Extention;
	public $Data;
	
	/**
	 * Returns a file upload as xml that is ready to save to a file or database
	 *
	 * @param	string	$fieldname
	 * @return	string
	 */
	public function ToXML($base64 = true)
	{
		return "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n"
			. "<file>\r\n"
			. "<name>".$this->Name."</name>\r\n"
			. "<size>".$this->Size."</size>\r\n"
			. "<type>".$this->Type."</type>\r\n"
			. "<extention>".$this->Extention."</extention>\r\n"
			. "<encoding>" . ($base64 ? "base64" : "none") . "</encoding>\r\n"
			. "<data>" . ($base64 ? base64_encode($this->Data) : $this->Data) . "</data>\r\n"
			. "</file>";
	}
	
	/**
	 * Loads this FileUpload object from previously obtained from ToXML()
	 *
	 * @param	string	$xml
	 */
	public function FromXML($xml)
	{
		$sxo = new SimpleXMLElement($xml);
		
		if($sxo->encoding == "base64")
		{
			$this->Data = base64_decode($sxo->data);
		}
		else
		{
			$this->Data = $sxo->data;
		}
		
		$this->Name = $attachment->name;
		$this->Type = $attachment->type;
		$this->Size = $attachment->size;
		$this->Extension = $attachment->extension;
	}
}
?>