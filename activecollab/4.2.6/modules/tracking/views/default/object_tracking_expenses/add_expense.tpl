{title}Log Expense{/title}

<div id="add_expense">
  {form action=$active_tracking_object->tracking()->getAddExpenseUrl() method=post}
    <div class="fields_wrapper">
	    {wrap field=value}
	    	{money_field name='expense[value]' value=$expense_data.value label='Amount' required=true} {lang}in{/lang} {select_expense_category name='expense[category_id]' value=$expense_data.category_id}
	    {/wrap}
	    
	    {wrap field=summary}
	      {text_field name='expense[summary]' value=$expense_data.summary label='Summary'}
	    {/wrap}
	  
	    {wrap field=record_date}
	    	{select_date name='expense[record_date]' value=$expense_data.record_date label='Date' required=true}
	    {/wrap}

      {if $can_track_for_others}
        {wrap field=user_id}
          {select_project_user name='expense[user_id]' value=$expense_data.user_id project=$active_project user=$logged_user optional=false required=true label='User'}
        {/wrap}
      {else}
        <input type="hidden" name="expense[user_id]" value="{$expense_data.user_id}"/>
      {/if}
	    
	    {wrap field=billable_status}
	      {select_billable_status name='expense[billable_status]' value=$expense_data.billable_status label='Is Billable?'}
	    {/wrap}
    </div>
    
    {wrap_buttons}
    	{submit}Log Expense{/submit}
    {/wrap_buttons}
  {/form}
</div>