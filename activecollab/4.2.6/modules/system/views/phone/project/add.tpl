{title}New Project{/title}
{add_bread_crumb}New Project{/add_bread_crumb}

<div id="new_project">
  {form action=Router::assemble('projects_add')}
    {include file=get_view_path('_project_form', 'project', $smarty.const.SYSTEM_MODULE)}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('project_created', 'mobile')}">
  {/if}
    
    {wrap_buttons}
      {submit}Create Project{/submit}
    {/wrap_buttons}
  {/form}
</div>