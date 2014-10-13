{if $subtask instanceof Subtask && $subtask->isLoaded()}
<tr class="edit_subtask">
{else}
<tr class="new_subtask" style="display: none">
{/if}
  <td class="task_reorder"></td>
  <td class="task_meta"></td>
  <td colspan="2" class="task_content">
  {if $subtask instanceof Subtask && $subtask->isLoaded()}
    <form action="{$subtask->getEditUrl()}" method="post" class="subtask_form">
  {else}
    <form action="{$subtask_parent->subtasks()->getAddUrl()}" method="post" class="subtask_form">
  {/if}
      <div class="subtask_summary">
        {text_field name='subtask[body]' value=$subtask_data.body class='long' id="{$subtasks_id}_summary_field"}
      </div>
      
      <div class="subtask_attributes">
        <div class="subtask_attribute subtask_assignee">
          {label for="($subtasks_id)_select_assignee"}Assignee{/label} {select_assignee name='subtask[assignee_id]' value=$subtask_data.assignee_id parent=$subtask user=$user id="{$subtasks_id}_select_assignee"}
        </div>
        
      {if $subtask->usePriority()}
        <div class="subtask_attribute subtask_priority">
          {label for="($subtasks_id)_task_priority"}Priority{/label} {select_priority name='subtask[priority]' value=$subtask_data.priority id="{$subtasks_id}_task_priority"}
        </div>
      {/if}
        
      {if $subtask->useLabels()}
        <div class="subtask_attribute subtask_label">
          {label for="($subtasks_id)_label"}Label{/label} {select_label name='subtask[label_id]' value=$subtask_data.label_id type=get_class($subtask->label()->newLabel()) id="{$subtasks_id}_label" user=$logged_user can_create_new=false}
        </div>
      {/if}
        
        <div class="subtask_attribute subtask_due_on">
          {label for="($subtasks_id)_due_on"}Due On{/label} {select_due_on name='subtask[due_on]' value=$subtask_data.due_on id="{$subtasks_id}_due_on"}
        </div>
      </div>
      
      <input type="hidden" name="submitted" value="submitted" />
      
      <div class="subtask_buttons_wrapper">
      {if $subtask instanceof Subtask && $subtask->isLoaded()}
        {submit}Save Changes{/submit} {lang}or{/lang} <a href="#" class="subtask_cancel">{lang}Close{/lang}</a>
      {else}
        {submit}Add Subtask{/submit} {lang}or{/lang} <a href="#" class="subtask_cancel">{lang}Close{/lang}</a>
      {/if}
      </div>
    </form>
  </td>
</tr>