<?php

require_once("_global.php");

$G_SMARTY->assign("G_CONNSTR",$G_CONNSTR);
$G_SMARTY->display("new_connection.tpl");

?>