/**
 * Related property handler
 */
App.Inspector.Properties.RelatedTasks = function (object, client_interface) {
  var wrapper = $(this);
  var wrapper_row = wrapper.parents('div.property:first');

  // Prepare list of tasks
  var to_append = [];

  if(object['has_related_tasks']) {
    var related_tasks_list = [];

    $.each(object['related_tasks'], function(index, item) {
      if(item['is_completed']) {
        related_tasks_list.push('<del><a href="' + App.clean(item['url']) + '" title="' + App.clean(item['name']) + '" class="quick_view_item quick_view_item_invert">#' + item['task_id'] + '</a></del>');
      } else {
        related_tasks_list.push('<a href="' + App.clean(item['url']) + '" title="' + App.clean(item['name']) + '" class="quick_view_item quick_view_item_invert">#' + item['task_id'] + '</a>');
      } // if
    });

    related_tasks_list = related_tasks_list.join(', ');
  } else {
    var related_tasks_list = '<span>' + App.lang('No related tasks') + '</span>';
  } // if

  var trigger_wrapper = $('<span class="inspector_edit_wrapper"></span>').append(related_tasks_list).appendTo(wrapper);
}; // RelatedTasks