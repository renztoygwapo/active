{form action=$disk_settings_url method=post id="disk_space_settings"}

<div class="content_stack_wrapper autoscrolled">
  <div class="content_stack_element">
    <div class="content_stack_element_info">
      <h3>{lang}Limits{/lang}</h3>
    </div>
    <div class="content_stack_element_body">
      {wrap field=label}
        {label for=ObjectLabel}Disk Space Limit{/label}
        {text_field name="disk_settings_data[disk_space_limit]" value=$disk_usage_settings.disk_space_limit id="disk_space_limit_field" disabled=!$can_modify_disk_limit} GB
      {/wrap}
    </div>
  </div>

  <div class="content_stack_element last">
    <div class="content_stack_element_info">
      <div class="content_stack_optional">
          <div class="checkbox_wrapper">
            <label>{checkbox_field name="disk_settings_data[disk_space_email_notifications]" value="1" checked=$disk_usage_settings.disk_space_email_notifications} {lang}Send{/lang}</label>
          </div>
      </div>

      <h3>{lang}Email Notifications{/lang}</h3>
      <p class="aid">{lang}Send Email Notifications to Administrators when Low Space threshold is reached.{/lang}</p>
    </div>
    <div class="content_stack_element_body">
      {wrap field=label}
        {label for=ObjectLabel}Low Space Threshold{/label}
        {select_low_disk_space_threshold name="disk_settings_data[disk_space_low_space_threshold]" value=$disk_usage_settings.disk_space_low_space_threshold}
      {/wrap}
    </div>
  </div>
</div>

  {wrap_buttons}
    {submit}Update Settings{/submit}
  {/wrap_buttons}
{/form}