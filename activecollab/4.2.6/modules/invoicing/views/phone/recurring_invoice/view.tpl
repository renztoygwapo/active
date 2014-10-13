{title lang=false}{lang recurring_profile_name=$active_recurring_profile->getName()}:recurring_profile_name{/lang}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_recurring_profile user=$logged_user show_inspector=false}
	<div class="invoice">
	  <div class="invoice_content">
	    <div class="invoice_data">
	    	<ul class="properties">
	        <li class="property">
	          <span class="property_title"></span>
	          <span class="property_data invoice_name">{$active_recurring_profile->getName()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Created On{/lang}</span>
	          <span class="property_data">{$active_recurring_profile->getCreatedOn()|date}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Starts On{/lang}</span>
	          <span class="property_data">{$active_recurring_profile->getStartOn()|date}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Frequency{/lang}</span>
	          <span class="property_data">{$active_recurring_profile->getFrequency()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Occurrence{/lang}</span>
	          <span class="property_data">{$active_recurring_profile->getOccurrences()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Auto Issue{/lang}</span>
	          <span class="property_data">{if $active_recurring_profile->getAutoIssue()}{lang}Yes{/lang}{else}{lang}No{/lang}{/if}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Payments{/lang}</span>
	          <span class="property_data">{$active_recurring_profile->getAllowPaymentsText()}</span>
	        </li>
	        
	        <li class="property company_name">
	          <span class="property_title">Company</span>
	          <span class="property_data">{company_link company=$active_recurring_profile->getCompany()}</span>
	        </li>
	        
	        <li class="property company_address">
	          <span class="property_title"></span>
	          <span class="property_data">{$active_recurring_profile->getCompanyAddress()|clean|nl2br nofilter}</span>
	        </li>
	      </ul>
	    </div>
			
			<div class="invoice_paper_items">
		    {if is_foreachable($active_recurring_profile->getItems())}
		      <table cellspacing="0" >
		        <thead>
		          <tr>
		            <td class="description">{lang}Description{/lang}</td>
		            <td class="unit_cost">{lang}Unit Cost{/lang}</td>
                {if $invoice_template->getDisplayQuantity()}<td class="quantity">{lang}Qty.{/lang}</td>{/if}
                {if $active_recurring_profile->getSecondTaxIsEnabled()}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                {else}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                {/if}
		            <td class="total">{lang}Total{/lang}</td>
		          </tr>
		        </thead>
		        <tbody>
		        {foreach from=$active_recurring_profile->getItems() item=invoice_item}
		          <tr class="{cycle values='odd,even'}">
		            <td class="description">{$invoice_item->getFormattedDescription() nofilter}</td>
                <td class="unit_cost">{$invoice_item->getUnitCost()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>
                {if $invoice_template->getDisplayQuantity()}<td class="quantity">{$invoice_item->getQuantity()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getFirstTax()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                {if $active_recurring_profile->getSecondTaxIsEnabled()}
                  {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$invoice_item->getSecondTax()|money:$active_recurring_profile->getCurrency():$active_recurring_profile->getLanguage()}</td>{/if}
                {/if}
		            <td class="total">{$invoice_item->getTotal()|money:$active_recurring_profile->getCurrency()}</td>
		          </tr>
		        {/foreach}
		        </tbody>
		        <tfoot>
		          <tr>
		            <td colspan="4" class="label">{lang}Subtotal{/lang}</td>
		            <td class="value"><span class="property_wrapper property_invoice_subtotal">{$active_recurring_profile->getSubTotal()|money:$active_recurring_profile->getCurrency()}</span></td>
		          </tr>
		          <tr>
		            <td colspan="4" class="label">{lang}Tax{/lang}</td>
		            <td class="value"><span class="property_wrapper property_invoice_tax">{$active_recurring_profile->getTax()|money:$active_recurring_profile->getCurrency()}</span></td>
		          </tr>
		          <tr class="total">
		            <td colspan="4" class="label">{lang}Total{/lang}</td>
		            <td class="value total"><span class="property_wrapper property_invoice_total">{$active_recurring_profile->getTotal()|money:$active_recurring_profile->getCurrency()}</span></td>
		          </tr>
		        </tfoot>
		      </table>
		    {else}
		      <p class="empty_page"><span class="inner">{lang}This Recurring Profile has no items{/lang}</span></p>
		    {/if}
	    </div>
	    
	    <div class="invoice_comment" style="display: {if $active_recurring_profile->getPrivateNote()}block{else}none{/if}">
	    	<h3>{lang}Comment{/lang}</h3>
	    	<p>{$active_recurring_profile->getPrivateNote()}</p>
	    </div>
			
			<div class="invoice_paper_notes" style="display: {if $active_recurring_profile->getNote()}block{else}none{/if}">
        <h3>{lang}Note{/lang}</h3>
        <p><span>{$active_recurring_profile->getNote()|clean|nl2br nofilter}</span></p>
      </div>
	  </div>
	  
	  <div class="invoice_bottom"></div>
	  <div class="invoice_bottom_shadow_left"></div><div class="invoice_bottom_shadow_right"></div>
  </div>
{/object}