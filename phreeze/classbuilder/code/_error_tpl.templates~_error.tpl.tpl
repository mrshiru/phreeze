{ldelim}include file="_header.tpl" title="Error Occured"{rdelim}

<div class="warning">{ldelim}$message{rdelim}</div>

{ldelim}if stacktrace{rdelim}
<div class="stacktrace"><b>Stack Trace:</b><br />{ldelim}$stacktrace{rdelim}</div>
{ldelim}/if{rdelim}

{ldelim}include file="_footer.tpl"{rdelim}