{title}Forgot Password{/title}

<div id="auth_dialog_container">
  <div id="auth_dialog">
    {form action=Router::assemble('forgot_password') method=post}
      <h3><span>{lang}Password Recovery{/lang}</span></h3>
        
      {wrap field=email class=big_input}
        {text_field name='forgot_password[email]' value=$forgot_password_data.email label="e-mail address" tabindex=1}
      {/wrap}
            
      {wrap_buttons}
        {submit tabindex=2}Recover Password{/submit}
      {/wrap_buttons}
    {/form}
  </div>
</div>

<div id="login_footer">
  {link href=Router::assemble('login') class=forgot_password_link}Back to Login Form{/link}
</div>

    <script type="text/javascript">
      var content = $('#auth_dialog_container');
      var form = content.find('form');

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

        field.bind('click focus', function () {
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
            loader.remove();
            content.html('<h3>' + App.lang('Instructions sent!') + '</h3><p>' + App.lang('We emailed reset password instructions at :email', {email : response.email}) + '</p>').show();
          },
          error : function (response, response_text) {
            if (response.responseText && response.responseText[0] == '{') {
              var error = $.evalJSON(response.responseText);              
              App.Wireframe.Flash.error(error.message);
            } else {
              App.Wireframe.Flash.error(App.lang('Unknown error occurred'));
            } // if

            loader.remove();
            content.show();
          }
        });
        
        return false;
      });
      {/literal}
    </script>