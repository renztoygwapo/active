{title}Update Task Segment{/title}

<div id="update_task_segment" class="task_segment_form">
  {form action=$active_task_segment->getEditUrl()}
    {include file=get_view_path('_task_segment_form', 'task_segments', 'tasks')}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>