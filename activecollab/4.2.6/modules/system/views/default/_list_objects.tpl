{if is_foreachable($_list_objects_objects)}
<table class="common" id="{$_list_objects_id}" cellspacing="0">
{if $_list_objects_show_header && !$_list_objects_short}
  <thead>
    <tr>
    {if $_list_objects_show_star}
      <th class="star"></th>
    {/if}
    {if $_list_objects_show_priority}
      <th class="priority"></th>
    {/if}
      <th class="name">{lang}Object{/lang}</th>
    {if $_list_objects_show_checkboxes}
      <th class="checkbox"><input type="checkbox" class="auto master_checkbox input_checkbox" /></th>
    {/if}
    </tr>
  </thead>
{/if}
  <tbody>
{foreach from=$_list_objects_objects item=_list_objects_object}
    <tr class="{cycle values='odd,even'}">
    {if $_list_objects_show_star}
      <td class="star">{favorite_object object=$_list_objects_object user=$logged_user}</td>
    {/if}
    {if $_list_objects_show_priority}
      <td class="priority">{object_priority object=$_list_objects_object user=$logged_user}</td>
    {/if}
      <td class="name">
        {$_list_objects_object->getVerboseType()}: {object_link object=$_list_objects_object del_completed=$_list_objects_del_completed excerpt=$_list_objects_name_length}
        {if !$_list_objects_short}
          <span class="details block">{action_on_by user=$_list_objects_object->getCreatedBy() datetime=$_list_objects_object->getCreatedOn()}{if $_list_objects_show_project} {lang}in{/lang} {project_link project=$_list_objects_object->getProject()}{/if}{if $_list_objects_object->can_be_completed && $_list_objects_object->complete()->isOpen() && $_list_objects_object->getDueOn()} | {due_on object=$_list_objects_object}{/if}</span>
        {/if}
      </td>
    {if $_list_objects_show_checkboxes}
      <td class="checkbox"><input type="checkbox" name="objects[]" value="{$_list_objects_object->getId()}" class="auto slave_checkbox input_checkbox" /></td>
    {/if}
    </tr>
{/foreach}
  </tbody>
</table>
{if $_list_objects_show_checkboxes}
<script type="text/javascript">
  $(document).ready(function() {ldelim}
    $('#{$_list_objects_id}').checkboxes();
  {rdelim});
</script>
{/if}
{/if}