<div class="company">
	<label class="company_name" for="{$_list_prepared_object_object.name}">
  {if $_list_prepared_object_object.is_new}
    {lang name=$_list_prepared_object_object.name}Company ":name" will <span>not</span> be created{/lang}
  {else}
    {lang name=$_list_prepared_object_object.name}Company ":name" will <span>not</span> be updated{/lang}
  {/if}
  </label>
	<input type="checkbox" name="{$_list_prepared_object_name}[import]" value="ok" id="{$_list_prepared_object_object.name}" class="master_checkbox input_checkbox" />
	
	{wrap field=name}
	  {label for=companyName required=yes}Name{/label}
	  {text_field name="$_list_prepared_object_name[name]" value=$_list_prepared_object_object.name id=companyName class=required}
	{/wrap}
	
	{wrap field=office_address}
	  {label for=companyAddress}Address{/label}
	  {textarea_field name="$_list_prepared_object_name[office_address]" id=companyAddress}{$_list_prepared_object_object.office_address nofilter}{/textarea_field}
	{/wrap}
	
	{wrap field=office_phone}
	  {label for=companyPhone}Phone Number{/label}
	  {text_field name="$_list_prepared_object_name[office_phone]" value=$_list_prepared_object_object.office_phone id=companyPhone}
	{/wrap}
	
	{wrap field=office_fax}
	  {label for=companyFax}Fax Number{/label}
	  {text_field name="$_list_prepared_object_name[office_fax]" value=$_list_prepared_object_object.office_fax id=companyFax}
	{/wrap}
	
	{wrap field=office_homepage}
	  {label for=companyHomepage}Homepage{/label}
	  {text_field name="$_list_prepared_object_name[office_homepage]" value=$_list_prepared_object_object.office_homepage id=companyHomepage}
	{/wrap}
	
	<input type="hidden" name="{$_list_prepared_object_name}[object_type]" value="{$_list_prepared_object_object.object_type}" />
	<input type="hidden" name="{$_list_prepared_object_name}[old_name]" value="{$_list_prepared_object_object.name}" />
	<input type="hidden" name="{$_list_prepared_object_name}[is_new]" value="{if $_list_prepared_object_object.is_new}true{else}false{/if}" />
	<input type="hidden" name="{$_list_prepared_object_name}[updated_on]" value="{if $_list_prepared_object_object.updated_on instanceof DateTimeValue}{$_list_prepared_object_object.updated_on->toMySQL()}{else}{$_list_prepared_object_object.updated_on}{/if}" />
	
	<!-- Import company's users (if there are any) -->
	{if isset($_list_prepared_object_object.users) && is_foreachable($_list_prepared_object_object.users)}
		<div class="company_users">
			{foreach from=$_list_prepared_object_object.users key=user_key item=user}
				{list_prepared_object object=$user type=$user.object_type name="company[$key][users][$user_key]" master_checkbox=false}
		  {/foreach}
		</div>
	{/if}
</div>