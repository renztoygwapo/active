<div id="company_invoices">
{if $quotes}
  <table class="invoices common" cellspacing="0">
    <thead>
      <tr>
        <th class="status">{lang}Status{/lang}</th>
        <th class="invoice">{lang}Name{/lang}</th>
        <th class="created_on">Created On</th>
        <th class="pdf right">{lang}Download PDF{/lang}</th>
      </tr>
    </thead>
    <tbody>
    {foreach $quotes as $quote}
      <tr class="{cycle values='odd,even'}">
        <td class="status">{$status_map[$quote->getStatus()]}</td>
        <td class="invoice quick_view_item"><a href="{if $logged_user->isFinancialManager()}{$quote->getViewUrl()}{else}{$quote->getCompanyViewUrl()}{/if}">{$quote->getName()}</a></td>
        <td class="created_on">{$quote->getCreatedOn()|date:0}</td>
        <td class="pdf right">
        {if !$quote->isLost()}
          <a href="{$quote->getCompanyPdfUrl()}" target="_blank" title="{lang}Download Quote in PDF Format{/lang}"><img src="{image_url name="icons/16x16/pdf.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>
        {else}
          --
        {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <p class="empty_page">{lang company_name=$active_company->getName()}There are no quotes for :company_name{/lang}</p>
{/if}
</div>

<script type="text/javascript">
  var inline_tabs = $('#company_invoices').parents('.inline_tabs:first');
  if (inline_tabs.length) {
    var tabs_id = inline_tabs.attr('id');
    
    //refresh this tab if some project is updated
    App.Wireframe.Events.bind('quote_created.inline_tab quote_updated.inline_tab quote_deleted.inline_tab', function (event, invoice) {
      App.widgets.InlineTabs.refresh(tabs_id);
    });
  
    App.widgets.InlineTabs.updateCount(tabs_id, false, {$quotes|count});
  } // if
</script>