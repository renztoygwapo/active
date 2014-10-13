<div class="user">
  {if $_list_prepared_object_object.first_name && $_list_prepared_object_object.last_name}
    {assign_var name=user_display_name}{$_list_prepared_object_object.first_name} {$_list_prepared_object_object.last_name}{/assign_var}
  {else}
    {assign_var name=user_display_name}{$_list_prepared_object_object.email}{/assign_var}
  {/if}

	<label class="user_name" for="{$_list_prepared_object_object.email}">
    {if $_list_prepared_object_object.is_new}
      {if $_list_prepared_object_object.company_defined}
        {lang user_name=$user_display_name company_name=$_list_prepared_object_object.company_name}User ":user_name" from ":company_name" company will <span>not</span> be added{/lang}
      {else}
        {lang user_name=$user_display_name}User ":user_name" will <span>not</span> be added{/lang}
      {/if}
    {else}
      {lang user_name=$user_display_name company_name=$_list_prepared_object_object.company_name}User ":user_name" from ":company_name" company will <span>not</span> be updated{/lang}
    {/if}
  </label>
	<input type="checkbox" name="{$_list_prepared_object_name}[import]}" value="ok" id="{$_list_prepared_object_object.email}" class="{if $_list_prepared_object_master_checkbox}master_checkbox{else}slave_checkbox{/if} input_checkbox" />

  {if $_list_prepared_object_object.is_new && !$_list_prepared_object_object.company_defined}
    {wrap field=company_id}
      {label for=companyId required=yes}Company{/label}
      {select_company name="$_list_prepared_object_name[company_id]" value=$_list_prepared_object_object.company_id user=$logged_user optional=false success_event=company_created required=true class=company}
    {/wrap}
  {/if}

  {wrap field=email}
	  {label for=userEmail required=yes}Email{/label}
	  {text_field name="$_list_prepared_object_name[email]" value=$_list_prepared_object_object.email id=userEmail class="required validate_email"}
	{/wrap}
	
	{wrap field=first_name}
	  {label for=userFirstName}First Name{/label}
	  {text_field name="$_list_prepared_object_name[first_name]" value=$_list_prepared_object_object.first_name id=userFirstName}
	{/wrap}
	
	{wrap field=last_name}
	  {label for=userLastName}Last Name{/label}
	  {text_field name="$_list_prepared_object_name[last_name]" value=$_list_prepared_object_object.last_name id=userLastName}
	{/wrap}
	
	{wrap field=title}
	  {label for=userTitle}Title{/label}
	  {text_field name="$_list_prepared_object_name[title]" value=$_list_prepared_object_object.title id=userTitle}
	{/wrap}
	
	{wrap field=phone_work}
	  {label for=userPhoneWork}Office Phone Number{/label}
	  {text_field name="$_list_prepared_object_name[phone_work]" value=$_list_prepared_object_object.phone_work id=userPhoneWork}
	{/wrap}
	
	{wrap field=phone_mobile}
	  {label for=userPhoneMobile}Mobile Phone Number{/label}
	  {text_field name="$_list_prepared_object_name[phone_mobile]" value=$_list_prepared_object_object.phone_mobile id=userPhoneMobile}
	{/wrap}
	
	{wrap field=im}
	  {label for=userIm}Instant Messenger{/label}
	  {select_im_type name="$_list_prepared_object_name[im_type]" value=$_list_prepared_object_object.im_type id=userImType class=auto} {text_field name="$_list_prepared_object_name[im_value]" value=$_list_prepared_object_object.im_value id=userIm}
	{/wrap}
	
	<input type="hidden" name="{$_list_prepared_object_name}[object_type]" value="{$_list_prepared_object_object.object_type}" />
	<input type="hidden" name="{$_list_prepared_object_name}[old_email]" value="{$_list_prepared_object_object.email}" />
	<input type="hidden" name="{$_list_prepared_object_name}[is_new]" value="{if $_list_prepared_object_object.is_new}true{else}false{/if}" />
	<input type="hidden" name="{$_list_prepared_object_name}[updated_on]" value="{if $_list_prepared_object_object.updated_on instanceof DateTimeValue}{$_list_prepared_object_object.updated_on->toMySQL()}{else}{$_list_prepared_object_object.updated_on}{/if}" />
	<input type="hidden" name="{$_list_prepared_object_name}[company_name]" value="{$_list_prepared_object_object.company_name}" />
</div>