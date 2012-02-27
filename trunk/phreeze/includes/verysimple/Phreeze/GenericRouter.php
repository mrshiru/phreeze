<?php
require_once('IRouter.php');

class GenericRouter implements IRouter
{
	private static $routes = array();
	
	private $defaultAction = 'Default.DefaultAction';
	private $uri = '';
	private $appRoot = '';
	
	private $currentRouteParams;
	
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
		$prefix = $this->appRoot ? $this->appRoot : '';
		$url = RequestUtil::GetServerRootUrl() . $prefix;
		$requestMethod = RequestUtil::GetMethod();
		
		if( $params == '' || count($params) == 0 )
			$url = $url . '/' . strtolower($controller . '/' . $method);
		else
		{
			foreach( static::$routes as $key => $value)
			{
				list($routeController,$routeMethod) = explode(".",$value["route"]);
				
				if( ($routeController == $controller) && ($routeMethod == $method) &&
				    (count($params) == count($value["params"]) && $requestMethod == $value["method"])
				  )
				{					
					$keyArr = explode('/',$key);
					
					// merge the parameters passed in with the routemap's path
					// example: path is user/(:num)/events and parameters are [userCode]=>111
					// this would yiled an array of [0]=>user, [1]=>111, [2]=>events 
					foreach( $value["params"] as $rKey => $rVal )
						$keyArr[$value["params"][$rKey]] = $params[$rKey];
						
					// put the url together:
					foreach( $keyArr as $urlPiece )
						$url = $url . '/' . $urlPiece;
					
					break;
				}
			}
		}
		
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
	public function GetUrlParam($paramKey)
	{
		$params = $this->GetUrlParams();
		$uri = $this->GetUri();
		$count = 0;
		$routeMap = "";
		
		// replace the current url with the routemap key version
		foreach ( $params as $arg )
		{
			if( preg_match('/^\d+$/', $arg) )
				$routeMap = $routeMap . '(:num)/';
			else
				$routeMap = $routeMap . $params[$count] . '/';
			$count++;
		}
		
		// remove trailing slash:
		$routeMap = substr($routeMap, 0, -1);
		
		foreach ( static::$routes as $key => $value)
		{
			if( $key == $routeMap )
			{
				$indexLocation = $value["params"][$paramKey];
				return $params[$indexLocation];
			}
		}
		
		return "";
	}
}
?>