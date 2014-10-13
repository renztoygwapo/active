<tr class="item_form expense_form edit_expense" id="{$_project_expense_form_id}">
  <td colspan="8" style="padding: 0 !important;">
    <form action="{$_project_expense_form_row_record->getEditUrl()}" method="post" class="expense_form">
      <div class="item_attributes">
        {if $can_track_for_others}
          <div class="item_attribute expense_user user">
            {select_project_user name='expense[user_id]' value=$expense_data.user_id project=$active_project user=$logged_user optional=false id="{$_project_expense_form_id}_user" required=true}
          </div>
        {else}
          <input type="hidden" name="expense[user_id]" value="{$expense_data.user_id}"/>
        {/if}

        <div class="item_attribute item_value_wrapper expense_category category">
          {select_expense_category name='expense[category_id]' value=$expense_data.category_id}
        </div>

        <div class="item_attribute expense_date date">
          {select_date name='expense[record_date]' value=$expense_data.record_date id="{$_project_expense_form_id}_date" required=true}
        </div>
      
        <div class="item_attribute item_value_wrapper expense_value value">
          {money_field name='expense[value]' value=$expense_data.value id="{$_project_expense_form_id}_value" required=true}
        </div>
        
        <div class="item_attribute item_summary_wrapper item_summary expense_summary summary">
          {text_field name='expense[summary]' value=$expense_data.summary id="{$_project_expense_form_id}_summary"}
        </div>
        
        <div class="item_attribute expense_billable billable">
          {select_billable_status name='expense[billable_status]' value=$expense_data.billable_status id="{$_project_expense_form_id}_billable"}
        </div>
      </div>
      
      <div class="item_form_buttons">
        {submit}Log Expense{/submit} {lang}or{/lang} <a href="#" class="item_form_cancel">{lang}Cancel{/lang}</a>
      </div>
    </form>
  </td>
</tr>