{title}Workweek Settings{/title}
{add_bread_crumb}Workweek Settings{/add_bread_crumb}
{use_widget name="days_off" module="globalization"}

<div id="workweek_settings">
  {form action=Router::assemble('workweek_settings') method=post}
    <div class="content_stack_wrapper">
    
      <!-- Work days -->
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Workdays{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=first_week_day}
            {select_week_day name="workweek[time_first_week_day]" value=$workweek_data.time_first_week_day label='First Day in a Week'}
          {/wrap}
          
          {wrap field=workdays}
            {label required=yes}Workdays{/label}
            {select_week_days name="workweek[time_workdays]" value=$workweek_data.time_workdays required=yes}
          {/wrap}
        </div>
      </div>
      
      <!-- Days Off -->
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Days Off{/lang}</h3>
        </div>
        <div id="days_off_wrapper" class="content_stack_element_body">
          {wrap field=days_off}
            <table class="form form_field" id="workweek_days_off" style="{if !is_foreachable($workweek_data.days_off)}display: none{/if}">
              <tr>
                <th class="name">{label required=yes}Event Name{/label}</th>
                <th class="date">{label required=yes}Date{/label}</th>
                <th class="yearly center">{label}Repeat Yearly?{/label}</th>
                <th></th>
              </tr>
            {if is_foreachable($workweek_data.days_off)}
              {foreach $workweek_data.days_off as $day_off_key => $day_off}
                <tr class="day_off_row {cycle values='odd,even'}">
                  <td class="name">{text_field name="workweek[days_off][$day_off_key][name]" value=$day_off.name}</td>
                  <td class="date">{select_date name="workweek[days_off][$day_off_key][event_date]" value=$day_off.date}</td>
                  <td class="yearly center"><input name="workweek[days_off][{$day_off_key}][repeat_yearly]" type="checkbox" value="1" class="inline" {if $day_off.repeat_yearly}checked="checked"{/if} /></td>
                  <td class="options right"><a href="#" title="{lang}Remove Day Off{/lang}" class="remove_day_off"><img src='{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}' alt='' /></a></td>
                </tr>
              {/foreach}
            {/if}
            </table>
            <p id="no_days_off_message" style="{if is_foreachable($workweek_data.days_off)}display: none{/if}">{lang}There are no days off defined{/lang}</p>
            <p><a href="#" class="button_add">{lang}New Day Off{/lang}</a></p>
          {/wrap}
        </div>
      </div>
      
      <div class="content_stack_element last">
		    <div class="content_stack_element_info">
		      <h3>{lang}Effective Work Hours{/lang}</h3>
		    </div>
		    <div class="content_stack_element_body">
		      {wrap field=workweek_effective_work_hours}
		        {label for=workweekEffectiveWorkHours}Number of Effective Work Hours per Week{/label}
		        {text_field name="workweek[effective_work_hours]" value=$workweek_data.effective_work_hours class=short id=workweekEffectiveWorkHours}
		      {/wrap}
		    </div>
		  </div>
    </div>
    
    {wrap_buttons}
  	  {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  App.widgets.daysOff.init('days_off_wrapper');
</script>