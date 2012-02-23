<?php
interface IRouter
{
	/**
	 * Given a controller, method and params, returns a url that points
	 * to the correct location
	 *
	 * @param string $controller
	 * @param string $method
	 * @param string $params in the format param1=val1&param2=val2
	 * @return string URL
	 */
	public function GetUrl($controller,$method,$params = '');

	/**
	* Returns the controller and method for the given URI
	*
	* @param string the url, if not provided will be obtained using the current URL
	* @return array($controller,$method)
	*/
	public function GetRoute($uri = "");
}
?>
