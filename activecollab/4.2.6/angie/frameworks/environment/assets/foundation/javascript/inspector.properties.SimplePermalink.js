/**
 * Simple permalink property handler
 */
App.Inspector.Properties.SimplePermalink = function (object, client_interface, permalink_field, label_field, attributes) {
  var wrapper = $(this);
  
  var permalink = App.Inspector.Utils.getFieldValue(permalink_field, object);
  var label = App.Inspector.Utils.getFieldValue(label_field, object);

  if (permalink && !label) {
    label = permalink;
  } // if
  
  if (permalink) {
    var check_string = App.clean(label) + App.clean(permalink);
  } else {
    var check_string = 'no_link';
  } // if
  
  var link_attributes = [];

  if(typeof(attributes) == 'object' && attributes) {
    var quick_view = typeof(attributes['quick_view']) == 'undefined' || attributes['quick_view'] !== false;

    if(quick_view) {
      if(typeof(attributes['class']) != 'undefined' && attributes['class']) {
        attributes['class'] += ' quick_view_item';
      } else {
        attributes['class'] = 'quick_view_item';
      } // if
    } // if

    App.each(attributes, function (attribute_name, attribute_value) {
      if(attribute_name == 'quick_view') {
        return;
      } // if

      link_attributes.push(attribute_name + '="' + attribute_value + '"');
    });
  } // if
  
  link_attributes = link_attributes.length ? link_attributes.join(' ') : '';

  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if  
  wrapper.attr('check_string', check_string);
  
  var wrapper_row = wrapper.parents('div.property:first');
  
  // if we don't have permalink, hide the row
  if (!permalink) {
    wrapper_row.hide();
    return false;
  } // if
  
  if (App.isValidEmail(permalink)) {
    permalink = 'mailto:' + permalink;
  } // if
  
  wrapper_row.show();

  wrapper.empty().append('<a href="' + App.clean(permalink) + '" ' + link_attributes + '>' + App.clean(label) + '</a>');
};
