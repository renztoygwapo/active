{title}Invoice Payment Options{/title}
<div id="invoice_public_payment_info">
  <p>{lang}Payments for this Invoice can be made from this URL:{/lang}</p>
  <div class="public_url">
    {link href="{$active_invoice->payments()->getPublicUrl()}" target="_blank"}{$active_invoice->payments()->getPublicUrl()}{/link}
  </div>
</div>