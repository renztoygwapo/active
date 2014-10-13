{use_widget name="my_time_homescreen_widget" module="tracking"}

<div id="add_time">
  {form action="#"}
    <div class="fields_wrapper">
      {wrap field=value}
        {text_field name='time_record[value]' value=$time_record_data.value class=short label='Hours' required=yes} {lang}of{/lang} {select_job_type name='time_record[job_type_id]' value=$time_record_data.job_type_id user=$selected_user required=true}
        <span class="details block">{lang}Possible formats: 3:30 or 3.5{/lang}</span>
      {/wrap}

      {wrap field=summary}
        {text_field name='time_record[summary]' value=$time_record_data.summary label="Summary"}
      {/wrap}

      {wrap field=week_day}
        {select_my_time_week_day name="time_record[record_date]" value=$time_record_data.record_date user=$logged_user label='Week Day'}
      {/wrap}

      {wrap field=project_id}
        {label}Project{/label}
        {select_project name="time_record[project_id]" value=$time_record_data.project_id optional=false user=$selected_user}
      {/wrap}

      {wrap field=task_id}
        {label}Task{/label}
        {select_task name='time_record[task_id]' value=$time_record_data.task_id user=$selected_user}
      {/wrap}

      {wrap field=billable_status}
        {select_billable_status name='time_record[billable_status]' value=$time_record_data.billable_status label='Is Billable?'}
      {/wrap}

      <input type="hidden" name="time_record[user_id]" value="{$time_record_data.user_id}" />
    </div>

    {wrap_buttons}
      {submit}Log Time{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#add_time').each(function() {
    var wrapper = $(this);
    var form = wrapper.find('form');

    var week_data = {$week_data|json nofilter};
    var day_record_date = {$day_record_date|json nofilter};
    var add_project_time_url = {$add_project_time_url|json nofilter};
    var add_task_time_url = {$add_task_time_url|json nofilter};

    var select_week_day = form.find('select[name="time_record[record_date]"]');
    var select_week_day_options = select_week_day.find('option');

    App.each(select_week_day_options, function(value, option) {
      switch(value) {
        case 0:
          $(option).val(week_data.week_day_1);
          break;
        case 1:
          $(option).val(week_data.week_day_2);
          break;
        case 2:
          $(option).val(week_data.week_day_3);
          break;
        case 3:
          $(option).val(week_data.week_day_4);
          break;
        case 4:
          $(option).val(week_data.week_day_5);
          break;
        case 5:
          $(option).val(week_data.week_day_6);
          break;
        case 6:
          $(option).val(week_data.week_day_7);
          break;
      } // switch
    });

    // Pre-select chosen day from weekly flyout
    if(day_record_date != null) {
      var selected_day = select_week_day.find('option[value="' + day_record_date + '"]');

//      selected_day.siblings().removeAttr('selected');
      selected_day.prop('selected', 'selected');

      select_week_day.prop('disabled', 'disabled');
      form.append('<input type="hidden" name="time_record[record_date]" value="' + day_record_date + '" />');
    } // if

    form.find('button[type=submit]').click(function() {
      var project_id = form.find('select[name="time_record[project_id]"]').val();
      var task_id = form.find('select[name="time_record[task_id]"]').val();

      if(typeof(project_id) == 'undefined') {
        App.Wireframe.Flash.error(App.lang('No project selected'));
        return false;
      } // if

      var log_time_url = '#';

      if(project_id) {
        if(task_id) {
          log_time_url = add_task_time_url.replace('--PROJECT_ID--', project_id).replace('--TASK_ID--', task_id);
        } else {
          log_time_url = add_project_time_url.replace('--PROJECT_ID--', project_id);
        };
      } // if

      form.attr('action', log_time_url);

      form.submit();

      return false;
    });
  });
</script>