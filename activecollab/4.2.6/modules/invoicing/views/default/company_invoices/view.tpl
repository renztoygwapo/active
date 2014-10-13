{title}{$active_invoice->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{render_invoice invoice=$active_invoice}

<div class="wireframe_content_wrapper">
  {render_payments}
</div>