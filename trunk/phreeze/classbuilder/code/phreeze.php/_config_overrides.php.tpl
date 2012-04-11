<?php
/**
 * Any necessary overrides to GlobalConfig can be placed here.  This
 * file will be included by _config.php immediately after GlobalConfig
 * is initialized.
 */

{literal}
/** adjust static values before GlobalConfig is initialized */
// GlobalConfig::$debug_mode = true;
// GlobalConfig::$default_action = "Default.DefaultAction";
// GlobalConfig::$url_format = "index.php?action=%s.%s{delim}%s";

/** require any model files that may be persisted in the session */
// require_once("Model/Account.php");

/** override error reporting level */
// error_reporting(E_ERROR); // E_ALL | E_ERROR | E_WARNING | E_NOTICE
{/literal}

/** 
 * Fetching Strategy Configuration:
 * You may uncomment any of the lines below to specify always eager fetching.
 * Alternatively, you can copy/paste to a specific page for one-time eager fetching
 * If you paste into a controller method, replace $G_PHREEZER with $this->Phreezer
 */
{foreach from=$tables item=tbl}
{foreach from=$tbl->Constraints item=constraint}// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("{$tbl->Name|studlycaps}","{$constraint->Name}",KM_LOAD_EAGER);
{/foreach}
{/foreach}
?>