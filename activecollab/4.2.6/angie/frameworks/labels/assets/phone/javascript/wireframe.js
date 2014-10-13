/**
 * Reusable labels related functionality 
 */

/**
 * Render label
 * 
 * @param Object label
 * @return string
 */
App.Wireframe.Utils['renderLabel'] = function(label, update_url) {
  if(typeof(label) == 'object' && label) {
    return '<span>' + App.clean(label['name']) + '</span>';
  } else {
    return '<span>' + App.lang('No Label') + '</span>';
  } // if
};