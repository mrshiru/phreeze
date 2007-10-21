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
 * @version    1.0
 */
class Message
{
    var $Recipients;
    var $CCRecipients;
    var $BCCRecipients;
    var $From;
    var $Subject;
    var $Body;
    var $Format;
    var $Attachments;
    
    function Message()
    {
        $this->Recipients = Array();
        $this->CCRecipients = Array();
        $this->BCCRecipients = Array();
        $this->Attachments = Array();
        $this->Format = MESSAGE_FORMAT_TEXT;
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