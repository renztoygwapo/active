{lang language=$language}New project request has been submitted{/lang}
================================================================================
{notification_wrapper title='Request Received' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false}
  <p>{lang created_by_email=$context->getCreatedBy()->getEmail() created_by_name=$context->getCreatedBy()->getName() created_by_company=$context->getCreatedByCompanyName() link_style=$style.link language=$language}<a href="mailto::created_by_email" style=":link_style">:created_by_name</a> of ":created_by_company" has submitted a new project request{/lang}:</p>
  {notification_wrap_body}{$context->getBody() nofilter}{/notification_wrap_body}
  <table style="width: 100%">
    <tr>
      <td style="width: 80px; font-weight: bold;">{lang language=$language}Project{/lang}:</td>
      <td>{$context->getName()}</td>
    </tr>
  {foreach $context->getCustomFields() as $custom_field_key => $custom_field}
    <tr>
      <td style="width: 80px; font-weight: bold;">{$custom_field.label}:</td>
      <td>{if $custom_field.value}{$custom_field.value}{else}--{/if}</td>
    </tr>
  {/foreach}
  </table>
{/notification_wrapper}