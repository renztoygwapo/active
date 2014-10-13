/**
 * Indicator Hyperlink
 */
App.Inspector.Indicators.Hyperlink = function (object, client_interface, url, icon, title, additional) {
  var wrapper = $(this);
  
  wrapper.empty();
  
  var link = $('<a href="' + url + '" title="' + App.clean(title) + '"><img src="' + icon + '" /></a>').appendTo(wrapper);
  
  if (additional && additional.flyout_type) {
    link[additional.flyout_type](additional.flyout_options);
  } else if (additional) {
    $.each(additional, function (attribute_name, attribute_value) {
      link.attr(attribute_name, attribute_value);
    });
  } // if
};