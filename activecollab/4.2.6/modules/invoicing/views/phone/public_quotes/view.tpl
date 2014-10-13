{title lang=false}{$active_quote->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="project_request_details" class="object_wrapper">
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
            <span class="property_data">{$active_quote->getCreatedOn()|date}</span>
          </li>

          <li class="property">
            <span class="property_title">{lang}Sent On{/lang}</span>
            <span class="property_data">{$active_quote->getSentOn()|date}</span>
          </li>

          {if $active_quote->getClosedOn()}
          <li class="property">
            <span class="property_title">{lang}Closed On{/lang}</span>
            <span class="property_data">{$active_quote->getClosedOn()|date}</span>
          </li>
          {/if}
          
          <li class="property company_name">
            <span class="property_title">{lang}Company{/lang}</span>
            <span class="property_data">
              {if $active_quote->getCompany() instanceof Company && !$is_frontend}
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
          
          <li class="property contact_person">
            <span class="property_title">{lang}Contact{/lang}</span>
            <span class="property_data">
              {if $is_frontend}
                <a href="mailto:{$active_quote->getRecipientEmail()}">{$active_quote->getRecipientName()}</a>
              {else}
                {user_link user=$active_quote->getRecipient()}
              {/if}
            </span>
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
            {foreach from=$active_quote->getItems() item=quote_item}
              <tr class="{cycle values='odd,even'}">
                <td class="description">{$quote_item->getFormattedDescription() nofilter}</td>
                <td class="unit_cost">{$quote_item->getUnitCost()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
                <td class="quantity">{$quote_item->getQuantity()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>
                {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getFirstTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getFirstTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                {if $active_quote->getSecondTaxIsEnabled()}
                  {if $invoice_template->getDisplayTaxRate()}<td class="tax_rate">{$quote_item->getSecondTaxRatePercentageVerbose()}</td>{elseif $invoice_template->getDisplayTaxAmount()}<td class="tax_amount">{$quote_item->getSecondTax()|money:$active_quote->getCurrency():$active_quote->getLanguage()}</td>{/if}
                {/if}
                <td class="total">{$quote_item->getTotal()|money:$active_quote->getCurrency()}</td>
              </tr>
            {/foreach}
            </tbody>
            <tfoot>
              <tr>
                <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}3{elseif !$active_quote->getSecondTaxIsEnabled()}4{else}5{/if}" class="label">{lang}Subtotal{/lang}</td>
                <td class="value"><span class="property_wrapper property_quote_subtotal">{$active_quote->getSubTotal()|money:$active_quote->getCurrency()}</span></td>
              </tr>
              <tr>
                <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}3{elseif !$active_quote->getSecondTaxIsEnabled()}4{else}5{/if}" class="label">{lang}Tax{/lang}</td>
                <td class="value"><span class="property_wrapper property_quote_tax">{$active_quote->getTax()|money:$active_quote->getCurrency()}</span></td>
              </tr>
              <tr class="total">
                <td colspan="{if !$invoice_template->getDisplayTaxRate() && !$invoice_template->getDisplayTaxAmount()}3{elseif !$active_quote->getSecondTaxIsEnabled()}4{else}5{/if}" class="label">{lang}Total{/lang}</td>
                <td class="value total"><span class="property_wrapper property_quote_total">{$active_quote->getTotal()|money:$active_quote->getCurrency()}</span></td>
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
  
  {frontend_object_comments object=$active_quote user=$logged_user errors=$errors post_comment_url=$active_quote->getPublicUrl() comment_data=$comment_data}
</div>