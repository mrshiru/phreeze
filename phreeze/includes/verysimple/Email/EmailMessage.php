<?php
/** @package    verysimple::Email */

/** import supporting libraries */
require_once("Recipient.php");
require_once("verysimple/String/VerySimpleStringUtil.php");

define("MESSAGE_FORMAT_TEXT",0);
define("MESSAGE_FORMAT_HTML",1);

/**
 * Generic interface for sending Email
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.1
 */
class EmailMessage
{
    public $Recipients;
    public $CCRecipients;
    public $BCCRecipients;
    public $From;
    public $Subject;
    public $Body;
    public $Format;
    public $Attachments;
	public $Sender;
	
	static $RECIPIENT_TYPE_TO = "";
	static $RECIPIENT_TYPE_BCC = "BCC";
	static $RECIPIENT_TYPE_CC = "CC";
	
	/** set to true and AddREcipient will decode html entities in the email */
	static $DECODE_HTML_ENTITIES = true;
    
    function EmailMessage()
    {
        $this->Recipients = Array();
        $this->CCRecipients = Array();
        $this->BCCRecipients = Array();
        $this->Attachments = Array();
        $this->Format = MESSAGE_FORMAT_TEXT;
    }
	
	/**
	 * Set the sender of the message.  This will appear in some email clients
	 * as "on behalf of"
	 * @param string
	 */
	function SetSender($email)
	{
		$this->Sender = $email;
	}
	
	/**
	 * Set the from address of the message
	 * @param string
	 */
    function SetFrom($email,$name="")
    {
        $this->From = new Recipient($email,$name);
    }
    
	/**
	 * Adds a recipient to the email message
	 * @param $email a single email or semi-colon/comma delimited list
	 * @param $string a single email or semi-colon/comma delimited list
	 * @param $string $RECIPIENT_TYPE_TO, $RECIPIENT_TYPE_CC or $RECIPIENT_TYPE_BCC (default = $RECIPIENT_TYPE_TO)
	 */
    function AddRecipient($email, $name="", $recipientType = "")
    {
    	if (self::$DECODE_HTML_ENTITIES)
    	{

    		// utilize string util to decode to UTF-8, then decode that to ISO-8859-1
    		$email = utf8_decode ( VerySimpleStringUtil::DecodeFromHTML($email) );
    		$name = utf8_decode ( VerySimpleStringUtil::DecodeFromHTML($name) );

    	}
    	
    	// die('email is ' . $email . '\nname is ' . $name . ' = ' . chr(423));
    	
		$email = str_replace(",",";",$email);
		$emails = explode(";",$email);

		$name = str_replace(",",";",$name);
		$names = explode(";",$name);
		
		for ($i = 0; $i < count($emails); $i++)
		{
			$addr = trim($emails[$i]);
			$realname = isset($names[$i]) ? $names[$i] : $addr;
			
			if ($recipientType == self::$RECIPIENT_TYPE_CC)
			{
				$this->CCRecipients[] = new Recipient( $addr ,$realname);
			}
			elseif ($recipientType == self::$RECIPIENT_TYPE_BCC)
			{
				$this->BCCRecipients[] = new Recipient( $addr ,$realname);
			}
			else
			{
				$this->Recipients[] = new Recipient( $addr ,$realname);
			}
		}
    }
    
	/**
	 * Attach a message to the email.
	 * @param the full  path to the file to be attached
	 */
    function AddAttachment($path)
    {
        $this->Attachments[] = $path;
    }
    
}

?>