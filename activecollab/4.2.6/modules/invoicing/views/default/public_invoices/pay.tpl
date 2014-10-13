<div id="public_invoice_details">
  <span class="title_image">
    <img src="{image_url name="public-invoice/wallet.png" module='invoicing'}"/>
  </span>
  <div class="name">
    <h1>
      {if $active_object->isPaid()}
        Download Invoice {$active_object->getNumber()}
      {else}
        Make a Payment
      {/if}</h1>
  </div>
</div>

{if $active_object->isPaid()}
  {form action=$active_object->getPublicPdfUrl()}
  <div id="public_invoice_download">
    <div class="download_paid_invoice">
      <h1>{lang}Thank you for your payment!{/lang}</h1>
      <div class="invoice_thumbnail">
        <img src="{image_url name="public-invoice/public-invoice.png" module='invoicing'}"/>
      </div>
      <div class="invoice_download_footer">
        <p>{lang}Your invoice is ready for download!{/lang}</p>
        <div class="download_btn_container">
          {submit}Download Invoice{/submit}
        </div>
      </div>
    </div>
  </div>
  {/form}
{else}
  {form action=$active_object->payments()->getPublicUrl() class="public_invoice_form"}
  <div id="public_invoice">
    <div class="public_invoice_container">
      {make_payment object=$active_object is_public=true}
    </div>
    <div class="public_invoice_footer">
      {wrap_buttons}
        {submit}Make payment{/submit}
      {/wrap_buttons}
    </div>
  </div>
  {/form}
{/if}

<script type="text/javascript">
  $('#public_invoice_details').detach().appendTo($('#public_page_title .public_wrapper').empty());

  var wrapper = $('div#public_invoice');
  var make_payment_btn = wrapper.find('div.public_invoice_footer button[type=submit]');
  var form = $('form.public_invoice_form');
  form.submit(function(){
    var old_label = make_payment_btn.html();
    var new_label = App.lang('Processing...');
    make_payment_btn.attr('disabled','disabled');
    make_payment_btn.html(new_label);
  });

</script>