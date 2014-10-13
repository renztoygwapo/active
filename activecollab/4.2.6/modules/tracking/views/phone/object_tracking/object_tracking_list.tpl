{title}All Time and Expenses{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="project_time_expenses">
	{if $formatted_items}
	  {foreach $formatted_items as $record_date => $items}
	  	{if is_foreachable($items)}
	  		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	  		<li data-role="list-divider">{$record_date|date}</li>
		  	{foreach $items as $item}
			    {if $item.class == 'TimeRecord'}
			    	<li>
			    		<a href="{$item.urls.view}"><img class="ui-li-icon" src="{image_url name='icons/32x32/time-entry.png' module=$smarty.const.TRACKING_MODULE}" alt="Time Record"><h3>{lang time_record_value=$item.value user_name=$item.user_name}:time_record_value h, :user_name{/lang}</h3><p>{$item.summary}</p></a>
			    	</li>
			    {else}
			    	<li>
			    		<a href="{$item.urls.view}"><img class="ui-li-icon" src="{image_url name='icons/32x32/expense.png' module=$smarty.const.TRACKING_MODULE}" alt="Expense"><h3>{lang expense_value=$item.value user_name=$item.user_name}$ :expense_value, :user_name{/lang}</h3><p>{$item.summary}</p></a>
			    	</li>
			    {/if}
		  	{/foreach}
		  	</ul>
	  	{/if}
  	{/foreach}
  {else}
  	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li>{lang}There are no time nor expenses logged for this task{/lang}</li>
		</ul>
	{/if}
</div>