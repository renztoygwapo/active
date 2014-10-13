/**
 * Created on property handler
 */
App.Inspector.Properties.Label = function (object, client_interface) {
  var wrapper = $(this);
  
  if(client_interface == 'printer') {

	  if (object.label && object.label.id) {
	    var check_string = object.label.id + App.clean(object.label.name);
	  } else {
	    var check_string = 'no_label';
	  } // if
	  
	  if (wrapper.attr('check_string') == check_string) {
	    return true;
	  } // if
	  
	  wrapper.attr('check_string', check_string);
	  wrapper.empty();
	  wrapper_row = wrapper.parents('div.property:first');
	  
	  if (object.label && object.label.id) {
	    var label_property = $('<span>' + App.clean(object.label.name) + '</span>');
	  } else {
	    var label_property = $('<span>' + App.lang('No Label') + '</span>');
	  } // if
	
	    wrapper.append(label_property);
	    if (object.label && object.label.id) {
	      wrapper_row.show();
	    } else {
	      wrapper_row.hide();      
	    } // if
		  
	} else if (client_interface == 'default') {

      if (object.label && object.label.id) {
        var check_string = object.label.id + App.clean(object.label.name);
      } else {
        var check_string = 'no_label';
      } // if

      if (wrapper.attr('check_string') == check_string) {
        return true;
      } // if

      wrapper.attr('check_string', check_string);
      wrapper_row = wrapper.parents('div.property:first');
      wrapper.empty();

      if (object.label && object.label.id) {
        var label = $(App.Wireframe.Utils.renderLabel(object.label, (object.permissions.can_edit ? object.urls.update_label : null))).appendTo(wrapper);

        if(object.permissions.can_edit) {
          label.attr('title', App.lang('Change Label')).flyoutForm({
            'success_message' : App.lang('Label successfully changed'),
            'success_event' : object.event_names.updated,
            'width' : 'narrow'
          });
        } // if

        wrapper_row.show();
      } else {
        wrapper_row.hide();
        return true;
      } // if
  }

}; // Label property