<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("verysimple/HTTP/UrlWriter.php");
require_once("verysimple/HTTP/Request.php");
require_once("verysimple/HTTP/Context.php");
require_once("verysimple/Authentication/Authenticator.php");

/**
 * Controller is a base controller object used for an MVC pattern
 * This controller uses Phreeze ORM and Smarty Template Engine
 * This controller could be extended to use a differente ORM and
 * Rendering engine as long as they implement compatible functions.
 *
 * @package    verysimple::Phreeze 
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.2
 */
abstract class Controller
{
	protected $Phreezer;
	protected $Smarty;
	protected $ModelName;
	protected $Context;
	protected $UrlWriter;
	private $_cu;
	public $GUID;
	
	/**
	 * Constructor initializes the controller.  This method cannot be overriden.  If you need
	 * to do something during construction, add it to Init
	 * 
	 * @param Phreezer $phreezer Object persistance engine
	 * @param Smarty $smarty rendering engine
	 * @param Context (optional) a context object for persisting the state of the current page
	 * @param UrlWriter (optional) a custom writer for URL formatting
	 */
	final function Controller($phreezer, $smarty, $context = null, $urlwriter = null)
	{
		$this->Phreezer = $phreezer;
		$this->Smarty = $smarty;
		$this->GUID = $this->Phreezer->DataAdapter->GetDBName() . "_" . str_replace(".","_", $_SERVER['REMOTE_ADDR']);
		
		$this->UrlWriter = $urlwriter ? $urlwriter : new UrlWriter();

		if ($context)
		{
			$this->Context = $context;
		}
		else
		{
			$this->Context = new Context();
			$this->Context->GUID = "CTX_" . $this->GUID;
		}
		
		// assign some variables globally for the views
		$this->Assign("CURRENT_USER",$this->GetCurrentUser());
		$this->Assign("URL",$this->UrlWriter);
		
		$this->Init();
		
		if (!$this->ModelName)
		{
			throw new Exception(get_class($this) . " did not set ModelName during Init()");
		}
	}
	
	/**
	 * Init is called by the base constructor.  You must set the property
	 * $this->ModelName to the name of the primary model that this controller
	 * manages.  If this controller doesn't manage any specific model, you 
	 * may set ModelName to any dummy value.
	 */
	abstract protected function Init();
	
	/**
	 * LoadFromForm should load the object specified by primary key = $pk, or
	 * create a new instance of the object.  Then should overwrite any applicable
	 * properties with user input.
	 *
	 * @param variant $pk the primary key (optional)
	 * @return Phreezable a phreezable object
	 */
	abstract protected function LoadFromForm($pk = null);
	
	/**
	 * Displays the ListAll view for the primary model object.  Because the 
	 * datagrid is populated via ajax, no model data is populated here
	 */
	public function ListAll()
	{
		$this->Smarty->display("View" . $this->ModelName .  "ListAll.tpl");
		//$this->_ListAll(null, Request::Get("page",1), Request::Get("limit",20));
	}

	/**
	 * Displays the ListAll view for the primary model object in the event that
	 * ajax will not be used.  The model data is populated
	 *
	 * @param Criteria $criteria
	 * @param int $current_page number of the current page (for pagination)
	 * @param int $limit size of the page (for pagination)
	 */
	protected function _ListAll($criteria, $current_page, $limit)
	{
		$page = $this->Phreezer->Query($this->ModelName,$criteria)->GetDataPage($current_page,$limit);
		$this->Smarty->assign($this->ModelName . "DataPage", $page);
		$this->Smarty->display("View" . $this->ModelName .  "ListAll.tpl");
	}
	
	/**
	 * Renders a datapage as XML for use with a datagrid.  The optional $additionalProps allows
	 * retrieval of properties from foreign relationships
	 *
	 * @param DataPage $page
	 * @param Array $additionalProps (In the format Array("GetObjName1"=>"PropName","GetObjName2"=>"PropName1,PropName2")
	 */
	protected function RenderXML($page,$additionalProps = null)
	{
		header('Content-type: text/xml');
		print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";

		print "<DataPage>\r\n";
		print "<ObjectName>".htmlspecialchars($page->ObjectName)."</ObjectName>\r\n";
		print "<ObjectKey>".htmlspecialchars($page->ObjectKey)."</ObjectKey>\r\n";
		print "<TotalRecords>".htmlspecialchars($page->TotalResults)."</TotalRecords>\r\n";
		print "<TotalPages>".htmlspecialchars($page->TotalPages)."</TotalPages>\r\n";
		print "<CurrentPage>".htmlspecialchars($page->CurrentPage)."</CurrentPage>\r\n";
		print "<PageSize>".htmlspecialchars($page->PageSize)."</PageSize>\r\n";
	
		print "<Records>\r\n";
		
		// get the fieldmap for this object type
		$fms = $this->Phreezer->GetFieldMaps($page->ObjectName);
		
		foreach ($page->Rows as $obj) 
		{
			print "<" . htmlspecialchars($page->ObjectName) . ">\r\n";
			foreach (get_object_vars($obj) as $var => $val)
			{
				// depending on what type of field this is, do some special formatting
				$fm = isset($fms[$var]) ? $fms[$var]->FieldType : FM_TYPE_UNKNOWN;
				
				if ($fm == FM_TYPE_DATETIME)
				{
					$val = strtotime($val) ? date("m/d/Y h:i A",strtotime($val)) : $val;
				}
				elseif ($fm == FM_TYPE_DATE)
				{
					$val = strtotime($val) ? date("m/d/Y",strtotime($val)) : $val;
				}
				
				// $val = htmlentities(print_r($_REQUEST,1) );
				print "<" . htmlspecialchars($var) . ">" . htmlspecialchars($val) . "</" . htmlspecialchars($var) . ">\r\n";
			}
			
			
			// Add any properties that we want from child objects
			if ($additionalProps)
			{
				foreach ($additionalProps as $meth => $propPair)
				{
					$props = explode(",",$propPair);
					foreach ($props as $prop)
					{
						print "<" . htmlspecialchars($meth . $prop) . ">" . htmlspecialchars($obj->$meth()->$prop) . "</" . htmlspecialchars($meth . $prop) . ">\r\n";
					}
				}
		}
					
			print "</" . htmlspecialchars($page->ObjectName) . ">\r\n";
		}
		print "</Records>\r\n";
		
		print "</DataPage>\r\n";
	}
	
	/**
	 * This method calls LoadFromForm to retrieve a model object populated with user
	 * input.  The input is validated and a ValidationResponse is rendered in JSON format
	 */
	function ValidateInput()
	{
		require_once("ValidationResponse.php");
		$vr = new ValidationResponse();
		
		$obj = $this->LoadFromForm();

		if ($obj->Validate())
		{
			$vr->Success = true;
		}
		else
		{
			$vr->Success = false;
			$vr->Errors = $obj->GetValidationErrors();
			$vr->Message = "Validation Errors Occured";
		}

		$this->RenderJSON($vr);
	}
	
	/**
	 * Returns an array of all property names in teh primary model
	 * 
	 * @return array
	 */
	protected function GetColumns()
	{
		$counter = 0;
		$props = array();
		foreach (get_class_vars($this->ModelName)as $var => $val)
		{
			$props[$counter++] = $var;
		}
		return $props;
	}
	
	/**
	 * Returns a unique ID for this session based on connection string and remote IP
	 * This is a reasonable variable to use as a session variable because it ensures
	 * that if other applications on the same server are running phreeze, there won't
	 * be cross-application authentication issues.  Additionally, the remote ip
	 * helps to make session hijacking more difficult
	 *
	 * @deprecated use $controller->GUID instead
	 * @return string
	 */
	private function GetGUID()
	{
		return $this->GUID;
	}
	
	/**
	 * Clears the current authenticated user from the session
	 */
	protected function ClearCurrentUser()
	{
		Authenticator::ClearAuthentication($this->GUID);
	}
	
	/**
	 * Sets the given user as the authenticatable user for this session.
	 *
	 * @param IAuthenticatable The user object that has authenticated
	 */
	protected function SetCurrentUser(IAuthenticatable $user)
	{
		Authenticator::SetCurrentUser($user,$this->GUID);
	}

	/**
	 * Returns the currently authenticated user, or null if a user has not been authenticated.
	 *
	 * @return IAuthenticatable || null
	 */
	protected function GetCurrentUser()
	{
		if (!$this->_cu)
		{
			$this->Phreezer->Observe("Loading CurrentUser from Session");
			$this->_cu = Authenticator::GetCurrentUser($this->GUID);
			if ($this->_cu)
			{
				$this->_cu->Refresh($this->Phreezer);
			}
		}
		else
		{
			$this->Phreezer->Observe("Using previously loaded CurrentUser");
		}
		
		return $this->_cu;
	}
	
	/**
	 * Check the current user to see if they have the requested permission.
	 * If so then the function does nothing.  If not, then the user is redirected
	 * to $on_fail_action (if provided) or an AuthenticationException is thrown
	 *
	 * @param int $permission Permission ID requested
	 * @param string $on_fail_action (optional) The action to redirect if require fails
	 * @throws AuthenticationException
	 */
	protected function RequirePermission($permission, $on_fail_action = "")
	{
		$this->Phreezer->Observe("Checking For Permission '$permission'");
		$cu = $this->GetCurrentUser();
		
		if (!$cu || !$cu->IsAuthorized($permission))
		{
			if ($on_fail_action)
			{
				$this->Redirect($on_fail_action,"You are not authorized to view this page and/or your session has expired");
			}
			else
			{
				$ex = new AuthenticationException("You are not authorized to view this page and/or your session has expired",500);
				$this->Crash("Permission Denied",500,$ex);
			}
		}
	}
	
	/**
	 * Assigns a variable to the view
	 *
	 * @param string $varname
	 * @param variant $varval
	 */
	protected function Assign($varname,$varval)
	{
		$this->Smarty->assign($varname,$varval);
	}
	
	/**
	 * Renders the specified view
	 *
	 * @param string $view
	 * @param string $format
	 */
	protected function Render($view,$format="View")
	{
		$this->Smarty->display("View".$view.".tpl");
	}
	
	/**
	 * Renders the given value as JSON
	 *
	 * @param variant the variable, array, object, etc to be rendered as JSON
	 */
	protected function RenderJSON($var)
	{
		require_once("JSON.php");
		$json = new Services_JSON();
		print $json->encode($var);
	}
	
	/**
	 * Send a crash message to the browser and terminate
	 *
	 * @param string $errmsg text message to display
	 * @param int $code used for support for this error
	 * @param Exception $exception if exception was thrown, can be provided for more info
	 */
	protected function Crash($errmsg = "Unknown Error", $code = 0, $exception = null)
	{
		$ex = $exception ? $exception : new Exception($errmsg, $code);
		throw $ex;
	}

	/**
	 * Redirect to the appropriate page based on the action.  This function will
	 * call "exit" so do not put any code that you wish to execute after Redirect
	 *
	 * @param string $action in the format Controller.Method
	 * @param string $feedback
	 * @param array $params
	 */
	protected function Redirect($action, $feedback = "", $params = "")
	{
		$params = is_array($params) ? $params : array();
	
		if ($feedback)
		{
			$params["feedback"] = $feedback; 
		}
		
		// support for deprecated Controller/Method format
		list($controller,$method) = explode(".", str_replace("/",".",$action));
		
		$url = $this->UrlWriter->Get($controller,$method,$params);

		$this->Smarty->assign("url",$url);
		$this->Smarty->display("_redirect.tpl");
		
		exit;
	}
	
    /**
    * Throw an exception if an undeclared method is accessed
    *
	* @access     public
	* @param      string $name
	* @param      variant $vars
	* @throws     Exception
	*/
	function __call($name,$vars = null)
	{
		throw new Exception(get_class($this) . "::" . $name . " is not implemented");
	}
}

?>