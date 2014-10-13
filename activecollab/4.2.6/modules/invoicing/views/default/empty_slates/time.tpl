<div id="empty_slate_invoice_time" class="empty_slate">
  <h3>{lang}About Invoice Time and Expenses{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name="empty-slates/date-time.png" module=$smarty.const.SYSTEM_MODULE}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Related Time and Expense Records{/lang}</span>
      <span class="icon_list_description">{lang}When an invoice is issued, all Time and Expense records related to the invoice are automatically marked as "Pending Payment". When the invoice is marked as paid, all related Time and Expense records are automatically marked as paid. When the invoice is canceled, all related records are released and automatically reverted to their original billable state{/lang}.</span>
    </li>
    
    <li>
      <img src="{image_url name="empty-slates/release.png" module=invoicing}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Releasing Time and Expense Records{/lang}</span>
      <span class="icon_list_description">{lang}When records are released, the relation between the invoice and the items is removed, without deleting any records. The related records are reverted to their original billable state, while issuing, canceling or paying the invoice will not change the item status in the future{/lang}.</span>
    </li>
  </ul>
</div>