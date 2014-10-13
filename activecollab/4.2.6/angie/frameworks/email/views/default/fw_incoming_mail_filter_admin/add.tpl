{title}New Filter{/title}
{add_bread_crumb}New Filter{/add_bread_crumb}

<div id="incoming_mail_filter">
  {form action=$active_filter->getAddUrl() method=post id="filter_form"}
    {include file=get_view_path('_email_filter_form', 'fw_incoming_mail_filter_admin', $smarty.const.EMAIL_FRAMEWORK)}
    
    {wrap_buttons}
  	  {submit}Add Filter{/submit}
    {/wrap_buttons}
  {/form}
</div>
