{if is_foreachable($unavailable_actions)}
	<table cellpadding="0" cellspacing="0" class="common list_items">
		<tr>
			<th>{lang}Action Name{/lang}</th>
			<th>{lang}Reason{/lang}</th>
		</tr>
  	{foreach from=$unavailable_actions key=key item=action}
  	  <tr class="{cycle values='odd,even'}">
			<td>{$action.action->getName()}</td>
			<td>{$action.reason}</td>
	  </tr>
  	{/foreach}
	</table>
{else}
	<p class="empty_page"><span class="inner">{lang}There are no unavailable actions{/lang}</span></p>
{/if}

