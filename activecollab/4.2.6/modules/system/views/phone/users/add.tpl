{title}New User{/title}
{add_bread_crumb}New User{/add_bread_crumb}

<div id="new_user">
	{form action=$active_company->getAddUserUrl() csfr_protect=true}
	  {wrap field=email}
		  {text_field name="user[email]" value=$user_data.email label='Email' id=user_form_email required=true}
		{/wrap}
		
		{if $active_user->canChangeRole($logged_user)}
		  {wrap field=role_id}
		    {if $only_administrator}
          <p>{lang}Administrator{/lang}</p>
		      <input type="hidden" name="user[role_id]" value="{$user_data.role_id}" />
		    {else}
          {label for=user_form_select_role}Role{/label}
          {select_user_role name="user" active_user=$active_user value=$user_data id=user_form_select_role required=true}
          <script type="text/javascript">
            $(document).ready(function() {
              App.Wireframe.RadioButtons.init();
              App.Wireframe.Checkboxes.init();
            });
          </script>
		    {/if}
		  {/wrap}
		{/if}
		
		{wrap field=first_name}
		  {text_field name="user[first_name]" value=$user_data.first_name label='First Name' id=user_form_first_name}
		{/wrap}
		
		{wrap field=last_name}
		  {text_field name="user[last_name]" value=$user_data.last_name label='Last Name' id=user_form_last_name}
		{/wrap}
		
		{wrap field=title}
		  {text_field name='user[title]' value=$user_data.title label='Title' id=user_form_title}
		{/wrap}
		
		{wrap field=welcome_message}
      {textarea_field name="user[welcome_message]" label='Personalize welcome message' id=user_form_welcome_message}{$user_data.welcome_message nofilter}{/textarea_field}
		{/wrap}
		
		<input type="hidden" name="user[send_welcome_message]" value="1" id="user_form_send_welcome_message" />

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('user_created', 'mobile')}">
  {/if}
	
	  {wrap_buttons}
	    {submit}Add User{/submit}
	  {/wrap_buttons}
	{/form}
</div>