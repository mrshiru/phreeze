<?php
/** @package    verysimple::Email */

/**
 * Object representation of an Email recipient
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    LGPL
 * @version    1.1
 */
class Recipient
{
    var $Email;
    var $RealName;
	
    function Recipient($email, $name="")
    {
        $this->Email = $email;
        $this->RealName = $name!="" ? $name : $email;
    }
    
    /**
     * Parses an address in the either format:
     * "Real Name <email@address.com>" or "email@address.com"
     * @param string $val to parse
     */
    function Parse($val)
    {
		$pair = explode("<",$val);
		
		if (isset($pair[1]))
		{
			$this->RealName = trim($pair[0]);
			$this->Email = trim( str_replace(">","",$pair[1]) );
		}
		else
		{
			$this->Email = $val;
			$this->RealName = $val;
		}
		
		// just in case there was no realname
		if ($this->RealName == "") $this->RealName = $this->Email;
		
	}
	
	/**
	 * Returns true if $this->Email appears to be a valid email
	 * @return bool
	 */
	function IsValidEmail()
	{
		return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $this->Email);
	}
}

?>