{title}Sign In{/title}

<div id="auth_dialog_container">
    <div id="auth_dialog">
    <h3><span>{lang}Sign in with your credentials{/lang}</span></h3>
    {form method=post autofocus=$auto_focus show_errors=false action=Router::assemble('login')}
        {wrap field=email class=big_input}
          {text_field name='login[email]' value=$login_data.email id=login_form_email label="e-mail address" tabindex=1}
        {/wrap}
        
        {wrap field=password class=big_input}
          {password_field name='login[password]' id=login_form_password label="password" tabindex=2}
        {/wrap}
        
        {wrap field=remember_me}
          {checkbox_field name="login[remember]" checked=$login_data.remember id=login_form_remember label="Remember me for 14 days" tabindex=4}
        {/wrap}
        
        {wrap field=interface}
          {select_interface name='login[interface]' value=$login_data.interface id=login_form_interface}
        {/wrap}
      
	      {wrap_buttons}
	        {submit tabindex=5}Sign In{/submit}
	      {/wrap_buttons}
    {/form}
    </div>
</div>

<div id="login_footer">
  {link href=Router::assemble('forgot_password') class=forgot_password_link}Forgot password?{/link}
</div>

    <script type="text/javascript">
      var content = $('#auth_dialog_container');
      var form = content.find('form');
      var recover_password_link = content.find('.forgot_password_link');

      {literal}
      $('#auth_dialog_container .big_input').each(function () {
        var wrapper = $(this);
        var label = wrapper.find('label:first');
        var field = wrapper.find('input:first');

        setTimeout(function () {
          field.val() ? label.hide() : setTimeout(function () {
            field.val() ? label.hide() : false;    
          }, 100);  
        }, 100);
        field.val() ? label.hide() : false;
        
        label.bind('click', function () {
          label.hide();
          field.focus();
        });

        field.bind('click focus change', function () {
          label.hide();         
        });

        field.bind('blur', function () {
          if (!field.val()) {
            label.show();
          } // if
        });
      });

      form.submit(function () {
        content.hide();
        var loader = $('<div id="loader"></div>').insertBefore(content);
        $.ajax({
          url : form.attr('action'),
          type : 'post',
          data : form.serialize(),
          success : function (response) {
            App.Wireframe.Flash.success(App.lang('Logged in as :display_name', {display_name : App.clean(response.user.display_name)}));
            window.location = response.redirect;
          },
          error : function (response, response_text) {
            if (response.responseText && response.responseText[0] == '{') {
              var error = $.evalJSON(response.responseText);
              var error_message = error.message;
              if (error.type == 'ValidationErrors') {
                error_message = App.lang("Form failed to submit. These fields could not pass validation:");
                $.each(error.field_errors, function (index, field_error) {
                  error_message += "<br />&nbsp;&nbsp;" + field_error; 
                });
              } // if
              
              App.Wireframe.Flash.error(error_message);

              if (error.redirect_url) {
                window.location = error.redirect_url;
              } else {
                loader.remove();
                content.show();                 
              } // if
            } else {
              App.Wireframe.Flash.error(App.lang('Unknown error occurred'));
              loader.remove();
              content.show();
            } // if
          }
        });
        return false;
      });
      {/literal}
    </script>