<?php
/** @package    verysimple::Email */

/**
 * Generic interface for connecting to a Pop3 Server
 * GetPart and GetMimeType based on code by Kevin Steffer 
 * <http://www.linuxscope.net/articles/mailAttachmentsPHP.html>
 * GetAttachments based on code by developers@steffer.dk
 * <http://www.php.net/manual/en/function.imap-bodystruct.php>
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    LGPL
 * @version    1.0
 */
class Pop3Client
{
	private $mbox;
	private $do_delete = false;
	
	/**
	*
	*/
	function Open($user, $pass, $host, $port = "110", $mbtype = "pop3",$mbfolder = "INBOX")
	{
		$this->mbox = imap_open(
			"{".$host."/".$mbtype.":".$port."}".$mbfolder."",
			$user,
			$pass);
	}
	
	function DeleteMessage($msgnum)
	{
		imap_delete($this->mbox,$msgnum);
		$this->do_delete = true;
	}
	
	/**
	*
	*/
	function Close($empty_trash = true)
	{
		if ($this->do_delete && $empty_trash) {imap_expunge($this->mbox);}
		
		imap_close($this->mbox);
	}
	
	function GetMessageCount()
	{
		$summary = $this->GetSummary();
		return $summary->Nmsgs;
	}
	
	/**
	*
	*/
	function GetSummary()
	{
		return imap_check($this->mbox);
	}
	
	/**
	*
	*/
	function GetQuickHeaders()
	{
		return imap_headers($this->mbox);
	}
	
	function GetHeader($msgno)
	{
		return imap_headerinfo($this->mbox, $msgno);
	}
	
	/**
	*
	*/
	function GetAttachments($msgno, $include_raw_data = true)
	{
		$struct = imap_fetchstructure($this->mbox,$msgno);
		$contentParts = count($struct->parts);
		$attachments = array();

		if ($contentParts >= 2) 
		{
			for ($i=2;$i<=$contentParts;$i++) 
			{
				$att[$i-2] = imap_bodystruct($this->mbox,$msgno,$i);
				// these extra bits help us later...
				$att[$i-2]->x_msg_id = $msgno;
				$att[$i-2]->x_part_id = $i;
			}
			
			for ($k=0;$k<sizeof($att);$k++) 
			{
				if ( strtolower($att[$k]->parameters[0]->value) == "us-ascii" && $att[$k]->parameters[1]->value != "") 
				{
					$attachments[$k] = $this->_getPartFromStruct($att[$k],$include_raw_data);
				} 
				elseif ( strtolower($att[$k]->parameters[0]->value) != "iso-8859-1" ) 
				{
					$attachments[$k] = $this->_getPartFromStruct($att[$k],$include_raw_data);
				}
			}
		}
		
		return $attachments;
	}
	
	private function _getPartFromStruct($struct, $include_raw_data)
	{
		//print_r($struct);
		$part = null;
		$part->msgnum = $struct->x_msg_id;
		$part->partnum = $struct->x_part_id;
		$part->filename = $struct->parameters[0]->value;
		$part->type = $this->GetPrimaryType($struct);
		$part->subtype = $struct->subtype;
		$part->mimetype = $this->GetMimeType($struct);
		$part->rawdata = (!$include_raw_data) ? null : $this->GetAttachmentRawData($struct->x_msg_id,$struct->x_part_id, $struct->encoding);
		return $part;
	}
	
	function GetAttachmentRawData($msgno, $partnum, $encoding_id = 0)
	{
		$content = imap_fetchbody($this->mbox, $msgno, $partnum);
		
		if ($encoding_id == 3) 
		{
			return imap_base64($content);
		} 
		elseif ($encoding_id == 4) 
		{
			return imap_qprint($content);
		} 

		return $content;
	}
	
	function GetPrimaryType(&$structure) 
	{
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
		return $primary_mime_type[(int) $structure->type];
	}
	
	/**
	*
	*/
	function GetMimeType(&$structure) 
	{
		if($structure->subtype) 
		{
			return $this->GetPrimaryType($structure) . '/' .$structure->subtype;
		}
		return "TEXT/PLAIN";
	}
	
	function GetMessageBody($msgnum, $prefer_html = true)
	{
		if ($prefer_html)
		{
			$body = $this->GetPart($msgnum, "TEXT/HTML");
			$body = $body ? $body : $this->GetPart($msgnum, "TEXT/PLAIN");
		}
		else
		{
			$body = $this->GetPart($msgnum, "TEXT/PLAIN");
			$body = $body ? $body : $this->GetPart($msgnum, "TEXT/HTML");
		}
		
		return $body;
	}

	/**
	*
	*/
	function GetPart($msg_number, $mime_type, $structure = false, $part_number = false) 
	{
		$stream = $this->mbox;
		$prefix = "";

		$structure = $structure ? $structure : imap_fetchstructure($stream, $msg_number);
		
		if ($structure) 
		{
			if ($mime_type == $this->GetMimeType($structure)) 
			{
				$part_number = $part_number ? $part_number : "1";
				$text = imap_fetchbody($stream, $msg_number, $part_number);
				
				if ($structure->encoding == 3) 
				{
					return imap_base64($text);
				} 
				else if ($structure->encoding == 4) 
				{
					return imap_qprint($text);
				} 
				else 
				{
					return $text;
				}
			}

			if ($structure->type == 1) /* multipart */ 
			{
				while(list($index, $sub_structure) = each($structure->parts)) 
				{
					if($part_number) 
					{
						$prefix = $part_number . '.';
					}
					$data = $this->GetPart($msg_number, $mime_type, $sub_structure,$prefix .    ($index + 1));
					
					if($data) 
					{
						return $data;
					}
				}
			}
			
		}
		
		// no structure returned
		return false;
	}


}

?>