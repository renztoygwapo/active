{title}All Quotes{/title}
{add_bread_crumb}All Quotes{/add_bread_crumb}

<div id="quotes">
  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  	{if is_foreachable($formatted_quotes)}
	  	{foreach $formatted_quotes as $status => $quotes}
		  	{if is_foreachable($quotes)}
		  		{assign_var name=list_divider}
				    {if $status == $smarty.const.QUOTE_STATUS_DRAFT}
				    	{lang}Draft{/lang}
				    {elseif $status == $smarty.const.QUOTE_STATUS_SENT}
				      {lang}Sent{/lang}
				    {elseif $status == $smarty.const.QUOTE_STATUS_WON}
				      {lang}Won{/lang}
				    {elseif $status == $smarty.const.QUOTE_STATUS_LOST}
				      {lang}Lost{/lang}
				    {/if}
				  {/assign_var}
				  
		  		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-{$list_divider|lower|trim}-icon.png" module=$smarty.const.INVOICING_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{$list_divider}</li>
			  	{foreach $quotes as $quote}
	  				<li><a href="{$quote.permalink}">{$quote.name}</a></li>
			  	{/foreach}
		  	{/if}
	  	{/foreach}
	  {else}
	  	<li>{lang}There are no Quotes{/lang}</li>
	  {/if}
  </ul>
</div>