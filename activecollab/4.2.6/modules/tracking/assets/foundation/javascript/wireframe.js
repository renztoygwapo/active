/**
 * Reusable tracking functionality 
 */

/**
 * Render human friendly estimate value
 * 
 * @param Float value
 * @param Boolean short_var
 * @return string
 */
App.Wireframe.Utils['formatEstimate'] = function(value, short_var) {
  if (short_var) {
    if (!value) {
      return App.lang(':hours h', { 'hours' : 0 });
    } else if (value < 1) {
      return App.lang(':minutes m', { 'minutes' : Math.round(value * 60) });
    } else {
      return App.lang(':hours h', { 'hours' : value });
    } // if
  } else {
    if (!value) {
      return App.lang(':hours hours', { 'hours' : 0 });
    } else if (value < 1) {
      return App.lang(':minutes minutes', { 'minutes' : Math.round(value * 60) });
    } else if (value == 1) {
      return App.lang(':hours hour', { 'hours' : value });
    } else {
      return App.lang(':hours hours', { 'hours' : value });
    } // if
  } // if
};

/**
 * Parse human friendly estimate value
 * 
 * @param String value
 * @return float
 */
App.Wireframe.Utils['parseEstimate'] = function(value) {
  var parsed = App.parseNumeric(value);
  
  return isNaN(parsed) ? 0 : parsed;
};