<div id="remember_me">
	<div id="{$_remember_id}" class="login_form_remember {if !$_remember_checked}off{else}on{/if}">
		<span>{$_remember_label}</span>
		<input type="hidden" name="{$_remember_name}" value="" class='selected_value' />
	</div>
</div>

<script type="text/javascript">
  var control = $('#{$_remember_id}');
  var input_field = control.find('input.selected_value');
  
  control.click(function() {
  	var wrapper = $(this);
  	
  	if(wrapper.hasClass('off')) {
  		wrapper.removeClass('off');
    	wrapper.addClass('on');
    	input_field.val('checked');
  	} else if (wrapper.hasClass('on')) {
  		wrapper.removeClass('on');
    	wrapper.addClass('off');
    	input_field.val('');
  	} // if
    
    return false;
  });
</script>