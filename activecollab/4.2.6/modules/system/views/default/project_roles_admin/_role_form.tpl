<div class="content_stack_wrapper">
  <div class="content_stack_element odd">
    <div class="content_stack_element_info">
      <h3>{lang}Role{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=name}
        {text_field name="role[name]" value=$role_data.name required=true}
      {/wrap}
    </div>
  </div>
  
  <div class="content_stack_element role_permissions even">
    <div class="content_stack_element_info">
      <h3>{lang}Permissions{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=permissions}
        {select_project_permissions name="role[permissions]" value=$role_data.permissions}
      {/wrap}
    </div>
  </div>
</div>