{title}Send Welcome Message{/title}
{add_bread_crumb}Send Welcome Message{/add_bread_crumb}

<div id="send_welcome_message">
  <div class="fields_wrapper">
	  <p>{lang}Welcome message includes information user needs in order to log in: <strong>link to login form, email and password</strong>. Optionally, you can personalize message or provide more information using Personalize Message field below{/lang}.</p>
	  <p>{lang}For security reasons system does not store passwords in readable format. Because of this, <strong>random password will be generated</strong> each time you send a welcome message{/lang}!</p>
  </div>
  {form action=$active_user->getSendWelcomeMessageUrl() method=post}
    {wrap_fields}
	    {wrap field=message}
	      {label for=sendWelcomeMessageMessage}Personalize Message{/label}
	      {textarea_field name='welcome_message[message]' id=sendWelcomeMessageMessage}{$welcome_message_data.message nofilter}{/textarea_field}
	    {/wrap}
	    <p class="details boxless">{lang}HTML not supported! Line breaks are preserved. Links are recognized and converted{/lang}.</p>
    {/wrap_fields}
    {wrap_buttons}
      {submit}Send Welcome Message{/submit}
    {/wrap_buttons}
  {/form}
</div>