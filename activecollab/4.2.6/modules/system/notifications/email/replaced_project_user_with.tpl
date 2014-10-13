{lang replaced_with=$replaced_with->getDisplayName(true) project=$context->getName() language=$language}:replaced_with will be Replacing You on ':project' Project{/lang}
================================================================================
{notification_wrapper title='Project Replacement' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang replaced_with=$replaced_with->getDisplayName(true) project_url=$context_view_url project_name=$context->getName() link_style=$style.link language=$language}<u>:replaced_with</u> will be replacing you on "<a href=":project_url" style=":link_style">:project_name</a>" project{/lang}.</p>
{/notification_wrapper}