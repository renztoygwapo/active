{title lang=false}{$active_quote->getName() nofilter}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="project_request_details" class="object_wrapper">
  {if $quote_expired}
  <p class="page_message warning">{lang}<span class="label">Important</span>: Access to this quote is no longer available to clients{/lang}</p>
  {/if}
  {assign_var name=quote_class}
    {if $active_quote->isDraft()}
      quote_draft
    {elseif $active_quote->isSent()}
      quote_sent
    {elseif $active_quote->isWon()}
      quote_won
    {elseif $active_quote->isLost()}
      quote_lost
    {/if}
  {/assign_var}

  <div class="invoice_paper_wrapper {$quote_class|trim}_wrapper quote_{$active_quote->getId()}">
	  <div class="invoice_paper {$quote_class|trim}">
	    <div class="invoice_paper_top"></div>
	    <div class="invoice_paper_center">
	      <div class="invoice_paper_area">
	        <div class="invoice_paper_logo"></div>

	        <div class="invoice_paper_header">
	          <div class="invoice_paper_details">
	            <h2><span class="property_quote_name">{$active_quote->getName()}</span></h2>
	            <ul>

	              <li class="quote_currency property_wrapper">{lang}Currency{/lang}: <strong><span class="property_quote_currency">{$active_quote->getCurrencyCode()}</span></strong></li>
                <li class="quote_created_on property_wrapper">{lang}Created On{/lang}: <span class="property_quote_created_on">{$active_quote->getCreatedOn()|date}</span></li>
                <li class="quote_sent_on property_wrapper">{lang}Sent On{/lang}: <span class="property_quote_sent_on">{$active_quote->getSentOn()|date}</span></li>
                <li class="quote_closed_on property_wrapper">{lang}Closed On{/lang}: <span class="property_quote_closed_on">{if $active_quote->getClosedOn() instanceof DateTimeValue}{$active_quote->getClosedOn()|date}{else}{lang}Not Closed Yet{/lang}{/if}</span></li>

	            </ul>
	          </div>

	          <div class="invoice_paper_client"><div class="invoice_paper_client_inner">
	            <div class="invoice_paper_client_name property_wrapper">
                <span class="property_quote_client_name">
                  {if $active_quote->getCompany() instanceof Company && !$is_frontend}
                    {company_link company=$active_quote->getCompany()}
                  {else}
                    <b>{$active_quote->getCompanyName()}</b>
                  {/if}
                  <br/>
                  {$active_quote->getCompanyAddress()|clean|nl2br nofilter}
                </span>
              </div>
	            <div class="invoice_paper_client_address property_wrapper">
                  <span class="property_quote_client_address">
                    {lang}Contact Person{/lang}:
                    {if $is_frontend}
                      <a href="mailto:{$active_quote->getRecipientEmail()}">{$active_quote->getRecipientName()}</a>
                    {else}
                      {user_link user=$active_quote->getRecipient()}
                    {/if}
                  </span>
              </div>
	          </div></div>
	        </div>

	        <div class="invoice_paper_items">
				    {if is_foreachable($active_quote->getItems())}
				      <table cellspacing="0" >
				        <thead>
				          <tr>
				            <td class="num"></td>
				            <td class="description">{lang}Description{/lang}</td>
				            <td class="unit_cost">{lang}Unit Cost{/lang}</td>
				            <td class="quantity">{lang}Qty.{/lang}</td>
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
				        {foreach from=$active_quote->getItems() item=quote_item name=item_foreach}
				          <tr class="{cycle values='odd,even'}">
				            <td class="num">#{$smarty.foreach.item_foreach.iteration}</td>
				            <td class="description">{$quote_item->getFormattedDescription() nofilter}</td>
				            <td class="unit_cost">{$quote_item->getUnitCost()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
				            <td class="quantity">{$quote_item->getQuantity()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
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
				            <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}4{elseif !$active_quote->getSecondTaxIsEnabled()}5{else}6{/if}" class="label">{lang}Subtotal{/lang}</td>
				            <td class="value"><span class="property_wrapper property_quote_subtotal">{$active_quote->getSubTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
				          </tr>
				          <tr>
				            <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}4{elseif !$active_quote->getSecondTaxIsEnabled()}5{else}6{/if}" class="label">{lang}Tax{/lang}</td>
				            <td class="value"><span class="property_wrapper property_quote_tax">{$active_quote->getTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
				          </tr>
                  {if !$active_quote->requireRounding()}
                    <tr class="property_wrapper" style="">
                      <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}4{elseif !$active_quote->getSecondTaxIsEnabled()}5{else}6{/if}" class="label">{lang}Rounding Difference{/lang}</td>
                      <td class="value total"><span class="property_wrapper property_quote_rounding_difference">{$active_quote->getRoundingDifference()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
                    </tr>
                  {/if}
				          <tr class="total">
				            <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}4{elseif !$active_quote->getSecondTaxIsEnabled()}5{else}6{/if}" class="label">{lang}Total{/lang}</td>
				            <td class="value total"><span class="property_wrapper property_quote_total">{$active_quote->getTotal()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</span></td>
				          </tr>
				        </tfoot>
				      </table>
				    {else}
				      <p class="empty_page"><span class="inner">{lang}This quote has no items{/lang}</span></p>
				    {/if}
	        </div>


		      <div class="invoice_paper_notes property_wrapper" style="display: {if $active_quote->getNote()}block{else}none{/if}">
		        <h3>{lang}Note{/lang}</h3>
		        <p><span class="property_quote_note">{$active_quote->getNote()|clean|nl2br nofilter}</span></p>
		      </div>

	      </div>
	    </div>
	    <div class="invoice_paper_bottom"></div>

      <div class="invoice_paper_peel_draft"></div>
	    <div class="invoice_paper_stamp_paid"></div>
	    <div class="invoice_paper_stamp_canceled"></div>
	  </div>
  </div>
  {frontend_object_comments object=$active_quote user=$logged_user errors=$errors post_comment_url=$active_quote->getPublicUrl() comment_data=$comment_data}
</div>