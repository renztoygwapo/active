{title}New Discussion{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="new_discussion">
  {form action=$add_discussion_url}
    {include file=get_view_path('_discussion_form', 'discussions', $smarty.const.DISCUSSIONS_MODULE)}
    
    {wrap_buttons}
      {submit}Add Discussion{/submit}
    {/wrap_buttons}
  {/form}
</div>