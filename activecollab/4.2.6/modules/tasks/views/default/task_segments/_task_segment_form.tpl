<div class="content_stack_wrapper">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Name{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=name}
        {text_field name='task_segment[name]' value=$task_segment_data.name class='task_segment_name_input' required=true}
      {/wrap}
    </div>
  </div>

  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Filters{/lang}</h3>
      <p class="aid">{lang}For milestone, label or category filters you can specify a list of comma separated names{/lang}</p>
    </div>
    <div class="content_stack_element_body" id="task_segment_filters">
      <div class="task_segment_filter">
        {select name='task_segment[milestone_filter]' label='Milestone'}
          <option value="{$smarty.const.TaskSegment::FILTER_ANY}" {if $task_segment_data.milestone_filter == $smarty.const.TaskSegment::FILTER_ANY}selected{/if}>{lang}Any{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS}" {if $task_segment_data.milestone_filter == $smarty.const.TaskSegment::FILTER_IS}selected{/if}>{lang}Selected{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS_NOT}" {if $task_segment_data.milestone_filter == $smarty.const.TaskSegment::FILTER_IS_NOT}selected{/if}>{lang}Not Selected{/lang}</option>
        {/select}

        <div class="task_segment_filter_specify">
          {text_field name='task_segment[milestone_names]' value=$task_segment_data.milestone_names}
        </div>
      </div>

      <div class="task_segment_filter">
        {select name='task_segment[label_filter]' label='Label'}
          <option value="{$smarty.const.TaskSegment::FILTER_ANY}" {if $task_segment_data.label_filter == $smarty.const.TaskSegment::FILTER_ANY}selected{/if}>{lang}Any{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS}" {if $task_segment_data.label_filter == $smarty.const.TaskSegment::FILTER_IS}selected{/if}>{lang}Selected{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS_NOT}" {if $task_segment_data.label_filter == $smarty.const.TaskSegment::FILTER_IS_NOT}selected{/if}>{lang}Not Selected{/lang}</option>
        {/select}

        <div class="task_segment_filter_specify">
          {text_field name='task_segment[label_names]' value=$task_segment_data.label_names}
        </div>
      </div>

      <div class="task_segment_filter">
        {select name='task_segment[category_filter]' label='Category'}
          <option value="{$smarty.const.TaskSegment::FILTER_ANY}" {if $task_segment_data.category_filter == $smarty.const.TaskSegment::FILTER_ANY}selected{/if}>{lang}Any{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS}" {if $task_segment_data.category_filter == $smarty.const.TaskSegment::FILTER_IS}selected{/if}>{lang}Selected{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS_NOT}" {if $task_segment_data.category_filter == $smarty.const.TaskSegment::FILTER_IS_NOT}selected{/if}>{lang}Not Selected{/lang}</option>
        {/select}

        <div class="task_segment_filter_specify">
          {text_field name='task_segment[category_names]' value=$task_segment_data.category_names}
        </div>
      </div>

      <div class="task_segment_filter">
        {select name='task_segment[priority_filter]' label='Priority'}
          <option value="{$smarty.const.TaskSegment::FILTER_ANY}" {if $task_segment_data.priority_filter == $smarty.const.TaskSegment::FILTER_ANY}selected{/if}>{lang}Any{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS}" {if $task_segment_data.priority_filter == $smarty.const.TaskSegment::FILTER_IS}selected{/if}>{lang}Selected{/lang}</option>
          <option value="{$smarty.const.TaskSegment::FILTER_IS_NOT}" {if $task_segment_data.priority_filter == $smarty.const.TaskSegment::FILTER_IS_NOT}selected{/if}>{lang}Not Selected{/lang}</option>
        {/select}

        <div class="task_segment_filter_specify">
          {checkbox name="task_segment[priority_lowest]" checked=$task_segment_data.priority_lowest label='Lowest'}
          {checkbox name="task_segment[priority_low]" checked=$task_segment_data.priority_low label='Low'}
          {checkbox name="task_segment[priority_normal]" checked=$task_segment_data.priority_normal label='Normal'}
          {checkbox name="task_segment[priority_high]" checked=$task_segment_data.priority_high label='High'}
          {checkbox name="task_segment[priority_highest]" checked=$task_segment_data.priority_highest label='Highest'}
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#task_segment_filters').each(function() {
    var wrapper = $(this);

    wrapper.find('div.task_segment_filter').each(function() {
      var segment_filter = $(this);

      if(segment_filter.find('select').val() == 'any') {
        segment_filter.find('div.task_segment_filter_specify').hide();
      } // if
    });

    wrapper.find('div.task_segment_filter select').change(function() {
      var select = $(this);
      var segment_filter = select.parent();

      if(select.val() == 'any') {
        segment_filter.find('div.task_segment_filter_specify').hide();
      } else {
        segment_filter.find('div.task_segment_filter_specify').show().find('input[type=text]').focus();
      } // if
    });
  });
</script>