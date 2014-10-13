{title}List emails{/title}
{add_bread_crumb}List emails{/add_bread_crumb}

{if !is_error($connection)}
{form method="post" action=$active_mailbox->getDeleteMessagesUrl()}
  
  <div class="section_container">
  {if is_foreachable($headers)}
    <table class="common">
      <tr>
        	<td colspan="5" align="right"><h2 class="section_name"><span class="section_name_span">{lang unread=$unread_emails total=$total_emails}Emails in "{$active_mailbox->getName()} - {$active_mailbox->getEmail()}" mailbox (:unread unread of :total total){/lang}</span></h2></td>
      </tr>
      <tr>
        <th>{lang}UID{/lang}</th>
        <th>{lang}From{/lang}</th>
        <th>{lang}Subject{/lang}</th>
        <th>{lang}Date{/lang}</th>
        <th>{checkbox class="check_all_items" name="all_chx"}</th>
      </tr>
    {foreach from=$headers item=header}
      <tr>
        <td>{$header->uid}</td>
        <td>{$header->from}</td>
        <td>{$header->subject}</td>
        <td>{$header->date}</td>
        <td>{checkbox name="delete_emails[]" value=$header->uid class="action_chx"}</td>
      </tr>
    {/foreach}
    	<tr>
        	<td colspan="5" align="right">{button confirm="Are you sure that you want to delete selected emails?" type="submit" style="float:right;"}Delete{/button}</td>
        </tr>
    </table>
    
  
  {else}
    <p>{lang}No emails in mailbox{/lang}</p>
  {/if}
  </div>
{/form}
<script type="text/javascript">
	var check_all_items = $('.check_all_items');
	check_all_items.change(function(){
		var action_chx = $('.action_chx');
		if($(this).attr('checked')) {
			action_chx.attr('checked','checked');
		} else {
			action_chx.removeAttr('checked');
		}
	});
</script>
{/if}
