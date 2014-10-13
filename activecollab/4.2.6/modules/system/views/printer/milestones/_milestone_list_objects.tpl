{if is_foreachable($_objects)}
  <h2>{$_title}</h2>
  <table id="open_milestone_tasks" class="common" cellspacing="0">
    <tr>
      <th class="star"></th>
      <th class="name">{lang}Task Name{/lang}</th>
      {if $_type == 'discussions'}
      	<th class="date">{lang}Last Commented{/lang}</th>
      {/if}
      {if $_type == 'todo'}
      	<th class="priority">{lang}Priority{/lang}</th>
      {/if}
      <th class="user">{lang}Posted By{/lang}</th>
      <th class="date">{lang}Posted On{/lang}</th>
    </tr>
  {foreach from=$_objects item=obj}
    <tr>
      <td class="star">{favorite_object object=$obj user=$logged_user}</td>
      <td class="name">{object_link object=$obj}</td>
      {if $_type == 'discussions'}
      	<td class="date commented_on">{if $obj->getLastCommentOn() instanceof DateTimeValue}{$obj->getLastCommentOn()|date:0}{else}--{/if}</td>
      {/if}
      {if $_type == 'todo'}
      	<td class="priority">{object_priority object=$obj user=$logged_user show_normal=true}</td>
      {/if}
      <td class="user created_by">{user_link user=$obj->getCreatedBy()}</td>
      <td class="date created_on">{$obj->getCreatedOn()|date:0}</td>
    </tr>
  {/foreach}
  </table>
{/if}