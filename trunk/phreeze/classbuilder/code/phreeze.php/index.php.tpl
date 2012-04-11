<?php
/** @package    {$connection->DBName|studlycaps} */

{literal}
/** instantiate necesary configuration options */
include("_config.php");

// dispatch the action and catch any unhandled exceptions
try
{
	$gc = GlobalConfig::GetInstance();
	Dispatcher::Dispatch(
		$gc->GetPhreezer()
		, $gc->GetRenderEngine()
		, RequestUtil::Get("action",$gc->GetDefaultAction())
		, $gc->GetContext()
		, $gc->GetUrlWriter()
	);
}
catch (exception $ex)
{
	$gc->GetRenderEngine()->assign("message",$ex->getMessage());
	$gc->GetRenderEngine()->assign("stacktrace","# " . $gc->FormatTrace($ex->getTrace(),"\n<br /># ",true));
	$gc->GetRenderEngine()->assign("code",$ex->getCode());
	$gc->GetRenderEngine()->display("_error.tpl");
}
	
?>{/literal}