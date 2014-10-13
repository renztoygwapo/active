{title}Update Password{/title}
{add_bread_crumb}Update Password{/add_bread_crumb}

<div id="user_edit_password">
  {form action=$active_user->getEditPasswordUrl() csfr_protect=true}
    {wrap_fields}
	    {wrap field=password}
	      {label for=userPassword required=yes}Password{/label}
	      {password_field name='user[password]' id=userPassword class='required'}
	    {/wrap}
	    
	    {wrap field=repeat_password}
	      {label for=userRepeatPassword required=yes}Repeat password{/label}
	      {password_field name='user[repeat_password]' id=userRepeatPassword class="required validate_same_as userPassword"}
	    {/wrap}

      {password_rules}
    {/wrap_fields}
    
    {wrap_buttons}
    	{submit}Update Password{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  var form = $('#user_edit_password form:first');
  var password_field = form.find('#userPassword');
  var password_repeat_field = form.find('#userRepeatPassword');

  // you cannot disable submit event propagation, so we use class 'disabled' to prevent it from propagating and handle it in flyoutForm
  form.submit(function (event) {
    var password = $.trim(password_field.val());
    var password_repeat = $.trim(password_repeat_field.val());

    if (!password || password.length < 3) {
      App.Wireframe.Flash.error(App.lang('Minimum password length is 3 characters'));
      form.addClass('disabled');
      return false;
    } // if

    if (password != password_repeat) {
      App.Wireframe.Flash.error(App.lang('Passwords do not match'));
      form.addClass('disabled');
      return false;
    } // if
  });
</script>