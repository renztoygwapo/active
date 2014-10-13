
{if is_foreachable($_activity_logs)}
	<h2>{lang}Recent Activities{/lang}</h2>
	{foreach from=$_activity_logs key=date item=activities name=print_object}
		<table class="discussion_table common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="5">{$date}</th>
            </tr>
          </thead>
          <tbody>
		  {foreach from=$activities item=object}	
		  <tr>
		  	<td class="icon" align="left">
  		  	</td>
		 
		    <td class="name">{$object|json nofilter}</td>
		  </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}

{/if}

<div class="activity_logs" id="{$_activity_logs_id}">


{foreach $_activity_logs as $date => $activities}
	
  {if is_foreachable($activities)}
	<div class="activity_logs_day_group">
     <div class="activity_log_day">
      <div class="date_stamp">
        <span class="date_stamp_month">{$date|date_format:"%b"}</span>
        <span class="date_stamp_month_day">{$date|date_format:"%e"}</span>
      </div>
     </div>
	   <div class="activity_log_day_logs">
      <table cellspacing="0">
        {foreach from=$activities item=activity name='day_activity_logs_loop'}
        
        <tr>
          <td class="activity_icon"></td>
          <td class="activity_content">
            
        </tr>
        {/foreach}
      </table>
     </div>
  </div>
  {/if}
{/foreach}
</div>