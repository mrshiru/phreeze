<?php
require_once('IRouter.php');

class GenericRouter implements IRouter
{
	protected static $routes = array();
	
	/**
	 * Constructor sets up the router allowing for patterns to be
	 * instantiated
	 *
	 * @param array associative array of $patterns
	 */
	public function __construct($patterns)
	{
		$this->mapRoutes($patterns);
	}

	/**
	 * Adds router mappings to our routes array.
	 * 
	 * @param array $src
	 */
	private static function mapRoutes( $src )
	{
		foreach ( $src as $key => $val )
			static::$routes[ $key ] = $val;
	}
	
	/**
	 * @inheritdocs
	 */
	public function GetUrl( $controller, $method, $params = '' )
	{
		// @todo: update this for generic RESTful urls
	}
	
	/**
	 * @inheritdocs
	 */
	public function GetRoute( $uri = "" )
	{
		if( $uri == "" )
			$uri = RequestUtil::GetCurrentURL();
		
		// literal match check
		if ( isset(static::$routes[ $uri ]) )
		{
			// expects mapped values to be in the form: Controller.Model
			list($controller,$method) = explode(".",static::$routes[ $uri ]);
			return array($controller,$method,$queryString);
		}
		
		// loop through the route map for wild cards:
		foreach( static::$routes as $key => $value)
		{
			// convert wild cards to RegEx.
			// currently only ":any" and ":num" are supported wild cards
			$key = str_replace( ':any', '.+', $key );
			$key = str_replace( ':num', '[0-9]+', $key );
			
			// check for RegEx match
			if ( preg_match( '#^' . $key . '$#', $uri ) )
			{
				// expects mapped values to be in the form: Controller.Model
				list($controller,$method) = explode(".",$value);
				return array($controller,$method,$queryString);
			}
		}

		// if we haven't returned by now, we've found no match:
		return array("Default","Error404");
	}
}
?>