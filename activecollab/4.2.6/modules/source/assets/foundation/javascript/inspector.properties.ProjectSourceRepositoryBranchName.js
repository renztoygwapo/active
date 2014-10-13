/**
 * Simple boolean property handler
 */
App.Inspector.Properties.ProjectSourceRepositoryBranchName = function (object, client_interface, field_name, change_branch_url) {
  var wrapper = $(this);

  // Phone interface
  if(client_interface == 'phone') {
    wrapper.empty().append(App.clean(object[$.trim(field_name)]));

  // Other interfaces
  } else {
    var anchor = $(' <a href="' + change_branch_url + '" title="' + App.lang('Change Branch') + '"><img src=" '+ App.Wireframe.Utils.imageUrl('icons/12x12/change-arrows.png', 'environment') + '" /></a>');

    anchor.flyoutForm({
      'width' : 'narrow',
      'success_event' : 'source_branch_changed',
      'success_message' : App.lang('Branch has been changed')
    });

    wrapper.empty().append(App.clean(object[$.trim(field_name)]) + ' ').append(anchor);
  } // if
}; // SourceCommitCommitedBy property