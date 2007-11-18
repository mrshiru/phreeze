<?php
/** @package Phreeze::ClassBuilder */

/**
 * Holds a parameter.
 *
 * @package Phreeze::ClassBuilder
 * @author  laplix
 * @since   2007-11-02
 */
class CBParameter
{
   // TODO need to check if Smarty can use objext->method() in its 
   // template. If so, these are gonna go private.
   public $name;
   public $value;

   /**
    * Constructor.
    * @param string $name     Parameter name.
    * @param string $value    Parameter value.
    */
   function __construct($name=null, $value=null) {
      $this->name = $name;
      $this->value = $value;
   }

   /**
    * Sets the value of the parameter.
    *
    * If name is provided, also sets the parameter name.
    *
    * @param mixed $value
    * @param string $name
    */
   function set($value, $name=null)
   {
      if (!empty($name))
      {
         $this->name = $name;
      }
      $this->value = $value;
   }

   /**
    * Returns the parameter name.
    *
    * @return string
    */
   function name()
   {
      return $this->name;
   }

   /**
    * Return the parameter value.
    *
    * @return mixed
    */
   function value()
   {
      return $this->value;
   }

   /**
    * Return the parameter as a key=value string
    * @return string
    */
   function toString()
   {
      return $this->name . ' = ' . $this>value;
   }
}

?>
