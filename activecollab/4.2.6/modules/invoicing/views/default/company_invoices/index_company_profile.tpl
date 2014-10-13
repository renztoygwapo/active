<div id="company_invoices">
{if $invoices}
  <table class="invoices common" cellspacing="0">
    <thead>
      <tr>
        <th></th>
        <th class="invoice">{lang}Invoice #{/lang}</th>
        <th class="project">{lang}Project{/lang}</th>
        <th class="status">{lang}Status{/lang}</th>
        <th class="due_on">{lang}Payment Due On{/lang}</th>
        <th class="pdf right">{lang}Download PDF{/lang}</th>
      </tr>
    </thead>
    <tbody>
    {foreach $invoices as $invoice}
      <tr class="{cycle values='odd,even'} {if $invoice->isOverdue()}overdue{/if}">
        <td>
          {if $invoice->isOverdue()}
          <img src="{image_url name='icons/16x16/important.png' module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" title="{lang}Invoice is overdue{/lang}" />
          {/if}
        </td>
        <td class="invoice quick_view_item">{invoice_link invoice=$invoice company=!$logged_user->isFinancialManager()}</a></td>
        <td class="project quick_view_item">
        {if $invoice->getProject() instanceof Project}
          {project_link project=$invoice->getProject()}
        {else}
          --
        {/if}
        </td>
      {if $invoice->isIssued()}
        <td class="status quick_view_item">{action_on_by datetime=$invoice->getIssuedOn() user=$invoice->getIssuedBy() action='Issued' offset=0}</td>
        <td class="due_on">{$invoice->getDueOn()|date:0}</td>
        <td class="pdf right"><a href="{$invoice->getCompanyPdfUrl()}" title="{lang}Download Invoice in PDF Format{/lang}"><img src="{image_url name="icons/16x16/pdf.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a></td>
      {elseif $invoice->isPaid()}
        <td class="status quick_view_item">{action_on_by datetime=$invoice->getClosedOn() user=$invoice->getClosedBy() action='Paid' offset=0}</td>
        <td class="due_on">{$invoice->getDueOn()|date:0}</td>
        <td class="pdf right"><a href="{$invoice->getCompanyPdfUrl()}" title="{lang}Download Invoice in PDF Format{/lang}"><img src="{image_url name="icons/16x16/pdf.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a></td>
      {elseif $invoice->isCanceled()}
        <td class="status quick_view_item">{action_on_by datetime=$invoice->getClosedOn() user=$invoice->getClosedBy() action='Canceled' offset=0}</td>
        <td class="due_on no_due_date">--</td>
        <td class="pdf right">--</td>
      {elseif $invoice->isDraft()}
        <td class="status quick_view_item">{action_on_by datetime=$invoice->getCreatedOn() user=$invoice->getCreatedBy() action='Created' offset=0}</td>
        <td class="due_on no_due_date">--</td>
        <td class="pdf right">--</td>
      {/if}
      </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <p class="empty_page">{lang}There are no invoices to display{/lang}</p>
{/if}

<script type="text/javascript">
	var inline_tabs = $('#company_invoices').parents('.inline_tabs:first');
	if (inline_tabs.length) {
	  var tabs_id = inline_tabs.attr('id');
	  
	  //refresh this tab if some project is updated
	  App.Wireframe.Events.bind('invoice_created.inline_tab invoice_updated.inline_tab invoice_deleted.inline_tab', function (event, invoice) {
	    App.widgets.InlineTabs.refresh(tabs_id);
	  });
	
	  App.widgets.InlineTabs.updateCount(tabs_id, false, {$invoices|count});
	} // if
</script>