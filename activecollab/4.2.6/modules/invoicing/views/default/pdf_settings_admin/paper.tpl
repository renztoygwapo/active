{form method="POST" action=$form_url id="invoice_paper_form" enctype="multipart/form-data"}
  <div class="fields_wrapper">
		{wrap field=paper_format}
			{label for=paper_format required=yes}Paper Size:{/label}
      {select_paper_format name="template[paper_size]" value=$template_data.paper_size id="paper_format"}
		{/wrap}
    
    {wrap field=background_image}
      {label for=background_image}Background Image{/label}
      <div class="a4_instructions">
        <p>{lang}Background image needs to be in exact dimensions of <strong>210mm x 297mm 300dpi</strong> and in <strong>PNG</strong> format. If you upload image which is not in those dimensions, it will be stretched out to fit invoice. Bear in mind that it's desirable to optimize this image as that will affect PDF file size. If possible, avoid using transparency as it will greatly increase rendering time, and memory consumption.{/lang}</p>
      </div>
      
      <div class="letter_instructions">
        <p>{lang}Background image needs to be in exact dimensions of <strong>8½ by 11 inches 300dpi</strong> and in <strong>PNG</strong> format. If you upload image which is not in those dimensions, it will be stretched out to fit invoice. Bear in mind that it's desirable to optimize this image as that will affect PDF file size. If possible, avoid using transparency as it will greatly increase rendering time, and memory consumption.{/lang}</p>
      </div>
      
      <div id="upload_image_segment">
        {file_field name="background_image"} {link_button id="remove_background_image" label="Remove Background Image" href=$remove_background_image_url}
      </div>
    {/wrap}
  </div>
  
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
  
  <script type="text/javascript">
  {literal}
    var form = $('#invoice_paper_form');
    var paper_size = form.find('#paper_format');
    var remove_image = form.find('#remove_background_image');
    var upload_image_segment = form.find('#upload_image_segment');
    var submit_button = form.find('.button_holder button[type="submit"]');
    
    var a4_instructions = form.find('.a4_instructions');
    var letter_instructions = form.find('.letter_instructions');

    paper_size.change(function () {
      if (paper_size.val() == 'A4') {
        a4_instructions.show();
        letter_instructions.hide(); 
      } else {
        a4_instructions.hide();
        letter_instructions.show(); 
      } // if      
    }).change();

    if (remove_image.attr('href')) {
	    remove_image.click(function () {
	      upload_image_segment.hide();
	      var action_indicator = $('<div class="remove_invoice_background"><img src="' + App.Wireframe.Utils.indicatorUrl() + '" alt="" />Removing Background Image</div>').insertAfter(upload_image_segment);
	      submit_button.attr('disabled', true);

        $.ajax({
          'url' : remove_image.attr('href'),
          'type' : 'post',
          'data' : { 'submitted' : 'submitted' },
          'success' : function (response) {
            submit_button.attr('disabled', false);
            action_indicator.remove();
            upload_image_segment.show();
            App.Wireframe.Flash.success(App.lang('Invoice background image removed successfully'));
            App.Wireframe.Events.trigger('invoice_template_updated');
            remove_image.hide();    
          },
          'error' : function (response) {
            submit_button.attr('disabled', false);
            action_indicator.remove();
            upload_image_segment.show();
            App.Wireframe.Flash.error(App.lang('Failed to remove background image'));
          }
        });
        
	      return false;
	    });
    } else {
      remove_image.hide();
    } // if

  {/literal}
  </script>
{/form}