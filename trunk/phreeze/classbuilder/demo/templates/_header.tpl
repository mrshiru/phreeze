<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" rev="stylesheet" href="styles/demo.css" />
	
	<link rel="stylesheet" rev="stylesheet" href="/shared/css/tables.css" />
	<link rel="stylesheet" rev="stylesheet" href="/shared/css/forms.css" />

	<!-- datepicker -->
	<link rel="stylesheet" type="text/css" media="all" href="/shared/js/calendar/calendar-blue.css" title="win2k-cold-1" />
	<script type="text/javascript" src="/shared/js/calendar/calendar.js"></script>
	<script type="text/javascript" src="/shared/js/calendar/lang/calendar-en.js"></script>
	<script type="text/javascript" src="/shared/js/calendar/calendar-setup.js"></script>

	<!-- ajax related -->
	<script type="text/javascript" src="/shared/js/prototype.js"></script>
	<script type="text/javascript" src="/shared/js/scriptaculous/scriptaculous.js"></script>
	<script type="text/javascript" src="/shared/js/json.js"></script>
	<script type="text/javascript" src="/shared/js/verysimple/validate.js"></script>

	<title>Phreeze Framework Demo: </title>

</head>
<body>

<div id="stage">

{if $smarty.request.feedback}
	<div class="feedback">{$smarty.request.feedback|escape}</div>
{elseif $feedback}
	<div class="feedback">{$feedback|escape}</div>
{/if}

{if $smarty.request.warning}
	<div class="warning">{$smarty.request.warning|escape}</div>
{elseif $warning}
	<div class="warning">{$warning|escape}</div>
{/if}
