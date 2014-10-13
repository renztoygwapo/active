{title}New Milestone{/title}
{add_bread_crumb}New Milestone{/add_bread_crumb}

<div id="add_milestone">
  {form action=$add_milestone_url}
    {include file=get_view_path('_milestone_form', 'milestones', $smarty.const.SYSTEM_MODULE)}
    
    {wrap_buttons}
      {submit}Add Milestone{/submit}
    {/wrap_buttons}
  {/form}
</div>