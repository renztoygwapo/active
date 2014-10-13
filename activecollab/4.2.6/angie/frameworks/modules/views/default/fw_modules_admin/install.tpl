{title}Install Module{/title}
{add_bread_crumb}Install{/add_bread_crumb}

<!--  Install -->
<div id="install_module" class="content_stack_element">
  
  	<div id="installation_steps">
  	  <ul id="initialize_upgrade_process" class="async_process">
      {foreach $installation_steps as $installation_step}
        <li class="step" step_url="{$installation_step.url}">{$installation_step.text}</li>
        
      {/foreach}
      </ul>
  	</div>
	
	<p style="display: none; margin-left:10px;" id="message"></p>
	
  </div>

<script type="text/javascript">
	
	$('#initialize_upgrade_process').asyncProcess({
    'success' : function(response, step_num) {
	  $('#initialize_upgrade_process').after(response);
      //$('#install_module #message').html(App.ucfirst(response.name) + ' module successfully installed').show();
	    App.widgets.FlyoutDialog.front().close();
      App.Wireframe.Events.trigger('module_created',response);
//	  $("#close_btn_box").show().find('button').click(function(){
//			App.Wireframe.Events.trigger('module_created',response);
//		  App.widgets.FlyoutDialog.front().close();
//		});
    },
    'error' : function (response) {
    	$('#install_module #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();
//    	$("#close_btn_box").show().find('button').click(function(){
//			App.widgets.FlyoutDialog.front().close();
//		});
    },
    'on_step_success' : function (response) {
		
    },
    'on_step_error' : function (response) {
        //App.Wireframe.Flash.error(App.Wireframe.Utils.responseToErrorMessage(response));
    	$('#install_module #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();
//    	$("#close_btn_box").show().find('button').click(function(){
//			App.widgets.FlyoutDialog.front().close();
//		});
    }
  });
</script>


