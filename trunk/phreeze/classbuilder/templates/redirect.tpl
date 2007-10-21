{include file="_header.tpl" page_title="One moment please..."}

<p class="header">&nbsp;One moment please...</p>
<p>Please <a href="{$redirect}">click here</a> if you are not redirected within 5 seconds...</p>

{include file="_footer.tpl"}

{literal}
<script>
self.location='{/literal}{$redirect}{literal}';
</script>
{/literal}