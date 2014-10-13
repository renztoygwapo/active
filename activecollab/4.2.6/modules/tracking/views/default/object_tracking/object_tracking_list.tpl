<div class="object_time_and_expenses" id="object_time_and_expenses_for_{$active_tracking_object->getId()}" add_time_url="{$active_tracking_object->tracking()->getAddTimeUrl()}" add_expense_url="{$active_tracking_object->tracking()->getAddExpenseUrl()}" time_icon_url="{image_url name='icons/16x16/time-record.png' module=$smarty.const.TRACKING_MODULE}" expense_icon_url="{image_url name='icons/16x16/expense.png' module=$smarty.const.TRACKING_MODULE}">
  <div class="object_inspector_wrapper">
    
    <ul class="object_tracking_totals">
    
      <li class="object_tracking_totals_estimated_time">
        <span class="label">{lang}Estimated Time{/lang}</span>
        <span class="value">{object_estimate object=$active_tracking_object user=$logged_user}</span>
      </li>
      
      <li class="object_tracking_totals_tracked_time">
        <span class="label">{lang}Logged Time{/lang}</span>
        <span class="value object_total_time"></span>      
      </li>
      
      <li class="object_tracking_totals_tracked_expenses">
        <span class="label">{lang}Logged Expenses{/lang}</span>
        <span class="value object_total_expenses"></span>      
      </li>
      
    </ul>

  </div>
{if $can_add}
  <form action="{$active_tracking_object->tracking()->getAddTimeUrl()}" method="post">
{/if}
    <div class="table_wrapper">
    <table class="common" cellspacing="0">
      <thead>
        <tr>
          <th class="type">{lang}Type{/lang}</th>
          <th class="user">{lang}User{/lang}</th>
          <th class="job_type">{lang}Job{/lang} / {lang}Categ.{/lang}</th>
          <th class="record_date">{lang}Day{/lang}</th>
          <th class="value right">{lang}Value{/lang}</th>
          <th class="summary">{lang}Summary{/lang}</th>
          <th class="status">{lang}Status{/lang}</th>
          <th class="options"></th>
        </tr>
      </thead>
      <tbody>
      {if $can_add}
        <tr class="add_time_or_expense highlighted">
          <td class="type">
            <span class="tracking_type_toggler"></span>
          </td>
          <td class="user">
            {if $can_track_for_others}
              {select_project_user name='item[user_id]' value=$logged_user->getId() project=$active_tracking_object->getProject() user=$logged_user optional=false short=true}
            {else}
              {user_link user=$logged_user short=true}
              <input type="hidden" name="item[user_id]" value="{$logged_user->getId()}" />
            {/if}
          </td>
          <td class="job_type">
            {if $active_tracking_object->tracking()->getEstimate() instanceof Estimate}
              {assign var="active_tracking_object_job_type_id" value=$active_tracking_object->tracking()->getEstimate()->getJobTypeId()}
            {/if}
            {select_job_type name='item[job_type_id]' user=$logged_user class="timerecord_control" value=$active_tracking_object_job_type_id}
            {select_expense_category name='item[category_id]' class="expense_control"}
          </td>
          <td class="record_date">{select_date name='item[record_date]' value=DateTimeValue::now()->getForUser($logged_user)}</td>
          <td class="value right"><input type="text" name="item[value]" /></td>
          <td class="summary"><input type="text" name="item[summary]"></td>
          <td class="status">
            {select_billable_status name='item[billable_status]' value=$active_tracking_object->tracking()->getDefaultBillableStatus()}
          </td>
          <td class="options"><button type="submit">{lang}Add{/lang}</button></td>
        </tr>
      {/if}
      
    {if $items}
      {assign var='logged_user_langauge' value=$logged_user->getLanguage()}

      {foreach $items as $item}
        <tr class="item {if $item.class == 'TimeRecord'}time_record{else}expense{/if}" record_value="{$item.value}">
        {if $item.class == 'TimeRecord'}
          <td class="type"><img src="{image_url name='icons/16x16/time-record.png' module=$smarty.const.TRACKING_MODULE}" title="{lang}Time Record{/lang}" /></td>
        {else}
          <td class="type"><img src="{image_url name='icons/16x16/expense.png' module=$smarty.const.TRACKING_MODULE}" title="{lang}Expense{/lang}" /></td>
        {/if}
          <td class="user"><a class="user_link {if $item.user.id == 0 || !$item.user.id}anonymous_user_link{else}quick_view_item{/if}" href="{$item.user.permalink}">{$item.user.short_display_name}</a></td>
          <td class="job_type">
          {if $item.class == 'TimeRecord'}
            {$item.job_type_name}
          {else}
          	{$item.category_name}
          {/if}
          </td>
          <td class="record_date">{$item.record_date|date:0}</td>
          <td class="value right">
          {if $item.class == 'TimeRecord'}
            {$item.value|hours}h
          {else}
            {$item.value|money:$item.currency:$logged_user_language}
          {/if}
          </td>
          <td class="summary">{$item.summary}</td>
          <td class="status">
            {if $item.billable_status == $smarty.const.BILLABLE_STATUS_NOT_BILLABLE}
              {lang}Not Billable{/lang}
            {elseif $item.billable_status == $smarty.const.BILLABLE_STATUS_BILLABLE}
              {lang}Billable{/lang}
            {elseif $item.billable_status == $smarty.const.BILLABLE_STATUS_PENDING_PAYMENT}
              {lang}Pending Payment{/lang}
            {elseif $item.billable_status == $smarty.const.BILLABLE_STATUS_PAID}
              {lang}Paid{/lang}
            {else}
              {lang}Unknown{/lang}
            {/if}
          </td>
          <td class="options">
          {if $item.permissions.can_edit}
            <a href="{$item.urls.edit}" title="{lang}Edit{/lang}" class="edit"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>
          {/if}

          {if $item.permissions.can_trash}
            <a href="{$item.urls.trash}" title="{lang}Move to Trash{/lang}" class="trash"><img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" alt="" /></a>
          {/if}
          </td>
        </tr>
      {/foreach}
    {/if}
        <tr class="empty" style="display: none">
          <td colspan="7">{lang type=$active_tracking_object->getVerboseType(true, $logged_user->getLanguage())}There is no time logged for this :type{/lang}</td>
        </tr>
      </tbody>
    </table>
    </div>
{if $can_add}
  </form>
{/if}
</div>