{add_bread_crumb}Details{/add_bread_crumb}

{render_invoice invoice=$active_invoice}

{if !$active_invoice->isCreditInvoice()}
  {render_payments}
{/if}


    
