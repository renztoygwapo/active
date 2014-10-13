{title}Date and Time Settings{/title}
{add_bread_crumb}Date and Time Settings{/add_bread_crumb}

<div id="date_time_settings">
  {form action=Router::assemble('date_time_settings') method=post}
    <div class="content_stack_wrapper">
    
      <!-- Time Zone -->
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Time Zone{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=time_timezone}
            {label for=timeTimezone}Time Zone{/label}
            {select_timezone name="date_time[time_timezone]" value=$date_time_data.time_timezone optional=true id=timeTimezone}
          {/wrap}
          
          {wrap field=time_dst}
            {yes_no name="date_time[time_dst]" value=$date_time_data.time_dst label='Daylight Saving Time'}
          {/wrap}
        </div>
      </div>
      
      <!--  Formatting -->
      <div class="content_stack_element last">
        <div class="content_stack_element_info">
          <h3>{lang}Formatting{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=format_date}
            {select_date_format name="date_time[format_date]" value=$date_time_data.format_date optional=false label='Date Format'}
          {/wrap}
          
          {wrap field=format_time}
            {select_time_format name="date_time[format_time]" value=$date_time_data.format_time optional=false label='Time Format'}
          {/wrap}
        </div>
      </div>
    </div>
    
    {wrap_buttons}
  	  {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>