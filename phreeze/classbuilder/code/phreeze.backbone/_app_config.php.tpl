<?php
/**
 * @package {$appname}
 *
 * APPLICATION-WIDE CONFIGURATION SETTINGS
 *
 * This file contains application-wide configuration settings.  The settings
 * here will be the same regardless of the machine on which the app is running.
 *
 * This configuration should be added to version control.
 *
 * No settings should be added to this file that would need to be changed
 * on a per-machine basic (ie local, staging or production).  Any
 * machine-specific settings should be added to _machine_config.php
 */

/** application path setup */
GlobalConfig::$APP_ROOT = realpath("./");

/**
 * RENDER ENGINE
 * Haters be hatin' on Smarty? You can use any template system that implements
 * IRenderEngine for the view layer.  Phreeze provides pre-built
 * implementations for Smarty, Savant and plain PHP.
 */
require_once 'verysimple/Phreeze/SmartyRenderEngine.php';
GlobalConfig::$TEMPLATE_ENGINE = 'SmartyRenderEngine';
GlobalConfig::$TEMPLATE_PATH = GlobalConfig::$APP_ROOT . '/templates/';
GlobalConfig::$TEMPLATE_CACHE_PATH = GlobalConfig::$APP_ROOT . '/templates_c/';

/**
 * ROUTE MAP
 * The route map connects URLs to Controller+Method and additionally maps the
 * wildcards to a named parameter so that they are accessible inside the
 * Controller without having to parse the URL for parameters such as IDs
 */
GlobalConfig::$ROUTE_MAP = array(

	// default controller when no route specified
	'GET:' => array('route' => 'Default.Home'),
{foreach from=$tables item=table name=tablesForEach}{if isset($tableInfos[$table->Name])}
	{assign var=singular value=$tableInfos[$table->Name]['singular']}
	{assign var=plural value=$tableInfos[$table->Name]['plural']}

	// {$table->Name}
	'GET:{$plural|lower}' => array('route' => '{$singular}.ListView'),
	'GET:{$singular|lower}/{if $table->PrimaryKeyIsAutoIncrement()}(:num){else}(:any){/if}' => array('route' => '{$singular}.SingleView', 'params' => array('{$table->GetPrimaryKeyName()|studlycaps|lcfirst}' => 1)),
	'GET:api/{$singular|lower}' => array('route' => '{$singular}.Query'),
	'POST:api/{$singular|lower}' => array('route' => '{$singular}.Create'),
	'GET:api/{$singular|lower}/{if $table->PrimaryKeyIsAutoIncrement()}(:num){else}(:any){/if}' => array('route' => '{$singular}.Read', 'params' => array('{$table->GetPrimaryKeyName()|studlycaps|lcfirst}' => 2)),
	'PUT:api/{$singular|lower}/{if $table->PrimaryKeyIsAutoIncrement()}(:num){else}(:any){/if}' => array('route' => '{$singular}.Update', 'params' => array('{$table->GetPrimaryKeyName()|studlycaps|lcfirst}' => 2)),
	'DELETE:api/{$singular|lower}/{if $table->PrimaryKeyIsAutoIncrement()}(:num){else}(:any){/if}' => array('route' => '{$singular}.Delete', 'params' => array('{$table->GetPrimaryKeyName()|studlycaps|lcfirst}' => 2)),
{/if}{/foreach}

	// catch any broken API urls
	'GET:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'PUT:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'POST:api/(:any)' => array('route' => 'Default.ErrorApi404'),
	'DELETE:api/(:any)' => array('route' => 'Default.ErrorApi404')
);

/**
 * FETCHING STRATEGY
 * You may uncomment any of the lines below to specify always eager fetching.
 * Alternatively, you can copy/paste to a specific page for one-time eager fetching
 * If you paste into a controller method, replace $G_PHREEZER with $this->Phreezer
 */
{foreach from=$tables item=tbl}
{foreach from=$tbl->Constraints item=constraint}// $GlobalConfig->GetInstance()->GetPhreezer()->SetLoadType("{$tbl->Name|studlycaps}","{$constraint->Name}",KM_LOAD_EAGER);
{/foreach}
{/foreach}
?>