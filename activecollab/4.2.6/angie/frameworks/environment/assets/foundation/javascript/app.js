/**
 * Converts the newlines and carriage returns into <br />
 *
 * @return String
 */
String.prototype.nl2br = function () {
  return App.nl2br(this);
}; // String.prototype.nl2br

/**
 * Convert & -> &amp; < -> &lt; and > -> &gt;
 *
 *
 * @return string
 */
String.prototype.clean = function (quote_style, charset, double_encode) {
  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Nathan
  // +   bugfixed by: Arno
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +      input by: felix
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: charset argument not supported
  // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
  // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
  // *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
  // *     returns 2: 'ab"c&#039;d'
  // *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
  // *     returns 3: 'my &quot;&entity;&quot; is still here'

  if ((typeof console == 'object') && (typeof console.log == 'function')) {
    console.warn('Usage of .clean() prototype method on strings is DEPRECATED and will be removed in one of activeCollab\'s future versions. Please use App.clean(variable) wrapper instead.');
  } // if

  var string = this + '';

  var optTemp = 0,
      i = 0,
      noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
      quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) { // Put this first to avoid double-encoding
      string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

  var OPTS = {
      'ENT_NOQUOTES': 0,
      'ENT_HTML_QUOTE_SINGLE': 1,
      'ENT_HTML_QUOTE_DOUBLE': 2,
      'ENT_COMPAT': 2,
      'ENT_QUOTES': 3,
      'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
      noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
      quote_style = [].concat(quote_style);
      for (i = 0; i < quote_style.length; i++) {
          // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
          if (OPTS[quote_style[i]] === 0) {
              noquotes = true;
          }
          else if (OPTS[quote_style[i]]) {
              optTemp = optTemp | OPTS[quote_style[i]];
          }
      }
      quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
      string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
      string = string.replace(/"/g, '&quot;');
  }

  return string;
}; // String.prototype.clean

/**
 * Inverse operation of clean
 *
 * @param string
 * @param quote_style
 * @return {String}
 */
String.prototype.unclean = function (quote_style) {
  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Mateusz "loonquawl" Zalega
  // +      input by: ReverseSyntax
  // +      input by: Slawomir Kaniecki
  // +      input by: Scott Cariss
  // +      input by: Francois
  // +   bugfixed by: Onno Marsman
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
  // *     returns 1: '<p>this -> &quot;</p>'
  // *     example 2: htmlspecialchars_decode("&amp;quot;");
  // *     returns 2: '&quot;'

  var string = this + '';

  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined') {
    quote_style = 2;
  }

  string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
    // string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  // Put this in last place to avoid escape being double-decoded
  string = string.replace(/&amp;/g, '&');

  return string;
}

/**
 * Mozilla's (ECMA-262) version of Array.indexOf method, needed for IE compatibility
 */
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function(searchElement /*, fromIndex */) {
    "use strict";

    if (this === void 0 || this === null) {
      throw new TypeError();
    } // if

    var t = Object(this);
    var len = t.length >>> 0;
    if (len === 0) {
      return -1;
    } // if

    var n = 0;
    if (arguments.length > 0) {
      n = Number(arguments[1]);
      if (n !== n) {
        n = 0;
      } else if (n !== 0 && n !== (1 / 0) && n !== -(1 / 0)) {
        n = (n > 0 || -1) * Math.floor(Math.abs(n));
      } // if
    } // if

    if (n >= len) {
      return -1;
    } // if

    var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);

    for (; k < len; k++) {
      if (k in t && t[k] === searchElement) {
        return k;
      } // if
    } // for
    return -1;
  };

} // Array.prototype.indexOf

/**
 * Array Remove - By John Resig (MIT Licensed)
 *
 * Examples, from John's site:
 *
 * array.remove(1); // Remove the second item from the array
 * array.remove(-2); // Remove the second-to-last item from the array
 * array.remove(1,2); // Remove the second and third items from the array
 * array.remove(-2,-1); // Remove the last and second-to-last items from the array
 */
Array.prototype.remove = function(from, to) {
  if(jQuery.isArray(this)) {
    var rest = this.slice((to || from) + 1 || this.length);
    this.length = from < 0 ? this.length + from : from;
    return this.push.apply(this, rest);
  } // if
};

/**
 * Remove element(s) from array by value
 */
Array.prototype.removeByValue= function(){
  var what, a= arguments, L= a.length, ax;
  while(L && this.length){
    what= a[--L];
    while((ax= this.indexOf(what))!= -1){
      this.splice(ax, 1);
    }
  }
  return this;
};

/**
 * Return number of properties in a given object
 * 
 * @param obj
 */
Object.size = function(obj) {
  var size = 0, key;
  for (key in obj) {
    if (obj.hasOwnProperty(key)) {
      size++;
    } // if
  } // for
  return size;
};

var App = window.App || {};

/**
 * Application config storage
 */
App.Config = {
  
  /**
   * Object where we'll store the data
   */
  'data' : {},
  
  /**
   * Return value by a given key in the storage
   * 
   * @param String k
   * @param mixed default_value
   * @return mixed
   */
  'get' : function(k, default_value) {
    if (App.Config['data'][k] === undefined) {
      return default_value;
    } // if

    return App.Config['data'][k];
  },
  
  /**
   * Set value
   * 
   * If k is object, system will set a list of variables and ignore v
   * 
   * @param mixed k
   * @param mixed v
   */
  'set' : function(k, v) {
    switch(typeof(k)) {
      case 'object':
        for(var i in k) {
          App.Config['data'][i] = k[i];
        } // for
        
        break;
      case 'string':
        App.Config['data'][k] = v;
        
        break;
    } // switch
  }, 
  
  /**
   * Reset all the values in the data store
   */
  'reset' : function(data) {
    App.Config['data'] = typeof(data) == 'object' && data ? data : {};
  }
    
};

// variables
App.variables = {
 'z_index' : 900
};

// various global variables
App.getZIndex = function () {
  App.variables.z_index++;
  return App.variables.z_index - 1;
};

// All widgets should be defined here
App.widgets = {};

/**
 * Main nl2br function
 * 
 * @param string
 * @return string
 */
App.nl2br = function (string) {
  return (string + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
};

/**
 * PHP compatible str_replace
 *
 * @param mixed search
 * @param mixed replace
 * @param String subject
 * @param Integer count
 * @returns String
 */
App.strReplace = function (search, replace, subject, count) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Gabriel Paderni
  // +   improved by: Philip Peterson
  // +   improved by: Simon Willison (http://simonwillison.net)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   bugfixed by: Anton Ongson
  // +      input by: Onno Marsman
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    tweaked by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   input by: Oleg Eremeev
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Oleg Eremeev
  // %          note 1: The count parameter must be passed as a string in order
  // %          note 1:  to find a global variable in which the result will be given
  // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
  // *     returns 1: 'Kevin.van.Zonneveld'
  // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
  // *     returns 2: 'hemmo, mars'
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp).split(f[j]).join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}

/**
 * Clean the string
 * 
 * @param string_to_clean
 *
 * @return string
 */
App.clean = function (string_to_clean, quote_style, charset, double_encode) {

  // http://kevin.vanzonneveld.net
  // +   original by: Mirek Slugen
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Nathan
  // +   bugfixed by: Arno
  // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Ratheous
  // +      input by: Mailfaker (http://www.weedem.fr/)
  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +      input by: felix
  // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: charset argument not supported
  // *     example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
  // *     returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
  // *     example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
  // *     returns 2: 'ab"c&#039;d'
  // *     example 3: htmlspecialchars("my "&entity;" is still here", null, null, false);
  // *     returns 3: 'my &quot;&entity;&quot; is still here'

  var string = string_to_clean + '';

  var optTemp = 0,
      i = 0,
      noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
    quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) { // Put this first to avoid double-encoding
    string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      }
      else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
    string = string.replace(/"/g, '&quot;');
  }

  return string;
}; // App.clean

/**
 * Generate slug for string
 *
 * @param String string
 * @param String space
 * @return String
 */
App.slug = function (string, space) {
  if (space === undefined) {
    space = '-';
  } // if

  string = $.trim(string);
  string = string.replace(/[^a-zA-Z0-9 -]/, '');
  string = string.toLowerCase();
  string = string.toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'-');
  string = $.trim(string);

  return string;
} // App.slug

/**
 * Return ture if we have an object that we can go through, or a non-empty array
 * 
 * @param mixed v
 * @return boolean
 */
App.isForeachable = function(v) {
  return (typeof(v) == 'object' && v) || (jQuery.isArray(v) && v.length > 0);
}; // isForeachable

/**
 * Return true if two arrays are equal
 *
 * @param arr1
 * @param arr2
 */
App.array_equal = function(arr1,arr2) {
  return ($(arr1).not(arr2).length == 0 && $(arr2).not(arr1).length == 0);
};//array_equal

/**
 * Function which debugs the provided variable (console.log proxy)
 *
 * @param mixed variable
 */
App.debug = function (variable) {
  if ((typeof console == 'object') && (typeof console.log == 'function')) {
    console.log(variable);
  } // if
};

if (!(typeof console == 'object')) {
  console = {};
} // if

if (!console['log']) {
  console['log'] = function () {};
  console['debug'] = function () {};
  console['error'] = function () {};
} // if

if (!console['groupCollapsed']) {
  console['groupCollapsed'] = function () {};
  console['groupEnd'] = function () {};
} // if

/**
 * Send post request to specific link
 *
 * @param string the_link
 */
App.postLink = function(the_link) {
  var form = $(document.createElement('form'));
  form.attr({
    'action' : the_link,
    'method' : 'post'
  });
  
  var submitted_field = $(document.createElement('input'));
  submitted_field.attr({
    'type'  : 'hidden',
    'name'  : 'submitted',
    'value' : 'submitted'
  });
  
  form.append(submitted_field);
  
  $('body').append(form);
  
  form.submit();
  
  //IE 7 & 8 FIX ///
  if (typeof event !== 'undefined') {
	  event.returnValue=false;
  } //if
  //////////
  
  return false;
};

/**
 * JS version of lang function / helper
 *
 * @param string content
 * @param object params
 */
App.lang = function(content, params) {
  if(typeof(content) == 'string') {
    var translation = content;

    if(typeof(App.langs) == 'object') {
      if(App.langs[content]) {
        translation = App.langs[content];
      } // if
    } // if

    if(typeof params == 'object') {
      for(key in params) {
        if(typeof(params[key]) == 'string') {
          translation = translation.replace(':' + key, App.clean(params[key]));
        } else if(typeof(params[key]) != 'undefined') {
          if(params[key] === null) {
            params[key] = '';
          } // if

          translation = translation.replace(':' + key, App.clean(params[key].toString()));
        } else {
          throw "App.lang(): '" + key + "' not found while preparing '" + content + "'";
        } // if
      } // if
    } // if

    return translation;
  } else {
    return content;
  } // if
};

/**
 * JavaScript implementation of isset() function
 *
 * Usage example:
 *
 * if(isset(undefined, true) || isset('Something')) {
 *   // Do stuff
 * }
 *
 * @param value
 * @return boolean
 */
App.isset = function(value) {
  return !(typeof(value) == 'undefined' || value === null);
};

/**
 * Convert MySQL formatted datetime string to Date() object
 *
 * @params String timestamp
 * @return Date
 */
App.mysqlToDate = function(timestamp) {
  var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
  var parts=timestamp.replace(regex, "$1 $2 $3 $4 $5 $6").split(' ');
  return new Date(parts[0], parts[1], parts[2], parts[3], parts[4], parts[5]);
};

/**
 * Checks if value is today
 * 
 * @param Mixed value
 * @return boolean
 */
App.isToday = function(value) {
  if(typeof(value) == 'object' && value && typeof(value['timestamp']) == 'number') {
    var check_date = new Date(value['timestamp'] * 1000);
  } else {
    var check_date = new Date(value);
  } // if
  
  return check_date.is().today();
};

/**
 * Checks if value is yesterday
 * 
 * @param Mixed value
 * @return boolean
 */
App.isYesterday = function(value) {
  if(typeof(value) == 'object' && value && typeof(value['timestamp']) == 'number') {
    var check_date = new Date(value['timestamp'] * 1000);
  } else {
    var check_date = new Date(value);
  } // if
  
  return check_date.same().day(Date.parse('t - 1 d'));
};

/**
 * Attach more parameters to URL
 *
 * extend_with can be object or a serialized string
 *
 * @param string url
 * @param mixed extend_with
 */
App.extendUrl = function(url, extend_with) {
  if(!url || !extend_with) {
    return url;
  } // if
  
  var extended_url = url.indexOf('?') < 0 ? url + '?' : url + '&';
  
  // Extend with array
  if(typeof(extend_with) == 'object') {
    var parameters = [];
    
    for(var i in extend_with) {
      if(typeof(extend_with[i]) == 'object') {
        for(var j in extend_with[i]) {
          parameters.push(i + '[' + j + ']' + '=' + extend_with[i][j]);
        } // for
      } else {
        parameters.push(i + '=' + extend_with[i]);
      } // if
    } // for
    
    return extended_url + parameters.join('&');
    
  // Extend with string (serialized?)
  } else {
    return extended_url + extend_with;
  } // if
};

/**
 * Parse numeric value and return integer or float
 *
 * @param String value
 * @return mixed
 */
App.parseNumeric = function(value) {
  if(typeof(value) == 'number') {
    return value;
  } else if(typeof(value) == 'string') {
    
    var point_pos = value.lastIndexOf('.');
    var comma_pos = value.lastIndexOf(',');

    if (point_pos != -1 && comma_pos != -1) {
      if (point_pos > comma_pos) {
        return parseFloat(value.replace(/\,/g, ''));
      } else {
        var result = '';
        value = value.replace(/\,/g, '.');
        
        for (var i=0; i < value.length; i++) {
          if(value[i] == '.' && i != comma_pos) {
            continue;
          } // if
          
          result += value[i];
        } // for
        
        return parseFloat(result);
      } // if
    } else if (comma_pos > -1) {
      return parseFloat(value.replace(/\,/g, '.'));
    } else {
      return parseFloat(value);
    } // if
  } else {
    return NaN;
  } // if
};

/**
 * Percent of total
 *
 * @param value
 * @param total
 * @return {String}
 */
App.percentOfTotal = function(value, total) {
  if(value > total) {
    return '100%';
  } else {
    if(value == 0) {
      return total > 0 ? '0%' : '100%';
    } else {
      return Math.round(value * 100 / total) + '%';
    } // if
  } // if
}; // percentOfTotal

function trim (str, charlist) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: mdsjack (http://www.mdsjack.bo.it)
  // +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
  // +      input by: Erkekjetter
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      input by: DxGx
  // +   improved by: Steven Levithan (http://blog.stevenlevithan.com)
  // +    tweaked by: Jack
  // +   bugfixed by: Onno Marsman
  // *     example 1: trim('    Kevin van Zonneveld    ');
  // *     returns 1: 'Kevin van Zonneveld'
  // *     example 2: trim('Hello World', 'Hdle');
  // *     returns 2: 'o Wor'
  // *     example 3: trim(16, 1);
  // *     returns 3: 6
  var whitespace, l = 0,
    i = 0;
  str += '';

  if (!charlist) {
    // default list
    whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
  } else {
    // preg_quote custom list
    charlist += '';
    whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  }

  l = str.length;
  for (i = 0; i < l; i++) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(i);
      break;
    }
  }

  l = str.length;
  for (i = l - 1; i >= 0; i--) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str = str.substring(0, i + 1);
      break;
    }
  }

  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}

function rtrim (str, charlist) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      input by: Erkekjetter
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +   input by: rem
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: rtrim('    Kevin van Zonneveld    ');
  // *     returns 1: '    Kevin van Zonneveld'
  charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
  var re = new RegExp('[' + charlist + ']+$', 'g');
  return (str + '').replace(re, '');
}

/**
 * PHP JS implementation of number_format function
 *
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 * @return {String}
 */
function number_format (number, decimals, dec_point, thousands_sep) {
  // http://kevin.vanzonneveld.net
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +     bugfix by: Michael White (http://getsprink.com)
  // +     bugfix by: Benjamin Lupton
  // +     bugfix by: Allan Jensen (http://www.winternet.no)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +     bugfix by: Howard Yeend
  // +    revised by: Luke Smith (http://lucassmith.name)
  // +     bugfix by: Diogo Resende
  // +     bugfix by: Rival
  // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
  // +   improved by: davook
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Jay Klehr
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Amir Habibi (http://www.residence-mixte.com/)
  // +     bugfix by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Theriault
  // +      input by: Amirouche
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // *     example 1: number_format(1234.56);
  // *     returns 1: '1,235'
  // *     example 2: number_format(1234.56, 2, ',', ' ');
  // *     returns 2: '1 234,56'
  // *     example 3: number_format(1234.5678, 2, '.', '');
  // *     returns 3: '1234.57'
  // *     example 4: number_format(67, 2, ',', '.');
  // *     returns 4: '67,00'
  // *     example 5: number_format(1000);
  // *     returns 5: '1,000'
  // *     example 6: number_format(67.311, 2);
  // *     returns 6: '67.31'
  // *     example 7: number_format(1000.55, 1);
  // *     returns 7: '1,000.6'
  // *     example 8: number_format(67000, 5, ',', '.');
  // *     returns 8: '67.000,00000'
  // *     example 9: number_format(0.9, 0);
  // *     returns 9: '1'
  // *    example 10: number_format('1.20', 2);
  // *    returns 10: '1.20'
  // *    example 11: number_format('1.20', 4);
  // *    returns 11: '1.2000'
  // *    example 12: number_format('1.2000', 3);
  // *    returns 12: '1.200'
  // *    example 13: number_format('1 000,50', 2, '.', ' ');
  // *    returns 13: '100 050.00'
  // Strip all characters but numerical ones.
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

/**
 * Round number and return it rounded to certain number of decimals
 * 
 * @param Number value
 * @param Number decimals
 * @param String decimal_separator
 * @param String thousands_separator
 * @return String
 */
App.numberFormat = function (value, language, decimal_spaces, remove_zeros) {
  decimal_spaces = typeof(decimal_spaces) == 'undefined' ? 2 : decimal_spaces;

  if (language) {
    var decimal_separator = language['decimal_separator'];
    var thousands_separator = language['thousands_separator'];
  } else {
    var decimal_separator = App.Config.get('decimal_separator', '.');
    var thousands_separator = App.Config.get('thousands_separator', ',');
  } // if

  var formatted = number_format(value, decimal_spaces, decimal_separator, thousands_separator);

  if(typeof(remove_zeros) != 'undefined' && remove_zeros) {
    formatted = rtrim(formatted, '0');
    formatted = rtrim(formatted, decimal_separator);

    if(formatted == '') {
      formatted = '0';
    } else {

    } // if
  } // if

  return formatted;
}; // App.numberFormat


/**
 * Custom number format
 *
 * @param float value
 * @param Number decimal_spaces
 * @param String decimal_separator
 * @param String thousands_separator
 */
App.customNumberFormat = function (value, decimal_spaces, decimal_separator, thousands_separator) {
  decimal_spaces = typeof(decimal_spaces) == 'undefined' ? 2 : decimal_spaces;
  decimal_separator = typeof(decimal_separator) == 'undefined' ? '.' : decimal_separator;
  thousands_separator = typeof(thousands_separator) == 'undefined' ? ',' : thousands_separator;
  return number_format(value, decimal_spaces, decimal_separator, thousands_separator);
} // App.customNumberFormat

/**
 * Return formatted money amount
 * 
 * @param Number value
 * @param Object currency
 * @return String
 */
App.moneyFormat = function (value, currency, language, show_currency_code, round) {
  if (!currency) {
    currency = App.Config.get('default_currency');
  } // if

  if (round && currency['decimal_rounding'] > 0) {
    var rounding_step = 1 / currency['decimal_rounding'];
    value = Math.round(value * rounding_step) / rounding_step;
  } // if

  // formatted
  var formatted = App.numberFormat(value, language, currency['decimal_spaces']);

  if (show_currency_code) {
    if (currency['code'] == '$') {
      formatted = currency['code'] + formatted;
    } else if (currency['code'] == 'USD') {
      formatted = currency['code'] + ' ' + formatted;
    } else {
      formatted = formatted + ' ' + currency['code'];
    } // if
  } // if

  return formatted;
}; // App.moneyFormat

/**
 * Format hours value
 *
 * If decimal is TRUE, system will just format decimal value, without converting it to HH:MM format
 *
 * @param Float value
 * @param Boolean decimal
 * @param Boolean include_h
 * @param Boolean remove_zeros
 * @return string
 */
App.hoursFormat = function(value, decimal, include_h, remove_zeros) {
  if(typeof(decimal) == 'undefined') {
    decimal = true;
  } // if
  
  var sufix = decimal && (typeof(include_h) == 'undefined' || include_h) ? 'h' : '';
  
  if(decimal) {
    if(typeof(remove_zeros) == 'undefined') {
      remove_zeros = true;
    } // if

    return App.numberFormat(value, null, 2, remove_zeros) + sufix;
  } else {
    var hours = Math.round(value * 100) / 100;
    var seconds = Math.round((hours % 1) * 100);
    
    if(seconds == 0) {
      return Math.floor(hours) + ':00';
    } else {
      return Math.floor(hours) + ':' + App.numberFormat((seconds / 100) * 60, 2);
    } // if
  } // if
}; // hoursFormat

/**
 * Format license key
 *
 * @param String license_key
 * @param String license_uid
 * @param Number numbers_in_group
 * @return {String}
 */
App.formatLicenseKey = function (license_key, license_uid, numbers_in_group) {
  if (!numbers_in_group) {
    numbers_in_group = 6;
  } // if

  var formatted_license_key = '';
  for (var x = 0; x < Math.ceil(license_key.length / numbers_in_group); x++) {
    formatted_license_key += license_key.substring(x * numbers_in_group, (x + 1) * numbers_in_group) + '-';
  } // fors
  formatted_license_key += license_uid;

  return formatted_license_key;
}; // formatLicenseKey

/**
 * Parse string and return version object
 *
 * @param String str
 * @return Object
 */
App.parseVersionString = function (str) {
    if (typeof(str) != 'string') { return false; }
    var x = str.split('.');
    // parse from string or default to 0 if can't parse
    var maj = parseInt(x[0]) || 0;
    var min = parseInt(x[1]) || 0;
    var pat = parseInt(x[2]) || 0;
    return {
        major: maj,
        minor: min,
        patch: pat
    };
}; // parseVersionString

/**
 * compare versions, if they are same returns 0, if first is lower returns -1, and
 * if second is lower returns 1
 *
 * @var string version1
 * @var string version2
 * @return int
 */
App.compareVersions = function (version1, version2) {
  version1 = App.parseVersionString(version1);
  version2 = App.parseVersionString(version2);
    
  if (version1.major < version2.major) {
    return -1;
  } else if (version1.major > version2.major) {
    return 1;
  } else {
    if (version1.minor < version2.minor) {
      return -1;
    } else if (version1.minor > version2.minor) {
      return 1;
    } else {
      if (version1.patch < version2.patch) {
        return -1;
      } if (version1.patch > version2.patch) {
        return 1;
      } else {
        return 0;
      } // if
    } // if
  } // if
}; // compareVersions

/**
 * Get Application Update Status Code
 *
 * @param string current_version
 * @param string latest_version
 * @param string latest_available_version
 * @return Object
 *    (-1)  - Development Version
 *    (0)   - We haven't checked server for new version
 *    (1)   - Application is up to date
 *    (2)   - Application is ahead of latest release
 *    (3)   - There is a new version, and you have access to it
 *    (4)   - There is a new version, and you don't have access to it
 */
App.getApplicationUpdateStatusCode = function (current_version, latest_version, latest_available_version) {
  // development version
  if (current_version == 'current') {
    return {
      'code' : -1,
      'message' : App.lang('Development version')
    };
  } // if

  // not checked yet
  if (!latest_version) {
    return {
      'code' : 0,
      'message' : App.lang('Not checked for the latest version yet')
    };
  } // if

  // application is up to date
  if (App.compareVersions(current_version, latest_version) == 0) {
    return {
      'code' : 1,
      'message' : App.lang('Your application is up to date')
    };
  } // if

  // we are ahead of latest release
  if (App.compareVersions(current_version, latest_version) == 1) {
    return {
      'code' : 2,
      'message' : App.lang('Your application is ahead of latest release')
    };
  } // if

  // there is a new available version
  if (App.compareVersions(current_version, latest_available_version) == -1) {
    return {
      'code' : 3,
      'message' : App.lang('New version available (:new_version)', { 'new_version' : latest_available_version })
    };
  } // if

  // there is a new available version but
  if (App.compareVersions(current_version, latest_version) == -1) {
    return {
      'code' : 4,
      'message' : App.lang('New version available (:new_version)', { 'new_version' : latest_version })
    };
  } // if
} // getApplicationUpdateStatusCode

/**
 * Uppercase first letter
 *
 * @param String str
 * @return String
 */
App.ucfirst = function(str) {
  str += '';
  return str.charAt(0).toUpperCase() + str.substr(1);
}; // ucfirst

/**
 * Makes a excerption of a string
 *
 * @param String string
 * @param Number max_length
 * @param String etc_string
 * @return String
 */
App.excerpt = function (string, max_length, etc_string) {
  if(typeof(string) == 'string') {
    if (max_length == undefined) {
      max_length = 100;
    } // if

    if (etc_string == undefined) {
      etc_string = '...';
    } // if

    return string.length <= max_length + (etc_string.length) ? string : (string.substring(0, max_length) + etc_string);
  } else {
    return string;
  } // if
};

/**
 * Returns input string padded on the left or right to specified length with pad_string
 *
 * http://kevin.vanzonneveld.net
 *  original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 *  namespaced by: Michael White (http://getsprink.com)
 *  input by: Marco van Oort
 *  bugfixed by: Brett Zamir (http://brett-zamir.me)
 * @param input
 * @param pad_length
 * @param pad_string
 * @param pad_type
 * @return {*}
 */
App.strPad = function (input, pad_length, pad_string, pad_type) {
  var half = '',
    pad_to_go;

  var str_pad_repeater = function (s, len) {
    var collect = '',
      i;

    while (collect.length < len) {
      collect += s;
    }
    collect = collect.substr(0, len);

    return collect;
  };

  input += '';
  pad_string = pad_string !== undefined ? pad_string : ' ';

  if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') {
    pad_type = 'STR_PAD_RIGHT';
  }
  if ((pad_to_go = pad_length - input.length) > 0) {
    if (pad_type == 'STR_PAD_LEFT') {
      input = str_pad_repeater(pad_string, pad_to_go) + input;
    } else if (pad_type == 'STR_PAD_RIGHT') {
      input = input + str_pad_repeater(pad_string, pad_to_go);
    } else if (pad_type == 'STR_PAD_BOTH') {
      half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
      input = half + input + half;
      input = input.substr(0, pad_length);
    }
  }

  return input;
}

/**
 * Format file size
 * 
 * @param Number size
 * @param boolean remove_zeros
 * @return String
 */
App.formatFileSize = function (size, remove_zeros) {
  var data = {
    'TB' : 1099511627776,
    'GB' : 1073741824,
    'MB' : 1048576,
    'kb' : 1024
  };
  var in_unit = 0;

  if (remove_zeros == undefined) {
    remove_zeros = false;
  } // if

  var return_string = size + 'b';
  size = parseInt(size);
  $.each(data, function (unit, bytes) {
    in_unit = size/bytes;
    if (in_unit > 0.9) {
      return_string = App.numberFormat(in_unit, 2);
      if(remove_zeros) {
        var decimal_separator = App.Config.get('decimal_separator', '.');
        return_string = rtrim(return_string, '0');
        return_string = rtrim(return_string, decimal_separator);
      } //if
      return_string += unit;
      return false;
    } // if
  });
  
  return return_string;
};

/**
 * Get available file types
 *
 * @param null
 * @return Array
 */
App.getAvailableFileTypes = function() {
  return [
    '7zip',
    'aac',
    'ac3',
    'ace',
    'ai',
    'aiff',
    'avi',
    'bin',
    'bmp',
    'cloud',
    'css',
    'csv',
    'divx',
    'dll',
    'doc',
    'docx',
    'download',
    'empty',
    'exe',
    'fla',
    'flash',
    'flv',
    'font',
    'gif',
    'html',
    'ini',
    'iso',
    'java',
    'jpeg',
    'jpg',
    'js',
    'm4a',
    'mov',
    'mp3',
    'mp4',
    'mpeg',
    'pdf',
    'php',
    'png',
    'ppt',
    'pptx',
    'prgrs',
    'psd',
    'rar',
    'rtf',
    'ruby',
    'sql',
    'swf',
    'tif',
    'tiff',
    'txt',
    'unknown',
    'vob',
    'wav',
    'wma',
    'wmv',
    'xls',
    'xlsx',
    'xml',
    'zip'
  ];
};

/**
 * Check if url is valid URL
 * 
 * @param String url
 * @returns boolean
 */
App.isValidUrl = function (url) {
//  return url.match(/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i);
  // inserted better regexp for url validation
  var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
  return regexp.test(url);
}; // App.isValidUrl

/**
 * Checks if email is valid email address
 * 
 * @param String email
 * @return boolean
 */
App.isValidEmail = function (email) {
  return typeof(email) == 'string' ? email.toLowerCase().match(/^[_+a-z0-9-!#$%&'*/=?^_`{|}~]+(?:\.[_+a-z0-9-!#$%&'*/=?^_`{|}~]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/) : false;
}; // App.isValidEmail

App.EmailObject = function() {
  return {
    init : function (object_id) {
      var email_object = $('#'+object_id);
      var blockquotes = email_object.find('>blockquote');
      blockquotes.each(function () {
        var blockquote = $(this);
        if (!blockquote.parent().is('div.content')) {
          blockquote = blockquote.parent();
        } // if
        blockquote.before('<a href="#" class="hidden_history">' + App.lang('Hidden Email History') + '</a>');
        blockquote.hide();
        var blockquote_anchor = blockquote.prev();
        
        blockquote_anchor.click(function () {
          blockquote.slideDown();
          $(this).remove();
          return false;
        });
      });
    }
  };
}();

App.noWeekendsAndDaysOff = function (date) {
  if (jQuery.inArray(date.getDay(), App.Config.get('work_days')) == -1) {
    return [false, null, App.lang('Weekend')]; // if it's a non work days, then don't do it
  }; // if

  var days_off = App.Config.get('days_off');
  var date_id = date.getDate() + '/' + (date.getMonth() + 1);
  var year = date.getFullYear();


  if (typeof(days_off[date_id]) != 'undefined') {

    // Day off that repeats yearly
    if (days_off[date_id]['repeat'] && year >= days_off[date_id]['year']) {
      return [false, null, days_off[date_id]['name']];

    // One occurance, this year
    } else if (days_off[date_id]['year'] == year) {
      return [false, null, days_off[date_id]['name']];
    } // if

  } // if
  
  return [true];
};

/**
 * Iterate over an array or over an object
 *
 * @param data
 * @param callback
 * @return {*}
 */
App.each = function(data, callback) {

  // Map instance
  if(data instanceof App.Map) {
    return data.each(callback);

  // Array that can be loaded into map
  } else if(App.isMappable(data)) {
    var map = new App.Map(data);

    return map.each(callback);

  // Everything else
  } else {
    return jQuery.each(data, callback);
  } // if

}; // each

/**
 * Returns true if data can be converted to map
 *
 * @param data
 * @return boolean
 */
App.isMappable = function(data) {
  return jQuery.isArray(data) && typeof(data[0]) == 'object' && typeof(data[0]['__k']) != 'undefined' && typeof(data[0]['__v']) != 'undefined';
}; // if

/**
 * Namespace for delegates
 */
App.Delegates = {};

/**
 * Check if application is in development mode
 * 
 * @return Boolean
 */
App.isInDevelopment = function () {
  return App.Config.get('application_mode') == 'in_development';
};

/**
 * Check if application is in debug mode
 * 
 * @return Boolean
 */
App.isInDebugMode = function () {
  return App.Config.get('application_mode') == 'in_debug_mode';
};

/**
 * Check if application is in production mode
 * 
 * @return Boolean
 */
App.isInProduction = function () { 
  return !(App.isInDevelopment() || App.isInDebugMode());
};

/**
 * Interface used to interact with client side storage
 */
App.Storage = {

  /**
   * Return all values that are stored
   *
   * @return Array
   */
  'index' : function() {
    return $.jStorage.index();
  },

  /**
   * Return object from storage, or default value if value was not found
   *
   * @param key
   * @param default_value
   * @return mixed
   */
  'get' : function(key, default_value) {
    return $.jStorage.get(key, default_value);
  },

  /**
   * Add value to the storage
   *
   * @param String key
   * @param Mixed value
   * @param Integer options
   */
  'set' : function(key, value, options) {
    $.jStorage.set(key, value, options);
  },

  /**
   * Remove value from the storage
   *
   * @param Storage key
   */
  'remove' : function(key) {
    $.jStorage.deleteKey(key);
  },

  /**
   * Clear storage
   */
  'clear' : function() {
    $.jStorage.flush();
  },

  /**
   * Listen to key change
   *
   * @param String key
   * @param Function callback
   */
  'listen' : function(key, callback) {
    $.jStorage.listenKeyChange(key, callback);
  },

  /**
   * Stop listening for key change
   *
   * If callback is set, only the used callback will be cleared, otherwise all listeners will be dropped.
   *
   * @param key
   * @param callback
   */
  'stopListening' : function(key, callback) {
    $.jStorage.stopListening(key, callback);
  },

  /**
   * Return storage size
   */
  'size' : function() {
    return $.jStorage.storageSize();
  }

};

/**
 * Determine if we can navigate away
 *
 * @return boolean
 */
App.canNavigateAway = function () {
  // reset global variables
  window.prevent_navigation = false;
  window.target_form = '';

  // trigger event
  App.Wireframe.Events.trigger('navigate_away');

  if (window.prevent_navigation && !confirm(App.lang("Are you sure that you want to discard changes you've made in :target_form?", {'target_form' : window.target_form}))) {
    return false;
  } // if

  return true;
}; // App.canNavigateAway

/**
 * Check if current browser has support for touch
 *
 * @return boolean
 */
App.isTouchDevice = function () {
  return !!('ontouchstart' in window) ? 1 : 0;
}; // App.isTouchDevice

/**
 * Control tower version info
 *
 * @param settings
 */
App.widgets.controlTowerVersionInfo = function (settings) {
  var application_update_status = App.getApplicationUpdateStatusCode(settings['current_version'], settings['latest_version'], settings['latest_available_version']);
  var application_update_status_code = application_update_status['code'];
  var wrapper = $('#' + settings['id']);
  var update_now_url = settings['update_now_url'];

  wrapper.append('<p class="control_tower_version_title">' + App.lang('activeCollab version') + '</p>');

  // development version
  if (application_update_status_code == -1) {
    wrapper.append('<p class="control_tower_version_bubble_wrapper"><span class="control_tower_version_bubble development">' + App.lang('Development') + '</span></p>');
    return true;
  } // if

  // if we haven't checked server for new version, hide the wrapper
  if (application_update_status_code == 0) {
    wrapper.hide();
    return true;
  } // if

  // if there is a new version
  if (application_update_status_code == 3 || application_update_status_code == 4) {
    var link_id = 'check_for_new_version_' + $.now();

    wrapper.append('<p class="control_tower_version_bubble_wrapper"><a class="control_tower_version_bubble new_version_available menu_navigation_item" href="#" id="' + link_id + '"></a></p>');
    wrapper.append('<p class="control_tower_version_bubble_title">' + App.lang('New version available') + '</p>');
    wrapper.append('<p class="control_tower_version_updated_on"><a href="' + $('#global_whats_new a:first').attr('href') + '">' + App.lang('See what\'s new') + '</a></p>');

    var link = $('#' + link_id);
    var flyout_width = null;

    if (application_update_status_code == 3) {
      link.attr('href', settings['update_url']);
      link.attr('title', App.lang('Update activeCollab'));
      link.html(settings['latest_available_version']);
    } else {
      flyout_width = 'narrow';
      link.attr('href', settings['new_version_details_url']);
      link.attr('title', App.lang('New Version Details'));
      link.html(settings['latest_version']);
    } // if

    wrapper.append('<p class="control_tower_version_updated_on">' + App.lang('Updated On: :date', {'date' : settings['license_details_updated_on']['formatted']}) + '</p>');
    wrapper.append('<p class="control_tower_version_refresh"><a href="' + update_now_url + '">' + App.lang('Check Now') + '</a></p>');

    // initialize flyout
    link.flyout({
      'width' : flyout_width,
      'success' : function () {
        $('#context_popup.statusbar_item_control_tower_popup').remove();
      }
    });
    return true;
  } // if

  // if there are new modules available
  var new_modules = settings['new_modules_available'];

  if (new_modules && (typeof(new_modules) == 'object') && (new_modules.length > 0)) {
    var link_id = 'check_for_new_version_' + $.now();
    wrapper.append('<p class="control_tower_version_bubble_wrapper"><a class="control_tower_version_bubble new_version_available menu_navigation_item" href="' + settings['update_url'] + '" id="' + link_id + '" title="' + App.lang('Update activeCollab') + '">' + App.lang('New Modules') +  '</a></p>');

    $('#' + link_id).flyout({
      'success' : function () {
        $('#context_popup.statusbar_item_control_tower_popup').remove();
      }
    });

    wrapper.append('<p class="control_tower_version_bubble_title">' + App.lang('New modules available') + '</p>');
    wrapper.append('<p class="control_tower_version_updated_on">' + App.lang('Updated On :date', {'date' : settings['license_details_updated_on']['formatted']}) + '</p>');
    wrapper.append('<p class="control_tower_version_refresh"><a href="' + update_now_url + '">' + App.lang('Check Now') + '</a></p>');
    return true;
  } // if

  // if we are running latest version
  if (application_update_status_code == 1 || application_update_status_code == 2) {
    wrapper.append('<p class="control_tower_version_bubble_wrapper"><span class="control_tower_version_bubble running_latest">' + settings['current_version'] + '</span></p>');
    wrapper.append('<p class="control_tower_version_updated_on">' + App.lang('Updated On :date', {'date' : settings['license_details_updated_on']['formatted']}) + '</p>');
    wrapper.append('<p class="control_tower_version_refresh"><a href="' + update_now_url + '">' + App.lang('Check Now') + '</a></p>');

    return true;
  } // if

}; // controlTowerVersionInfo

window.main_javascript_loaded = true;