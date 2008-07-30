<?php
/** @package    verysimple::Email */

/** import supporting libraries */
require_once("Recipient.php");

define("MESSAGE_FORMAT_TEXT",0);
define("MESSAGE_FORMAT_HTML",1);

/**
 * Generic interface for sending Email
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class Message
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
    
    function Message()
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
	 */
    function AddRecipient($email,$name="")
    {
		$email = str_replace(",",";",$email);
		$emails = explode(";",$email);

		$name = str_replace(",",";",$name);
		$names = explode(";",$name);
		
		for ($i = 0; $i < count($emails); $i++)
		{
			$addr = trim($emails[$i]);
			$realname = isset($names[$i]) ? $names[$i] : $addr;
			$this->Recipients[] = new Recipient( $addr ,$realname);
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