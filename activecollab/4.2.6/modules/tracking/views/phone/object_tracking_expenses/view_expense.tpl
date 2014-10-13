{title lang=false}{$active_expense->getName()}{/title}
{add_bread_crumb}View Expense{/add_bread_crumb}

{object object=$active_expense user=$logged_user}
	<div class="object_tracking">
	  <div class="preview">{$active_expense->getName()}</div>
	</div>
{/object}