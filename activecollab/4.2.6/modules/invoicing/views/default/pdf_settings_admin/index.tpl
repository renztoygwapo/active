{title}Invoice Designer{/title}
{add_bread_crumb}Modify{/add_bread_crumb}
{use_widget name="invoice_designer" module="invoicing"}

<div id="invoice_designer">
  {form}
		<div class="invoice_designer_paper"> 
		</div>
	  
    <div class="invoice_designer_buttons">
      <ul>
        <li><a href="{assemble route=admin_invoicing_pdf_paper}" class="invoice_paper_size" flyout_width="narrow">{lang}Paper Size and Background{/lang}</a></li>
        <li><a href="{assemble route=admin_invoicing_pdf_header}" class="invoice_header">{lang}Header Settings{/lang}</a></li>
        <li><a href="{assemble route=admin_invoicing_pdf_body}" class="invoice_body">{lang}Body Settings{/lang}</a></li>
        <li><a href="{assemble route=admin_invoicing_pdf_footer}" class="invoice_footer">{lang}Footer Settings{/lang}</a></li>
      </ul>      
    </div>
    
    <div class="disclamer">
      {lang url=$sample_url}Picture on the left is only the draft preview. Font will not be applied to it due to technical limitations.<br /><br /> Download the <a href=":url" target="_blank">sample invoice</a> to see the final invoice design.{/lang}
    </div>
  {/form}
</div>

<script type="text/javascript">
  $('#invoice_designer').invoiceDesigner({
    'template' : {$active_template|json nofilter},
    'invoice' : {$sample_invoice|json nofilter}
  });
</script>