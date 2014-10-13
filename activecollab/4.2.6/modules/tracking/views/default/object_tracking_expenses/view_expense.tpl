{title}Expense{/title}
{add_bread_crumb}Expense Details{/add_bread_crumb}

{use_widget name='properties_list' module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="tracking_object_detials">
  <div id="tracking_object_detials_inner_wrapper">
    <dl class="properties_list">
      <dt>{lang}Expense{/lang}</dt>
      <dd>{$active_expense->getValue()|money:$active_project->getCurrency():null:true:true}</dd>

      <dt>{lang}Status{/lang}</dt>
      <dd>{$active_expense->getBillableVerboseStatus()}</dd>

      {if $expense_category instanceof ExpenseCategory}
        <dt>{lang}Category{/lang}</dt>
        <dd>{$expense_category->getName()}</dd>
      {/if}

      <dt>{lang}Date{/lang}</dt>
      <dd>{$active_expense->getRecordDate()|date:0}</dd>

      <dt>{lang}User{/lang}</dt>
      <dd>{user_link user=$active_expense->getUser()}</dd>
    </dl>
  </div>
</div>