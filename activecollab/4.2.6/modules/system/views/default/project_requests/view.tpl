{title lang=false}{$active_project_request->getName()}{/title}
{add_bread_crumb}View{/add_bread_crumb}

{object object=$active_project_request user=$logged_user}
  <div class="wireframe_content_wrapper">{object_comments object=$active_project_request user=$logged_user show_first=yes}</div>
{/object}
<script type="text/javascript">
  var wrapper = $('div.objects_list_details_single_wrapper');
  var save_client = wrapper.find('span.save_client a');
  var can_save_client = {if $logged_user->isPeopleManager() && !($active_project_request->getCompany() instanceof Company)}true{else}false{/if};

  if (save_client && can_save_client) {
    save_client.parent().show();
    save_client.attr('href', '{$active_project_request->getSaveClientUrl()}');
    save_client.flyoutForm({
      'title' : App.lang('Add Client to People'),
      'success_event' : 'project_request_updated',
      'width' : 550
    });
  } // if
</script>