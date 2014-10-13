{title}New Discussion{/title}
{add_bread_crumb}New Discussion{/add_bread_crumb}

{form action=$add_discussion_url method=post enctype="multipart/form-data" ask_on_leave=yes autofocus=yes class='big_form'}
  {include file=get_view_path('_discussion_form', 'discussions', 'discussions')}
  
  {wrap_buttons}
    {submit}Add Discussion{/submit}
  {/wrap_buttons}
{/form}