{title}Time{/title}
{add_bread_crumb lang=false}{$active_day|date}{/add_bread_crumb}

<div id="timesheet_day" user_id="{$active_user->getId()}" day="{$active_day->toMySQL()}">
  <div id="timesheet_day_info">
    <div class="user_avatar">
      <img src="{$active_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt="" />
    </div>
    <div class="user_day_details">
      <div class="user">{user_link user=$active_user}</div>
      <div class="day">{lang}Time for{/lang}: {$active_day|date:0}</div>
    </div>
  </div>
  
  <div class="fields_wrapper">
	  {if $can_add}
	  <form action="{$active_project->tracking()->getAddTimeUrl()}" method="post" id="time_day_log_add_form">
	  {/if}
	  
	  <table id="timesheet_day_log" class="common" cellspacing=0>
	    <thead>
	      <tr>
	        <th>{lang}Added On{/lang}</th>
	        <th class="right">{lang}Hours{/lang}</th>
	        <th>{lang}Parent{/lang} / {lang}Summary{/lang}</th>
	        <th>{lang}Status{/lang}</th>
	        <th></th>
	      </tr>
	    </thead>
	    <tbody>
	    {if $can_add}
	      <tr id="time_day_log_add">
	        <td class="created_on">{lang}Log Time{/lang}:</td>
	        <td class="value right"><input type="text" name="time_record[value]"> {lang}of{/lang} {select_job_type name='time_record[job_type_id]' user=$logged_user}</td>
	        <td class="summary"><input type="text" name="time_record[summary]"></td>
	        <td class="status">
	          <select name="time_record[billable_status]">
	            <option value="0" {if empty($default_billable_status)}selected{/if}>{lang}Not Billable{/lang}</option>
	            <option value="1" {if $default_billable_status}selected{/if}>{lang}Billable{/lang}</option>
	          </select>
	        </td>
	        <td class="options"><button type="submit" class="default">{lang}Log{/lang}</button></td>
	      </tr>
	    {/if}
	    
	  {if is_foreachable($records)}
	    {foreach $records as $record}
	      <tr class="timesheet_day_log_entry">
	        <td class="created_on">{$record->getCreatedOn()|date:0}</td>
	        <td class="value right">{$record->getValue()}</td>
	        <td class="summary">
	        {if $record->getParent() instanceof ProjectObject}{object_link object=$record->getParent()} - {/if}{$record->getSummary()}
	        </td>
	        <td class="status">{$record->getBillableVerboseStatus()}</td>
	        <td class="options">
	        {if $record->state()->canTrash($logged_user)}
	          <a href="{$record->state()->getTrashUrl()}" title="{lang}Move to Trash{/lang}" class="trash"><img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" alt="" /></a>
	        {/if}
	        </td>
	      </tr>
	    {/foreach}
	  {/if}
	  
	      <tr id="timesheet_day_log_empty" style="display: none">
	        <td colspan="5">{lang}There is no time logged for this day{/lang}</td>
	      </tr>
	    </tbody>
	    <tfoot>
	      <tr>
	        <td class="value right" colspan="2"></td>
	        <td colspan="3"></td>
	      </tr>
	    </tfoot>
	  </table>
	  
	  {if $can_add}
	  </form>
	  {/if}
  </div>
</div>