<input type="text" id="{$fieldname}" size="11" name="{$fieldname}" value="{$fieldvalue|date_format:"%m/%d/%Y"}" />
<a href="#" name="{$fieldname}_Trigger" id="{$fieldname}_Trigger" onclick="document.getElementById('{$fieldname}').focus(); return false;"><img src="/shared/images/ico_calendar.gif" /></a>
<script type="text/javascript">
Calendar.setup({ldelim}
inputField     :    "{$fieldname}",     // id of the input field
ifFormat       :    "%m/%d/%Y",         // format of the input field
button         :    "{$fieldname}",     // trigger for the calendar (button ID)
eventName      :    "focus"             // the button event that fires the calendar
{rdelim});
</script>