{title}New Task Segment{/title}

<div id="new_task_segment" class="task_segment_form">
  {form action=Router::assemble('task_segments_add')}
    {include file=get_view_path('_task_segment_form', 'task_segments', 'tasks')}
    
    {wrap_buttons}
      {submit}Create Task Segment{/submit}
    {/wrap_buttons}
  {/form}
</div>