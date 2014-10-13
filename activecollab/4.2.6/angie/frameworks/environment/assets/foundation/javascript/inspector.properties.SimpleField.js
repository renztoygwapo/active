/**
 * Simple permalink property handler
 */
App.Inspector.Properties.SimpleField = function (object, client_interface, content_field, additional) {
  var wrapper = $(this);
  
  var prefix = additional.prefix;
  var sufix = additional.sufix;
  var modifier = additional.modifier;
  var label_field = additional.label_field;
  var no_clean = additional.no_clean;
  
  var content = App.Inspector.Utils.getFieldValue(content_field, object);

  if (no_clean) {
    content = content ? content : '';
  } else {
    content = content ? App.clean('' + content) : '';
  } // if

  if (label_field) {
    var label = App.Inspector.Utils.getFieldValue(label_field, object)
  } else {
    var label = '';
  } // if
  label = label ? App.clean('' + label) : '';
  
  if (modifier) {
    if ((typeof(modifier) == 'object') && !$.isEmptyObject(modifier)) {
      // get the function
      var modifier_function;
      eval('modifier_function = ' + modifier[0]);
      
      // replace the function, with first parameter
      modifier[0] = content;
      
      content = modifier_function.apply(this, modifier);
    } else {
      eval('content = ' + modifier + '(content)');
    } // if
  } // if
  
  if (label) {
    var check_string = content + label + '_content';
  } else {
    var check_string = content + '_content';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if  
  wrapper.attr('check_string', check_string);
  
  wrapper_row = wrapper.parents('div.property:first');
  wrapper_label = wrapper_row.find('div.label:first');
  
  if (label) {
    wrapper_label.empty().append(label);
  } // if
  
  if (content) {
    wrapper.empty().append((prefix ? prefix : '') + content + (sufix ? sufix : ''));
    wrapper_row.show();
  } else {
    wrapper.empty().append(content);
    wrapper_row.hide();
  } // if
}; // SimpleField property