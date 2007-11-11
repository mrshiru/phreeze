<?php
/** @package    verysimple::Email */

/**
 * Object representation of an Email recipient
 * @package    verysimple::Email
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    LGPL
 * @version    1.0
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
}

?>