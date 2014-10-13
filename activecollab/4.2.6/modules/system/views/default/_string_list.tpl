<div id="{$_string_list_id}" string_list_name="{$_string_list_name}" class="string_list">
  <table>
{if is_foreachable($_string_list_value)}
  {counter start=0 name=string_list_num assign=_string_list_num}
  
  {foreach from=$_string_list_value item=_string_list_item}
    <tr class="{cycle values='odd,even'} item">
      <td class="num">#{counter name=string_list_num}{$_string_list_num}</td>
      <td class="value">
        <span>{$_string_list_item}</span>
        <input type="hidden" name="{$_string_list_name}[]" value="{$_string_list_item}" />
      </td>
      <td class="remove"><a href="javascript: return false;"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a></td>
    </tr>
  {/foreach}
{else}
    <tr class="odd empty">
      <td colspan="2">{lang}List is Empty{/lang}</td>
    </tr>
{/if}
  </table>
  
  <div class="add_list_item">
  	{link href="#" title=$_string_list_link_title class="button_add add_list_item_button"}{$_string_list_link_title}{/link}
  </div>
</div>

<script type="text/javascript">
  $('#{$_string_list_id}').stringList();
</script>