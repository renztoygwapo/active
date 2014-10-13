{title}Import Language{/title}
{add_bread_crumb}Importing{/add_bread_crumb}

{if $import_steps}
  <div id="import_language" class="content_stack_element">
    
    	<div id="import_steps">
    	  <ul id="initialize_import_process" class="async_process">
        {foreach $import_steps as $import_step}
          <li class="step" step_url="{$import_step.url}">{$import_step.text}</li>
          
        {/foreach}
        </ul>
    	</div>
  	
  	<p style="display: none; margin-left:10px;" id="message"></p>
  	
    </div>
    
   <script type="text/javascript">

    try {
      $('#initialize_import_process').asyncProcess({
        'success' : function(response, step_num) {
        $('#initialize_import_process').after(response);
          App.widgets.FlyoutDialog.front().close();
          App.Wireframe.Events.trigger('language_created',response);

        },
        'error' : function (response) {
          $('#import_language #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();

        },
        'on_step_success' : function (response) {

        },
        'on_step_error' : function (response) {
           $('#import_language #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();
        }
      });
    } catch (Exception) {

    } // try
  </script>
{/if}