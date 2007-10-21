<?php

include("_config.php");
require_once("verysimple/Phreeze/Request.php");

// instantiate the phreezer persistance api
$G_PHREEZER = new Phreezer($csetting, $observer);

// instantiate the smarty template engine
$G_SMARTY = new Smarty();
// $G_SMARTY->assign("feedback",Request::Get("feedback"));

// dispatch the action
try
{
	Dispatcher::Dispatch($G_PHREEZER,$G_SMARTY, Request::Get("action",$default_action));
}
catch (exception $ex)
{
	$G_SMARTY->assign("message",$ex->getMessage());
	$G_SMARTY->assign("stacktrace",FormatTrace($ex->getTrace(),"\n<br /># ",true));
	$G_SMARTY->assign("code",$ex->getCode());
	$G_SMARTY->display("_error.tpl");
}



/**
 * Formats the debug_backtrace array into a printable string
 *
 * @access     public
 * @param array  debug_backtrace array
 * @param string $join the string used to join the array
 * @return string
 */
function FormatTrace($tb, $join = " :: ", $show_lines = false)
{
	$stack = "";
	$delim = "";
	for ($x = count($tb)-1; $x > 0; $x--)
	{
		$stack .= $delim . (isset($tb[$x]['class']) ? ($tb[$x]['class'] . "-&gt;") : "") . $tb[$x]['function'];
		$show_lines && isset($tb[$x]['file']) && $stack .= " (" . basename($tb[$x]['file']) . " Line " . (isset($tb[$x]['line']) ? $tb[$x]['line'] : "??") . ")";
		$delim = $join;
	}
	return $stack;
}
	
?>