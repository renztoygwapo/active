{title}Company and Role{/title}
{add_bread_crumb}Company and Role{/add_bread_crumb}

<div id="user_edit_company_and_role">
  {form action=$active_user->getEditCompanyAndRoleUrl() csfr_protect=true}
    {wrap_fields}
	    {wrap field=company_id}
	      {select_company name='user[company_id]' exclude=$exclude_ids value=$user_data.company_id user=$logged_user optional=false id=userCompanyId required=true can_create_new=false label='Company'}
	    {/wrap}
      
      {wrap field=managed_by_id}
        {select_manage_by name="user[managed_by_id]" class='select_managed_by' user_id=$user_data.user_id value=$user_data.managed_by_id label="Managed By" optional=true}
      {/wrap}

      {wrap field=role_and_permissions}
        {if Users::isLastAdministrator($active_user)}
          {label}Role and Permissions{/label}
          <p>{lang}Administrator{/lang} &mdash; {lang}Role of last administrator account can't be changed{/lang}</p>
        {else}
          {select_user_role name='user' value=$user_data required=true label='Role and Permissions'}
        {/if}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
    <div class="hiddenPersonalityType" style="display:none">
      {wrap field=personality_type}
        {label for=personality_type}Personality Type{/label}
        {select_personality_type name='user[personality_type]' value=$user_data.personality_type class=auto optional=true}
      {/wrap}
    </div>
    <div class="hiddenPrivateUrlEnable" style="display:none">
      {wrap field=enable_private_url}
          {label for=enable_private_url}Enable Private URL{/label}
           {select_enable_private_url name='user[private_url_enabled]' value=$user_data.private_url_enabled class=auto optional=true}
      {/wrap}
    </div>

    <div class="hiddenPrivateUrl" style="display:none">
      {wrap field=private_url}
        {text_field name="user[private_url]" value=$user_data.private_url label='Private Url'}
        <span>.{$user_data.rep_site_domain}</span>
        <p class="aid">{lang}eg: <i>john</i>.abuckagallon.com{/lang}</p>
      {/wrap}
    </div>
  {/form}
</div>

<script type="text/javascript">
  $(document).ready(function(){
    // appemd personality_type field after role:Client


    var personalityTypeHtml = $('.hiddenPersonalityType').html();
    $('.role_client').prepend(personalityTypeHtml);
    $('.hiddenPersonalityType').html('');


    var privateUrlHtml = $('.hiddenPrivateUrlEnable').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrlEnable').html('');

    var privateUrlHtml = $('.hiddenPrivateUrl').html();
    $('.role_subcontractor').prepend(privateUrlHtml);
    $('.hiddenPrivateUrl').html('');

  });

</script>