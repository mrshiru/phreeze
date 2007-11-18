<?php
/** @package    verysimple::IO */

/** import supporting libraries */
require_once("FileHelper.php");

/**
 * Provided object oriented access to a file system directory
 *
 * @package    verysimple::IO
 * @author Jason Hinkle
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class FolderHelper
{
	private $Path;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $path uri to directory to manipulate
	 */	
	function FolderHelper($path)
	{
		$this->Path = $path;
	}
	
	/**
    * Returns an array of FileHelper objects.
    *
    * GetFiles can take either a regex pattern (default) or a glob
    * pattern. 
    *
	 * @access public
    * @param string $pattern  Pattern to search for. Defaults to
    *                         regex: .*
    *                         glob : *
    * @param string $type     type of pattern. can be regex or glob.
	 * @return array
	 */	
	public function GetFiles($pattern = ".*", $type='regex')
	{
		$files = Array();

      switch ($type)
      {
         case 'glob':
            foreach(glob($this->Path.$pattern) as $fname)
            {
               if (is_file($fname))
               {
                  $files[] = new FileHelper($fname);
               }
            }
            break;

         case 'regex':
         default:
            $dh = opendir($this->Path);
            $fnames = array();
            while ($fname = readdir($dh)) 
            {
               if (is_file($this->Path.$fname) && preg_match("/{$pattern}/i", $fname))
               {
                  $files[] = new FileHelper($this->Path.$fname);
               }
            }
            closedir($dh);
            break;
      }
		return $files;
	}

	/**
	 * Returns an array of FileHelper objects
    *
    * ls() uses the glob pattern to find its files.
    * 
	 * @access public
	 * @param string $pattern A glob type pattern
	 * @return array
	 */	
	public function ls($pattern = '*')
	{
		return $this->GetFiles($pattern, 'glob');
	}

}
?>
