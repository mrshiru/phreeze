<?php
require_once('IRouter.php');

class GenericRouter implements IRouter
{
	private static $routes = array();
	
	private $defaultAction = 'Default.DefaultAction';
	private $uri = '';
	private $appRoot = '';
	
	/**
	 * Constructor sets up the router allowing for patterns to be
	 * instantiated
	 *
	 * @param array associative array of $patterns
	 */
	public function __construct($appRoot = '', $defaultAction = 'Default.DefaultAction', $mapping = array())
	{
		if ($defaultAction) $this->defaultAction = $defaultAction;
		if ($appRoot) $this->appRoot = $appRoot;
		
		$this->mapRoutes($mapping);
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
	public function GetRoute( $uri = "" )
	{
		if( $uri == "" )
			$uri = $this->GetUri();
		
		// literal match check
		if ( isset(static::$routes[ $uri ]) )
		{
			// expects mapped values to be in the form: Controller.Model
			list($controller,$method) = explode(".",static::$routes[ $uri ]["route"]);
			return array($controller,$method);
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
				list($controller,$method) = explode(".",$value["route"]);
				return array($controller,$method);
			}
		}

		// if we haven't returned by now, we've found no match:
		return array("Default","Error404");
	}
	
	/**
	 * @see IRouter::GetUri()
	 */
	public function GetUri()
	{
		if (!$this->uri)
		{
			$this->uri = $_REQUEST['_REWRITE_COMMAND'];
	
			// if a root folder was provided, then we need to strip that out as well
			if ($this->appRoot)
			{
				$prefix = $this->appRoot.'/';
				while (substr($this->uri,0,strlen($prefix)) == $prefix)
				{
					$this->uri = substr($this->uri,strlen($prefix));
				}
			}
	
			// strip trailing slash
			while (substr($this->uri,-1) == '/')
			{
				$this->uri = substr($this->uri,0,-1);
			}
		}
		return $this->uri;
	}
	
	/**
	 * @inheritdocs
	 */
	public function GetUrl( $controller, $method, $params = '' )
	{		
		// @todo: implement Request Method into routemap
		$prefix = $this->appRoot ? $this->appRoot . '/' : '';
		$url = RequestUtil::GetServerRootUrl() 
			. $prefix 
			. strtolower($controller . '/' . $method);
		
		return $url;
	}
	
	/**
	 * @inheritdocs
	 */
	public function GetUrlParams()
	{
		return explode('/',$this->GetUri());
	}
	
	/**
	 * @inheritdocs
	 */
	public function GetUrlParam($index)
	{
		$params = $this->GetUrlParams();
		return count($params) > $index ? $params[$index] : '';
	}
}
?>