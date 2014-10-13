{title}Send Quote{/title}

{form action=$active_quote->getSendUrl() method=post}
<div id="send_quote">
  <div class="fields_wrapper">
    {empty_slate name=send active_quote=$active_quote module=invoicing}
    <input type="hidden" name="quote[send_emails]" value="1"/>
  </div>
  {wrap_buttons}
    {submit}Send Quote{/submit}
  {/wrap_buttons}
</div>
{/form}