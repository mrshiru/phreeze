<?php
/** @package    verysimple::Email */

/** import supporting libraries */
require_once("class.phpmailer.php");
require_once("Message.php");

define("MAILER_RESULT_FAIL",0);
define("MAILER_RESULT_OK",1);

define("MAILER_METHOD_SENDMAIL","SENDMAIL");
define("MAILER_METHOD_SMTP","SMTP");
define("MAILER_METHOD_MAIL","MAIL");

/**
 * Generic interface for sending Email
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class Mailer
{
    
    var $_errors;
    var $Method;
    var $Path;
    var $Host;
    
    function Mailer($method = MAILER_METHOD_SENDMAIL, $path = "/usr/sbin/sendmail")
    {
        $this->_log = Array();
        $this->_errors = Array();
        $this->Method = $method;
        $this->Path = $path;
        
    }
    
    function Send($message)
    {
        $mailer = new PHPMailer();
        
        $mailer->From = $message->From->Email;
        $mailer->FromName = $message->From->RealName;
        $mailer->Subject = $message->Subject;
        $mailer->Body = $mailer->FixEOL($message->Body);
        $mailer->ContentType = ($message->Format == MESSAGE_FORMAT_TEXT) ? "text/plain" : "text/html";
        $mailer->Mailer = strtolower($this->Method);
        $mailer->Host = $this->Path;
        $mailer->Sendmail = $this->Path;
        
        // add the recipients
        foreach ($message->Recipients as $recipient)
        {
            $this->_log[] = "Adding Recipient ".$recipient->RealName." [".$recipient->Email."]";
            $mailer->AddAddress($recipient->Email,$recipient->RealName);
        }
        
        $result = MAILER_RESULT_OK;
        
        $this->_log[] = "Sending message using " . $mailer->Mailer;
        
        $fail = !$mailer->Send();
        
        if ( $fail || $mailer->ErrorInfo)
        {
            $result = MAILER_RESULT_FAIL;
            $this->_errors[] = $mailer->ErrorInfo;
        }
        
        return $result;
    }
    
    function QuickSend($to,$from,$subject,$body)
    {
        $message = new Message();
        $message->SetFrom($from);
        $message->AddRecipient($to);
        $message->Subject = $subject;
        $message->Body = $body;
        
        return $this->Send($message);
    }   
    
    function GetErrors()
    {
        return $this->_errors;   
    }

    function GetLog()
    {
		return $this->_log;   
    }
    
    
}

?>