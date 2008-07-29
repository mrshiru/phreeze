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
	
	function SetSender($email)
	{
		$this->Sender = $email;
	}
	
    function SetFrom($email,$name="")
    {
        $this->From = new Recipient($email,$name);
    }
    
    function AddRecipient($email,$name="")
    {
        $this->Recipients[] = new Recipient($email,$name);
    }
    
    function AddAttachment($path)
    {
        $this->Attachments[] = $path;
    }
    
}

?>