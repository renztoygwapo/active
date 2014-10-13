{title}Edit Filter{/title}
{add_bread_crumb}Edit Filter{/add_bread_crumb}

<div id="incoming_mail_filter">
  {form action=$active_filter->getEditUrl() method=post id="filter_form"}
    {include file=get_view_path('_email_filter_form', 'fw_incoming_mail_filter_admin', $smarty.const.EMAIL_FRAMEWORK)}
    
    {wrap_buttons}
  	  {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>
