{literal}
{extends file="Master.tpl"}

{block name=title}Error{/block}

{block name=banner}
	<h1>Oh Snap!</h1>
{/block}

{block name=content}

	<h2 onclick="$('#stacktrace').show('slow');" style="cursor: pointer;">{$message|escape}</h2>

	<p>Please return the the previous page and verify that all required fields
	have been completed.  If you continue to experience this error please
	contact support.  We're sorry for the inconvenience.</p>

	<div id="stacktrace" style="display: none; text-align: left; background-color: #eeeeee; border: solid 1px #cccccc; padding: 10px; font-family: courier new, courier; font-size: 8pt;">
		<p style="font-weight: bold;">Stack Trace:</p>
		{if $stacktrace}
			<p style="white-space: nowrap; overflow: auto; padding-bottom: 15px;">{$stacktrace|escape|nl2br}</p>
		{/if}
	</div>

{/block}

{block name=customFooterScripts}
{/block}
{/literal}