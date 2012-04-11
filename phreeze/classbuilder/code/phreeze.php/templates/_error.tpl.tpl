{ldelim}include file="_header.tpl" title="Error Occured"{rdelim}

{ldelim}if isset($message){rdelim}
<div class="warning">{ldelim}$message|escape{rdelim}</div>
{ldelim}/if{rdelim}

{ldelim}if isset($stacktrace){rdelim}
<div class="stacktrace"><b>Stack Trace:</b><br />{ldelim}$stacktrace{rdelim}</div>
{ldelim}/if{rdelim}

{ldelim}include file="_footer.tpl"{rdelim}