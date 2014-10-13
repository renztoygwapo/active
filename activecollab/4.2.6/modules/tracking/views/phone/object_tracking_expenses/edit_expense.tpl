{title}Edit Expense{/title}
{add_bread_crumb}Edit Expense{/add_bread_crumb}

<div id="edit_expense" data-rel="dialog">
  {form action=$active_expense->getEditUrl()}
  	{wrap field=user_id}
    	{select_project_user name='expense[user_id]' value=$expense_data.user_id label='User' project=$active_project user=$logged_user optional=false id=expenseUserId required=true}
    {/wrap}
    
    {wrap field=value}
    	{text_field name='expense[value]' value=$expense_data.value id=expenseValue label='Amount' required=true}
    {/wrap}
    
    {wrap field=category}
    	{select_expense_category name='expense[category_id]' value=$expense_data.category_id id=expenseCategory label='Category'}
    {/wrap}
    
    {wrap field=record_date}
    	{select_date name='expense[record_date]' value=$expense_data.record_date id=expenseRecordDate label='Date' required=true}
    {/wrap}
    
    {wrap field=summary}
      {text_field name='expense[summary]' value=$expense_data.summary label='Summary' id=expenseSummary}
    {/wrap}
    
    {wrap field=billable_status}
      {select_billable_status name='expense[billable_status]' value=$expense_data.billable_status label='Is Billable?' id=expenseIsBillable}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>