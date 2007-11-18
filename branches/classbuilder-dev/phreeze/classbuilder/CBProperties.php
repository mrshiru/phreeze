<?php
/** @package   Phreeze::ClassBuilder */

/**
 * Manages the ClassBuilder properties.
 *
 * ClassBuilder makes use of many properties amd parameters.
 *
 * CBProperties retains this information in an ini file and provides
 * it to the user next time he starts CB again.
 *
 * @package Phreeze::ClassBuilder
 * @author  laplix
 * @since   2007-11-02
 */
class CBProperties
{

   /**
    * file name.
    * @var string
    */
   private $filename;

   /**
    * contains the ini data
    * @var array
    * @access private
    */
   private $ini;

   /**
    * Constructor.
    *
    * Reads the properties ini file and store the data in memory.
    * 
    * @param string $filename Properties file name.
    */
   function __construct($filename = null)
   {
      $this->filename = 'CBProperties.ini.php';
      if (!empty($filename))
      {
         $this->filename = $filename;
      }
      $this->ini = $this->iniRead();
   }

   /**
    * Returns the entire ini array.
    *
    * @return array
    */
   public function getIni()
   {
      return $this->ini;
   }

   /**
    * Returns an array containing the section data.
    *
    * @param string $sectionName    section to read
    * @return array
    */
   public function getSection($sectionName)
   {
      $ini = array();
      if (array_key_exists($sectionName, $this->ini))
      {
         $ini = $this->ini[$sectionName];
      }
      return $ini;
   }

   /**
    * Writes a section into the ini array.
    *
    * If the named section already exists, it will be overwritten.
    *
    * @param string $sectionName    section to write to
    * @param mixed $section         data to write. can be empty.
    *
    * @todo Maybe just overwrite existing keys defined within the
    *       provided section, preserving the other keys.
    */
   public function putSection($sectionName, $section=null)
   {
      $this->ini[$sectionName] = $section;
   }

   /**
    * Writes a key/value pair into a section.
    *
    * @param string $key
    * @param mixed $value
    * @param string $sectionName    If sectionname is empty, the property
    *                               will be written to the main sectiom.
    * @return  mixed                old key value.
    */
   public function putValue($key, $value, $sectionName='VerySimple')
   {
      $val = false;
      if (!empty($key) && !empty($sectionName))
      {
         $val = $this->getValue($key, $sectionName);
         $this->ini[$sectionName][$key] = $value;
      }
      return $val;
   }

   /**
    * Returns the value of a section key.
    *
    * @param $string $key
    * @param $string $sectionName      if sectionName is empty, return
    *                                  key value from the 'Global' section.
    * @return muxed
    */
   public function getValue($key, $sectionName='VerySimple')
   {
      $val = null;
      if (array_key_exists($sectionName, $this->ini))
      {
         if (!empty($key)
            && is_array($this->ini[$sectionName])
            && array_key_exists($key, $this->ini[$sectionName]))
         {
            $val = $this->ini[$sectionName][$key];
         }
      }
      return $val;
   }

   /**
    * Reads the ini file and returns the data array.
    * @return array
    */
   public function iniRead()
   {
      $ini = array();
      $sectionPattern = '/^\[(.+)\]/';
      $keyvalPattern = '/^([a-z0-9_.-]+)\s*=\s*(.*)$/i';

      // setup the default section.
      $sectionName = 'VerySimple';

      $dataLines = $this->readFile($this->filename);

      if (!empty($dataLines))
      {
         foreach($dataLines as $line)
         {
            $line = trim($line);

            // bypass comments
            if (substr($line, 0, 1) != ';')
            {
               // we have a section
               if (preg_match($sectionPattern, $line, $match))
               {
                  $sectionName = $match[1];
               }
               else
               {
                  // we have a key=value pair
                  if (preg_match($keyvalPattern, $line, $match))
                  {
                     $ini[$sectionName][$match[1]] = trim($match[2]);
                  }                     
               }
            }
         }
      }
      return $ini;
   }

   /**
    * Writes the ini file.
    *
    * @return bool
    */
   public function iniWrite()
   {
      // kill the script.
      $content = "; <?php die(); ?>\n";

      $sections = array_keys($this->ini);
      foreach($sections as $section)
      {
         $content .= "\n[" . $section . "]\n";

         foreach($this->ini[$section] as $key => $value)
         {
            $content .= $key . ' = ' . $value . "\n";
         }
      }

      $status = $this->writeFile($this->filename, $content);
      return $status;
   }

   /**
    * Reads the file and returns the contents.
    *
    * @param string $filename
    * @return array
    */
   private function readFile($filename)
   {
      $content = null;
      if (file_exists($filename))
      {
         $content = file($filename);
      }
      return $content;
   }

   /**
    * writes the data to the ini file.
    *
    * @TODO if an error occurs, display a message
    *
    * @param string $filename
    * @param array $data
    * @return bool
    */ 
   private function writeFile($filename, $data)
   {
      $fp = fopen($filename, "w");
      $bytes = fwrite($fp, $data);
      fclose($fp);
      return ($bytes) ? true : false;
   }


}

?>
