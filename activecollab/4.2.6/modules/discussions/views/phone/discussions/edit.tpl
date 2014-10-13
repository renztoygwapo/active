{title}Update Discussion{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="edit_discussion">
  {form action=$active_discussion->getEditUrl()}
    {include file=get_view_path('_discussion_form', 'discussions', $smarty.const.DISCUSSIONS_MODULE)}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>