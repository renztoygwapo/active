{object object=$active_invoice user=$logged_user show_inspector=false}
	<div class="invoice">
	  <div class="invoice_content">
	    <div class="invoice_data">
	    	<ul class="properties">
	        <li class="property">
	          <span class="property_title"></span>
	          <span class="property_data invoice_name">{$active_invoice->getName()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Currency{/lang}</span>
	          <span class="property_data">{$active_invoice->getCurrencyCode()}</span>
	        </li>
	        
	        {assign var=project value=$active_invoice->getProject() instanceof Project}
	        <li class="property {if !$project}hidden{/if}">
	          <span class="property_title">{lang}Project{/lang}</span>
	          <span class="property_data">{if $active_invoice->getProject() instanceof Project}{object_link object=$active_invoice->getProject()}{/if}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Created On{/lang}</span>
	          <span class="property_data">{if $active_invoice->getCreatedOn()}{$active_invoice->getCreatedOn()|date}{/if}</span>
	        </li>
	        
					{if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_ISSUED}
	        <li class="property">
	          <span class="property_title">{lang}Issued On{/lang}</span>
	          <span class="property_data">{if $active_invoice->getIssuedOn()}{$active_invoice->getIssuedOn()|date}{/if}</span>
	        </li>
					{/if}
					
					{assign var=due_on value=$active_invoice->getDueOn()}
	        <li class="property {if !$due_on}hidden{/if}">
	          <span class="property_title">{lang}Pymt. Due On{/lang}</span>
	          <span class="property_data">{if $active_invoice->getDueOn()}{$active_invoice->getDueOn()|date}{/if}</span>
	        </li>
	        
	        {if $active_invoice->getStatus() == $smarty.const.INVOICE_STATUS_PAID}
	        <li class="property">
	          <span class="property_title">{lang}Paid On{/lang}</span>
	          <span class="property_data">{if $active_invoice->getClosedOn()}{$active_invoice->getClosedOn()|date}{/if}</span>
	        </li>
					{/if}
					
					{assign var=closed_on value=$active_invoice->getClosedOn() instanceof DateValue}
	        <li class="property {if !$closed_on}hidden{/if}">
	          <span class="property_title">{lang}Closed On{/lang}</span>
	          <span class="property_data">{if $active_invoice->getClosedOn()}{$active_invoice->getClosedOn()|date}{/if}</span>
	        </li>
	        
	        <li class="property company_name">
	          <span class="property_title">Company</span>
	          <span class="property_data">{company_link company=$active_invoice->getCompany()}</span>
	        </li>
	        
	        <li class="property company_address">
	          <span class="property_title"></span>
	          <span class="property_data">{$active_invoice->getCompanyAddress()|clean|nl2br nofilter}</span>
	        </li>
	      </ul>
	    </div>
			
			<div class="invoice_paper_items">
		    {if is_foreachable($active_invoice->getItems())}
		      <table cellspacing="0" >
		        <thead>
		          <tr>
		            <td class="description">{lang}Description{/lang}</td>
		            <td class="unit_cost">{lang}Unit Cost{/lang}</td>
		            <td class="quantity">{lang}Qty.{/lang}</td>
                {if $active_invoice->getSecondTaxIsEnabled()}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                {else}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                {/if}
		            <td class="total">{lang}Total{/lang}</td>
		          </tr>
		        </thead>
		        <tbody>
		        {foreach from=$active_invoice->getItems() item=invoice_item}
		          <tr class="{cycle values='odd,even'}">
		            <td class="description">{$invoice_item->getFormattedDescription() nofilter}</td>
		            <td class="unit_cost">{$invoice_item->getUnitCost()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>
		            <td class="quantity">{$invoice_item->getQuantity()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>
                {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount tax_rate">{$invoice_item->getFirstTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                {if $active_invoice->getSecondTaxIsEnabled()}
                  {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$invoice_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount tax_rate">{$invoice_item->getSecondTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>{/if}
                {/if}
		            <td class="total">{$invoice_item->getTotal()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</td>
		          </tr>
		        {/foreach}
		        </tbody>
		        <tfoot>
		          <tr>
		            <td colspan="4" class="label">{lang}Subtotal{/lang}</td>
		            <td class="value"><span class="property_wrapper property_invoice_subtotal">{$active_invoice->getSubTotal()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
		          </tr>

              {if $invoice_template->getSummarizeTax() || !is_foreachable($active_invoice->getTaxGroupedByType())}
                <tr class="tax_row">
                  <td colspan="4" class="label">{lang}Tax{/lang}</td>
                  <td class="value"><span class="property_wrapper property_invoice_tax">{$active_invoice->getTax()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                </tr>
              {else}
                {assign var=grouped_taxes value=$active_invoice->getTaxGroupedByType()}
                {foreach from=$grouped_taxes item=grouped_tax}
                <tr class="tax_row">
                  <td colspan="4" class="label">{$grouped_tax.name}</td>
                  <td class="value"><span class="property_wrapper property_invoice_tax">{$grouped_tax.amount|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
                </tr>
                {/foreach}
              {/if}

              {if $active_invoice->requireRounding()}
              <tr>
                <td colspan="4" class="label">{lang}Rounding Difference{/lang}</td>
                <td class="value"><span class="property_wrapper property_invoice_rounding_difference">{$active_invoice->getRoundingDifference()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
              </tr>
              {/if}
		          <tr class="total">
		            <td colspan="4" class="label">{lang}Total{/lang}</td>
		            <td class="value total"><span class="property_wrapper property_invoice_total">{$active_invoice->getTotal(true)|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
		          </tr>
              <tr>
                <td colspan="4" class="label">{lang}Amount Paid{/lang}</td>
                <td class="value"><span class="property_wrapper property_invoice_paid_amount">{$active_invoice->getPaidAmount()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
              </tr>
              <tr class="total">
                <td colspan="4" class="label">{lang}Balance Due{/lang}</td>
                <td class="value total"><span class="property_wrapper property_invoice_balance_due">{$active_invoice->getBalanceDue()|money:$active_invoice->getCurrency():$active_invoice->getLanguage()}</span></td>
              </tr>
		        </tfoot>
		      </table>
		    {else}
		      <p class="empty_page"><span class="inner">{lang}This invoice has no items{/lang}</span></p>
		    {/if}
	    </div>
	    
	    <div class="invoice_comment" style="display: {if $active_invoice->getPrivateNote()}block{else}none{/if}">
	    	<h3>{lang}Comment{/lang}</h3>
	    	<p>{$active_invoice->getPrivateNote()}</p>
	    </div>
			
			<div class="invoice_paper_notes" style="display: {if $active_invoice->getNote()}block{else}none{/if}">
        <h3>{lang}Note{/lang}</h3>
        <p><span>{$active_invoice->getNote()|clean|nl2br nofilter}</span></p>
      </div>
	  </div>
	  
	  <div class="invoice_bottom"></div>
	  <div class="invoice_bottom_shadow_left"></div><div class="invoice_bottom_shadow_right"></div>
  </div>
{/object}