/**
 * Estimate property handler
 */
App.Inspector.Properties.Estimate = function (object, client_interface) {
  var wrapper = $(this);
  
  if(client_interface == App.Wireframe.Utils.INTERFACE_PRINTER) {

	  if (object.estimate && object.estimate.value) {
	    var check_string = object.estimate.value + object.estimate.value;
	  } else {
	    var check_string = 'no_estimate';
	  } // if
	  
	  if (wrapper.attr('check_string') == check_string) {
	    return true;
	  } // if
	  
	  wrapper.attr('check_string', check_string);
	  wrapper.empty();
	  var wrapper_row = wrapper.parents('div.property:first');
	  
	  if (object.estimate && object.estimate.value) {
	    var estimate_property = $('<span>' + App.lang(':value of :job_name', {value : App.Wireframe.Utils.formatEstimate(object.estimate.value), job_name : object.estimate.job_type_name}) + '</span>');
	  } else {
	    var estimate_property = $('<span>' + App.lang('No Estimate') + '</span>');
	  } // if
	
	    wrapper.append(estimate_property);
	    if (object.estimate && object.estimate.value) {
	      wrapper_row.show();
	    } else {
	      wrapper_row.hide();      
	    } // if
		  
	}//if
  

}; // Label property