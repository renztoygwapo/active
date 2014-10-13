/**
 * Returns the platform on which this JS is being run
 */
jQuery.platform = {
  'windows' : false,
  'mac'     : false,
  'linux'   : false,
  'iphone'  : false,
  'other'   : false
};

var platform = navigator.platform;
if (platform.indexOf('Win') === 0) {
  jQuery.platform.windows = true;
} else if (platform.indexOf('Mac') === 0) {
  jQuery.platform.mac = true;
} else if (platform.indexOf('Linux') === 0) {
  jQuery.platform.linux = true;
} else if (platform.indexOf('iPhone') === 0) {
  jQuery.platform.iphone = true;
} else {
  jQuery.platform.other = true;
} // if