{object object=$active_quote user=$logged_user show_inspector=false}
	<div class="invoice">
	  <div class="invoice_content">
	    <div class="invoice_data">
	    	<ul class="properties">
	        <li class="property">
	          <span class="property_title"></span>
	          <span class="property_data invoice_name">{$active_quote->getName()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Currency{/lang}</span>
	          <span class="property_data">{$active_quote->getCurrencyCode()}</span>
	        </li>
	        
	        <li class="property">
	          <span class="property_title">{lang}Created On{/lang}</span>
	          <span class="property_data">{if $active_quote->getCreatedOn()}{$active_quote->getCreatedOn()|date}{/if}</span>
	        </li>
	        
					{if $active_quote->getStatus() == $smarty.const.INVOICE_STATUS_SENT}
	        <li class="property">
	          <span class="property_title">{lang}Sent On{/lang}</span>
	          <span class="property_data">{if $active_quote->getSentOn()}{$active_quote->getSentOn()|date}{/if}</span>
	        </li>
					{/if}
					
					{assign var=closed_on value=$active_quote->getClosedOn() instanceof DateValue}
	        <li class="property {if !$closed_on}hidden{/if}">
	          <span class="property_title">{lang}Closed On{/lang}</span>
	          <span class="property_data">{if $active_quote->getClosedOn()}{$active_quote->getClosedOn()|date}{/if}</span>
	        </li>
	        
	        <li class="property company_name">
	          <span class="property_title">{lang}Company{/lang}</span>
	          <span class="property_data">
	          	{if $active_quote->getCompany() instanceof Company}
                {company_link company=$active_quote->getCompany()}
              {else}
                <b>{$active_quote->getCompanyName()}</b>
              {/if}
	          </span>
	        </li>
	        
	        <li class="property company_address">
	          <span class="property_title"></span>
	          <span class="property_data">{$active_quote->getCompanyAddress()|clean|nl2br nofilter}</span>
	        </li>
	      </ul>
	    </div>
			
			<div class="invoice_paper_items">
		    {if is_foreachable($active_quote->getItems())}
		      <table cellspacing="0" >
			      <thead>
			        <tr>
			          <td class="description">{lang}Description{/lang}</td>
			          <td class="unit_cost">{lang}Unit Cost{/lang}</td>
                {if $invoice_template->getDisplayQuantity()}<td class="quantity">{lang}Qty.{/lang}</td>{/if}
                {if $active_quote->getSecondTaxIsEnabled()}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #1{/lang}</td>{/if}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax #2{/lang}</td>{/if}
                {else}
                  {if ($invoice_template->getDisplayTaxRate() || $invoice_template->getDisplayTaxAmount())}<td class="tax_rate">{lang}Tax{/lang}</td>{/if}
                {/if}
			          <td class="total">{lang}Total{/lang}</td>
			        </tr>
			      </thead>
			      <tbody>
			      {foreach from=$active_quote->getItems() item=quote_item}
			        <tr class="{cycle values='odd,even'}">
			          <td class="description">{$quote_item->getFormattedDescription() nofilter}</td>
                <td class="unit_cost">{$quote_item->getUnitCost()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
                {if $invoice_template->getDisplayQuantity()}<td class="quantity">{$quote_item->getQuantity()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getFirstTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                {if $active_quote->getSecondTaxIsEnabled()}
                  {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getSecondTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                {/if}
			          <td class="total">{$quote_item->getTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
			        </tr>
			      {/foreach}
			      </tbody>
			      <tfoot>
			        <tr>
			          <td colspan="4" class="label">{lang}Subtotal{/lang}</td>
			          <td class="value"><span class="property_wrapper property_quote_subtotal">{$active_quote->getSubTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
			        </tr>

              {if $invoice_template->getSummarizeTax() || !is_foreachable($active_quote->getTaxGroupedByType())}
                <tr class="tax_row">
                  <td colspan="4" class="label">{lang}Tax{/lang}</td>
                  <td class="value"><span class="property_wrapper property_invoice_tax">{$active_quote->getTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                </tr>
              {else}
                {assign var=grouped_taxes value=$active_quote->getTaxGroupedByType()}
                {foreach from=$grouped_taxes item=grouped_tax}
                  <tr class="tax_row">
                    <td colspan="4" class="label">{$grouped_tax.name}</td>
                    <td class="value"><span class="property_wrapper property_invoice_tax">{$grouped_tax.amount|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                  </tr>
                {/foreach}
              {/if}

              {if $active_quote->requireRounding()}
              <tr>
                <td colspan="4" class="label">{lang}Rounding Difference{/lang}</td>
                <td class="value"><span class="property_wrapper property_invoice_rounding_difference">{$active_quote->getRoundingDifference()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
              </tr>
              {/if}
			        <tr class="total">
			          <td colspan="4" class="label">{lang}Total{/lang}</td>
			          <td class="value total"><span class="property_wrapper property_quote_total">{$active_quote->getTotal(true)|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
			        </tr>
			      </tfoot>
			    </table>
		    {else}
		      <p class="empty_page"><span class="inner">{lang}This quote has no items{/lang}</span></p>
		    {/if}
	    </div>
			
			<div class="invoice_paper_notes" style="display: {if $active_quote->getNote()}block{else}none{/if}">
        <h3>{lang}Note{/lang}</h3>
        <p><span>{$active_quote->getNote()|clean|nl2br nofilter}</span></p>
      </div>
	  </div>
	  
	  <div class="invoice_bottom"></div>
	  <div class="invoice_bottom_shadow_left"></div><div class="invoice_bottom_shadow_right"></div>
  </div>
  
  {object_comments object=$active_quote user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=quote_comments}
  {render_comment_form object=$active_quote id=quote_comments}
{/object}