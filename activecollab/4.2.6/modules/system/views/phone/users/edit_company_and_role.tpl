{title}Company and Role{/title}
{add_bread_crumb}Company and Role{/add_bread_crumb}

<div id="user_edit_company_and_role">
  {form action=$active_user->getEditCompanyAndRoleUrl() csfr_protect=true}
    {wrap field=company_id}
      {select_company name='user[company_id]' exclude=$exclude_ids value=$user_data.company_id user=$logged_user optional=false id=userCompanyId label="Company" required=true}
    {/wrap}

    {wrap field=role_and_permissions}
      {label for=user_form_select_role}Role and Permissions{/label}
      {if Users::isLastAdministrator($active_user)}
        <p>{lang}Administrator{/lang} &mdash; {lang}Role of last administrator account can't be changed{/lang}</p>
      {else}
        {select_user_role name='user' value=$user_data required=true id=user_form_select_role}
      {/if}
    {/wrap}

    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $(document).ready(function() {
    App.Wireframe.SelectBox.init();
    App.Wireframe.RadioButtons.init();
    App.Wireframe.Checkboxes.init();
  });
</script>