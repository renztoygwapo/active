{wrap field="data_source_name"}
  {label for=data_source_name required=yes}Name{/label}
  {text_field name='data_source[name]' test_name="name" value=$source_data.name id=data_source_name required=true}
{/wrap}

{wrap field="data_source_account_id"}
  {label for=data_source_account_id required=yes}Account ID{/label}
  {text_field name='data_source[additional_properties][account_id]' test_name="account_id" value=$source_data.account_id id=data_source_account_id required=true}
{/wrap}
{wrap field="data_source_username"}
  {label for=data_source_username required=yes}Username{/label}
  {text_field name='data_source[additional_properties][username]' test_name="username" value=$source_data.username id=data_source_username required=true}
{/wrap}
{wrap field="data_source_account_password"}
  {label for=data_source_account_password required=yes}Password{/label}
  {password_field name='data_source[additional_properties][password]' test_name="password" value=$source_data.password id=data_source_account_password required=true}
{/wrap}
{wrap field="data_source_import_settings"}
  {label for=data_source_import_settings required=yes}Import Settings{/label}
  {radio_field name="data_source[additional_properties][import_settings]" value=Basecamp::IMPORT_SETTINGS_TODO_LIST_AS_TASK_CATEGORY pre_selected_value=$source_data.import_settings label="Import TODO lists as Task Category and TODOs as Tasks"}
  <br/>
  {radio_field name="data_source[additional_properties][import_settings]" value=Basecamp::IMPORT_SETTINGS_TODO_LIST_AS_TASK pre_selected_value=$source_data.import_settings label="Import TODO lists as Task and TODOs as Subtasks"}
{/wrap}
{wrap field="data_source_use_company"}
  {label for=data_source_use_company required=yes}Import Users in{/label}
  {select_company id="data_source_use_company" name="data_source[additional_properties][import_users_in_company]" value=$source_data.import_users_in_company user=$logged_user}
{/wrap}
{wrap field="data_source_user_role"}
  {label for=data_source_user_role required=yes}As{/label}
  <p class="details">This role will be applied on users who doesn't have "admin" access in your basecamp profile</p>
  {select_user_role id="data_source_user_role" name="data_source[additional_properties][import_users_with_role]" value=$source_data.import_users_with_role}
{/wrap}
