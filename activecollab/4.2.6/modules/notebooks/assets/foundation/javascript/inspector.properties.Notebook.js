/**
 * Notebook property handler
 */
App.Inspector.Properties.Notebook = function (object, client_interface) {
  var wrapper = $(this);
  
  if (object.notebook && object.notebook.id) {
    var check_string = object.notebook.id + App.clean(object.notebook.name);
  } else {
    var check_string = 'no_notebook';
  } // if
  
  if (wrapper.attr('check_string') == check_string) {
    return true;
  } // if
  
  wrapper.attr('check_string', check_string);
  wrapper_row = wrapper.parents('div.property:first');
  wrapper.empty();
  
  if (object.notebook && object.notebook.id) {
    wrapper.append('<a href="' + object.notebook.permalink + '" class="quick_view_item quick_view_item_invert">' + App.clean(object.notebook.name) + '</a>');
  } else {
    wrapper.append('<span>' + App.lang('Notebook not set') + '</span>');
  } // if

  if (object.notebook && object.notebook.id) {
    wrapper_row.show();
  } else {
    wrapper_row.hide();
  } // if
}; // Notebook property