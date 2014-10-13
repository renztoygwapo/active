{title}All Invoices{/title}
{add_bread_crumb}All Invoices{/add_bread_crumb}

<div id="invoices">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  	{if is_foreachable($formatted_invoices)}
	  	{foreach $formatted_invoices as $status => $invoices}
		  	{if is_foreachable($invoices)}
		  		{assign_var name=list_divider}
				    {if $status == $smarty.const.INVOICE_STATUS_DRAFT}
				    	{lang}Draft{/lang}
				    {elseif $status == $smarty.const.INVOICE_STATUS_ISSUED}
				      {lang}Issued{/lang}
				    {elseif $status == $smarty.const.INVOICE_STATUS_PAID}
				      {lang}Paid{/lang}
				    {elseif $status == $smarty.const.INVOICE_STATUS_CANCELED}
				      {lang}Canceled{/lang}
				    {/if}
				  {/assign_var}
				  
		  		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-{$list_divider|lower|trim}-icon.png" module=$smarty.const.INVOICING_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{$list_divider}</li>
			  	{foreach $invoices as $invoice}
	  				<li><a href="{$invoice.permalink}">{$invoice.name}</a></li>
			  	{/foreach}
		  	{/if}
	  	{/foreach}
	  {else}
	  	<li>{lang}There are no Invoices{/lang}</li>
	  {/if}
  </ul>
</div>