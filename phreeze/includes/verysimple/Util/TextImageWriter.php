<?php
/** @package    verysimple::Util */

/**
 * Utility to stream an image containing text to the browser
 *
 * @package    verysimple::Util
 * @author Jason Hinkle
 * @copyright  1997-2011 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class TextImageWriter
{
	
	/**
	 * 
	 * @param $message
	 */
	
	
	/**
	 * Output a png image to the browser, including headers
	 * @param string $message
	 * @param int $width
	 * @param int $height
	 * @param array $backgroundColor RGB values from 0-255.  example: array(0,0,0);
	 * @param array $fontColor RGB values from 0-255.  example: array(0,0,0);
	 */
	static function Write($message, $width = 250, $height = 150, $backgroundColor = array(255,255,255), $fontColor = array(0,0,0))
	{
		$im = self::GetErrorImage($message, $width, $height, $backgroundColor, $fontColor);
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
	
	/**
	 * Given text, returns an image reference with the text included in the image
	 * @param string $message
	 * @param int $width
	 * @param int $height
	 * @param array $backgroundColor RGB values from 0-255.  example: array(0,0,0);
	 * @param array $fontColor RGB values from 0-255.  example: array(0,0,0);
	 * @return int image reference
	 */
	static function GetErrorImage($message, $width = 250, $height = 150, $backgroundColor = array(255,255,255), $fontColor = array(0,0,0))
	{
		
		$msg = str_replace("\n","",$message);
		$im = imagecreate($width, $height);
		$bgColor = imagecolorallocate($im, $backgroundColor[0],$backgroundColor[1],$backgroundColor[2]);
		$fontColor = imagecolorallocate($im, $fontColor[0],$fontColor[1],$fontColor[2]);
		$lines = explode( "\r", wordwrap($msg,($width/5),"\r"));
		$count = 0;
		foreach ($lines as $line)
		{
			imagestring($im, 1, 5, 5 + ($count * 12) , $line, $fontColor);
			$count++;
		}
		return $im;
	}
}

?>