{if $_project_expense_form_row_record instanceof Expense && $_project_expense_form_row_record->isLoaded()}
<tr class="item_form expense_form edit_expense" id="{$_project_expense_form_id}">
{else}
<tr class="item_form expense_form new_expense" id="{$_project_expense_form_id}">
{/if}
  <td colspan="5">
    {if $_project_expense_form_row_record instanceof Expense && $_project_expense_form_row_record->isLoaded()}
    <form action="{$_project_expense_form_row_record->getEditUrl()}" method="post" class="expense_form">
    {else}
    <form action="{assemble route=project_tracking_expense_add project_slug=$active_project->getSlug()}" method="post" class="expense_form">
    {/if}
    
      <div class="item_attributes">
        {if $can_track_for_others}
        <div class="item_attribute expense_user">
          {label for="($_project_expense_form_id)_user" required=yes}User{/label} {select_project_user name='expense[user_id]' value=$expense_data.user_id project=$active_project user=$logged_user optional=false id="{$_project_expense_form_id}_user" required=true}
        </div>
        {else}
        <input type="hidden" name="expense[user_id]" value="{$expense_data.user_id}"/>
        {/if}
      
        <div class="item_attribute item_value_wrapper expense_value">
          {label for="($_project_expense_form_id)_value" required=yes}Amount{/label} {money_field name='expense[value]' value=$expense_data.value id="{$_project_expense_form_id}_value" required=true} {lang}in{/lang} {select_expense_category name='expense[category_id]' value=$expense_data.category_id}
        </div>
        
        <div class="item_attribute expense_date">
          {label for="($_project_expense_form_id)_date" required=yes}Date{/label} {select_date name='expense[record_date]' value=$expense_data.record_date id="{$_project_expense_form_id}_date" required=true}
        </div>
        
        <div class="item_attribute item_summary_wrapper item_summary expense_summary">
          {label for="($_project_expense_form_id)_summary"}Summary{/label} {text_field name='expense[summary]' value=$expense_data.summary id="{$_project_expense_form_id}_summary"}
        </div>
        
        <div class="item_attribute expense_billable">
          {label for="($_project_expense_form_id)_billable"}Billable?{/label} {select_billable_status name='expense[billable_status]' value=$expense_data.billable_status id="{$_project_expense_form_id}_billable"}
        </div>
      </div>
      
      <div class="item_form_buttons">
        {submit}Log Expense{/submit} {lang}or{/lang} <a href="#" class="item_form_cancel">{lang}Cancel{/lang}</a>
      </div>
    </form>
  </td>
</tr>