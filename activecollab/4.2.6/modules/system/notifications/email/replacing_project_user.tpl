{lang replacing_user=$replacing_user->getDisplayName(true) project=$context->getName() language=$language}You will be Replacing :replacing_user on ':project' Project{/lang}
================================================================================
{notification_wrapper title='Project Replacement' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  <p>{lang replacing_user=$replacing_user->getDisplayName(true) project_url=$context_view_url project_name=$context->getName() link_style=$style.link language=$language}You will be replacing <u>:replacing_user</u> on "<a href=":project_url" style=":link_style">:project_name</a>" project{/lang}.</p>
  <p>{lang url=$project_assignments_url link_style=$style.link language=$language}<a href=":url" style=":link_style">Click here</a> to see a list of your tasks on this project{/lang}.</p>
{/notification_wrapper}