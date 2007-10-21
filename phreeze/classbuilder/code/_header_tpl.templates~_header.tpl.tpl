<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	
	<title>{ldelim}$title{rdelim}</title>
	
	<!-- form validation scripts -->
	<script type="text/javascript" src="/shared/js/verysimple/validate.js"></script>
	<script type="text/javascript" src="scripts/validate_model.js"></script>

	<!-- ext js components -->
	<link rel="stylesheet" href="/shared/js/ext/resources/css/ext-all.css"/>
	<script type="text/javascript" src="/shared/js/ext/adapter/yui/yui-utilities.js"></script>
	<script type="text/javascript" src="/shared/js/ext/adapter/yui/ext-yui-adapter.js"></script>
	<script type="text/javascript" src="/shared/js/ext/ext-all.js"></script>
	
	<link rel="stylesheet" rev="stylesheet" href="styles/{$connection->DBName}.css" />
	<link rel="stylesheet" rev="stylesheet" href="/shared/css/tables.css" />
	<link rel="stylesheet" rev="stylesheet" href="/shared/css/forms.css" />

	{ldelim}* show feedback or warning if necessary using animated ext dialog *{rdelim}
	{ldelim}if isset($smarty.request.feedback){rdelim}{ldelim}assign var=feedback value=$smarty.request.feedback{rdelim}{ldelim}/if{rdelim}
	{ldelim}if isset($feedback){rdelim}
		<script type="text/javascript" src="scripts/feedback.js"></script>
		<script type="text/javascript">
			setTimeout("show_feedback('Result:', '{ldelim}ldelim{rdelim}0{ldelim}rdelim{rdelim}', '{ldelim}$feedback|escape{rdelim}')",1000);
		</script>
	{ldelim}/if{rdelim}

	{ldelim}if isset($smarty.request.warning){rdelim}{ldelim}assign var=warning value=$smarty.request.warning{rdelim}{ldelim}/if{rdelim}
	{ldelim}if isset($warning){rdelim}
		<div class="warning">{ldelim}$warning|escape{rdelim}</div>
	{ldelim}/if{rdelim}

</head>
<body>

<div id="main">
	<div id="stage">

	<h1>{$connection->DBName|studlycaps} Application</h1>

	
	<div id="nav">
	<a href="./">Home</a>
	
	{foreach from=$tables item=table}
		| <a href="index.php?action={$table->Name|studlycaps}.ListAll">{$table->Name|studlycaps}</a>
	{/foreach}

	</div>