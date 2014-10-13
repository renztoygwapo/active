<?php

  /**
   * This function will return request string relative to dispatch file
   * @TODO: not to be merged to /develop (3.3.x)
   *
   * @return string
   */
  function get_request_string() {
    return substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['PHP_SELF'])));
  } // get_request_string

  /**
   * General purpose functions
   * 
   * This file contains various general purpose functions used for string and 
   * array manipulation, input filtering, ouput cleaning end so on.
   *
   * @package angie.functions
   */

  /**
  * Round number to up value
  * 12.234 => 12.24
  * 12.236 => 12.24
  *
  * @param $value
  * @param int $decimals
  * @return mixed
  */
  function round_up($value, $decimals = 2) {
    $exp = pow(10, $decimals);
    return ceil($value * $exp) / $exp;
  } //round_up

  /**
   * @param mixed $value
   * @param mixed $from
   * @param int $decimals
   * @return float
   */
  function percent($value, $from, $decimals = 2) {
    if ($from === 0) {
      return 0;
    } // if

    return round(($value / $from * 100), (integer) $decimals);
  } // percent
  
  /**
   * Make links clickable
   *
   * @param string $text
   * @return string
   */
  function make_links_clickable($text) {    
    $text = " ".$text;
    // something://else
    $text = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
    // www/ftp.something.com
    $text = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
    // myemail@website.com
    $text = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);
    
    return substr($text, 1);
  } // make_links_clickable

  /**
   * This function will return true only if input string starts with
   * niddle
   *
   * @param string $string Input string
   * @param string $niddle Needle string
   * @return boolean
   */
  function str_starts_with($string, $niddle) {  
    return substr($string, 0, strlen($niddle)) == $niddle;    
  } // end func str_starts with
  
  /**
   * This function will return true only if input string ends with
   * niddle
   *
   * @param string $string Input string
   * @param string $niddle Needle string
   * @return boolean
   */
  function str_ends_with($string, $niddle) {
    return substr($string, strlen($string) - strlen($niddle), strlen($niddle)) == $niddle;
  } // end func str_ends_with
  
  /**
   * Return begining of the string
   *
   * @param string $string
   * @param integer $lenght
   * @param string $etc
   * @return string
   */
  function str_excerpt($string, $lenght = 100, $etc = '...') {
    return strlen_utf($string) <= $lenght + 3 ? $string : substr_utf($string, 0, $lenght) . $etc;
  } // str_excerpt
  
  /**
   * Parse encoded string and return array of parameters
   *
   * @param string $str
   * @return array
   */
  function parse_string($str) {
    $result = null;
    parse_str($str, $result);
    return $result;
  } // parse_string
  
  // str_ireplace implementation
  if (!function_exists('str_ireplace')) {

    /**
     * Replace str_ireplace()
     *
     * This function does not support the $count argument because
     * it cannot be optional in PHP 4 and the performance cost is
     * too great when a count is not necessary.
     *
     * @category    PHP
     * @package     PHP_Compat
     * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
     * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
     * @link        http://php.net/function.str_ireplace
     * @author      Aidan Lister <aidan@php.net>
     * @author      Arpad Ray <arpad@php.net>
     * @version     $Revision: 1.24 $
     * @since       PHP 5
     * @require     PHP 4.0.0 (user_error)
     */
    function str_ireplace($search, $replace, $subject) {
      // Sanity check
      if (is_string($search) && is_array($replace)) {
        user_error('Array to string conversion', E_USER_NOTICE);
        $replace = (string) $replace;
      }

      // If search isn't an array, make it one
      $search = (array) $search;
      $length_search = count($search);

      // build the replace array
      $replace = is_array($replace) ? array_pad($replace, $length_search, '') : array_pad(array(), $length_search, $replace);

      // If subject is not an array, make it one
      $was_string = false;
      if(is_string($subject)) {
        $was_string = true;
        $subject = array ($subject);
      }

      // Prepare the search array
      foreach($search as $search_key => $search_value) {
        $search[$search_key] = '/' . preg_quote($search_value, '/') . '/i';
      }

      // Prepare the replace array (escape backreferences)
      $replace = str_replace(array('\\', '$'), array('\\\\', '\$'), $replace);

      $result = preg_replace($search, $replace, $subject);
      return $was_string ? $result[0] : $result;
    } // str_ireplace
    
  } // if
  
  /**
   * Better nl2br that preserves newlines inside <pre> and <code> blocks
   *
   * @param string $string
   * @return string
   */
  function nl2br_pre($string) {
    $string = nl2br($string);
    $string =  preg_replace('/<pre>(.*?)<\/pre>/ise',"'<pre>' . preg_replace('/(<br \/?>)/is','','\\1') . '</pre>'",$string);
    $string =  preg_replace('/<code>(.*?)<\/code>/ise',"'<code>' . preg_replace('/(<br \/?>)/is','','\\1') . '</code>'",$string);
    return $string;
  } // nl2br_pre
  
  if(!function_exists('http_build_query')) {
    
    /**
      * Generates a URL-encoded query string from the associative (or indexed) array provided.
      *
      * @param array $data
      * @param string $prefix
      * @param string $sep
      * @param string $key
      * @return string
      */
    function http_build_query($data, $prefix = null, $sep = '', $key = '') {
      $ret = array();
      foreach((array)$data as $k => $v) {
        $k = urlencode($k);
        if(is_int($k) && $prefix != null) {
          $k = $prefix.$k;
        } // if
        if(!empty($key)) {
          $k = $key."[".$k."]";
        } // if

        if(is_array($v) || is_object($v)) {
          array_push($ret,http_build_query($v,"",$sep,$k));
        } else {
          array_push($ret,$k."=".urlencode($v));
        } // if
      } // foreach

      if(empty($sep)) {
        $sep = ini_get("arg_separator.output");
      } // if

      return implode($sep, $ret);
    } // http_build_query
  } // if
  
  /**
   * convert backslashes to slashes
   *
   * @param string $path
   * @return string
   */
  function fix_slashes($path) {
    return str_replace("\\", "/", $path);
  } // fix_slashes
  
  /**
   * Return path with trailing slash
   *
   * @param string $path Input path
   * @return string Path with trailing slash
   */
  function with_slash($path) {
    return str_ends_with($path, '/') ? $path : $path . '/';
  } // end func with_slash
  
  /**
   * Remove trailing slash from the end of the path (if exists)
   *
   * @param string $path File path that need to be handled
   * @return string
   */
  function without_slash($path) {
    return str_ends_with($path, '/') ? substr($path, 0, strlen($path) - 1) : $path;
  } // without_slash
  
  /**
   * Replace first $search_for with $replace_with in $in. If $search_for is not found
   * original $in string will be returned...
   *
   * @param string $search_for Search for this string
   * @param string $replace_with Replace it with this value
   * @param string $in Haystack
   * @return string
   */
  function str_replace_first($search_for, $replace_with, $in) {
    $pos = strpos($in, $search_for);
    if($pos === false) {
      return $in;
    } else {
      return substr($in, 0, $pos) . $replace_with . substr($in, $pos + strlen($search_for), strlen($in));
    } // if
  } // str_replace_first

  /**
   * Replace first $search_for with $replace_with in $in. If $search_for is not found
   * original $in string will be returned...
   *
   * @param string $search_for Search for this string
   * @param string $replace_with Replace it with this value
   * @param string $in Haystack
   * @return string
   */
  function str_ireplace_first($search_for, $replace_with, $in) {
    $pos = stripos($in, $search_for);
    if($pos === false) {
      return $in;
    } else {
      return substr($in, 0, $pos) . $replace_with . substr($in, $pos + strlen($search_for), strlen($in));
    } // if
  } // str_replace_first

  /**
   * Make random string
   *
   * @param integer $length
   * @param string $allowed_chars
   * @return string
   */
  function make_string($length = 10, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890') {
    $allowed_chars_len = strlen($allowed_chars);

    if($allowed_chars_len == 1) {
      return str_pad('', $length, $allowed_chars);
    } else {
      $result = '';

      while(strlen($result) < $length) {
        $result .= substr($allowed_chars, rand(0, $allowed_chars_len), 1);
      } // for

      return $result;
    } // if
  } // make_string

  /**
   * PBKDF2 Implementation (described in RFC 2898)
   *
   * Source: http://www.itnewb.com/tutorial/Encrypting-Passwords-with-PHP-for-Storage-Using-the-RSA-PBKDF2-Standard
   *
   * @param string p password
   * @param string s salt
   * @param int c iteration count (use 1000 or higher)
   * @param int kl derived key length
   * @param string a hash algorithm
   * @return string derived key
   */
  function pbkdf2($p, $s, $c, $kl, $a = 'sha256') {
    $hl = strlen(hash($a, null, true)); # Hash length
    $kb = ceil($kl / $hl);              # Key blocks to compute
    $dk = '';                           # Derived key

    # Create key
    for ( $block = 1; $block <= $kb; $block ++ ) {

      # Initial hash for this block
      $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);

      # Perform block iterations
      for ( $i = 1; $i < $c; $i ++ )

        # XOR each iterate
        $ib ^= ($b = hash_hmac($a, $b, $p, true));

      $dk .= $ib; # Append iterated block
    }

    # Return derived key of correct length
    return substr($dk, 0, $kl);
  } // pbkdf2
  
  /**
   * Return formatted float
   * 
   * This function will remove trailing zeros and dot if we have X.00 result
   *
   * @param float $value
   * @param integer $decimals
   * @return string
   */
  function float_format($value, $decimals = 2) {
    $result = number_format($value, $decimals, NUMBER_FORMAT_DEC_SEPARATOR, NUMBER_FORMAT_THOUSANDS_SEPARATOR);
    if(strpos($result, NUMBER_FORMAT_DEC_SEPARATOR) === false) {
      return $result;
    } else {
      return trim(rtrim($result, '0'), '.');
    } // if
  } // float_format
  
  /**
   * Prepare HTML before saving it into database
   *
   * @param string $value
   * @param boolean $purify
   * @return string
   * @deprecated
   */
  function prepare_html($value, $purify = false) {
    $value = trim($value);

    if($value != '') {
      // Remove brs from the end of the string
      if(str_ends_with($value, '<br /><br />')) {
        $value = substr_utf($value, 0, strlen_utf($value) - 12);
      } // if
              
      if($purify) {
        $value = purify_html($value);
      } // if
      
      // Clean up Microsoft Office paste:
      // <p> &lt;!--  /* Font Definitions */--&gt;  </p>
      if(str_starts_with($value, '<p> &lt;!--  /* Font Definitions */')) {
        $value = preg_replace('/(<p>)[\s]+(\&lt;\!--)[\s]+(\/\*)[\s]+(Font)[\s]+(Definitions)[\s]+(\*\/)(.*)(--\&gt\;)[\s]+(<\/p>)/i', '', $value);
      } // if
      
      return str_replace(array('<br>', '<br/>', '<br />'), array("\n", "\n", "\n"), $value);
    } // if
    
    return '';
  } // prepare_html
  
  // ---------------------------------------------------
  //  Input validation
  // ---------------------------------------------------
  
  /**
   * Check if selected email has valid email format
   *
   * @param string $user_email Email address
   * @return boolean
   */
  function is_valid_email($user_email) {
    if(function_exists('filter_var')) {
      return (boolean) filter_var($user_email, FILTER_VALIDATE_EMAIL);
    } else {
      if(strstr($user_email, '@') && strstr($user_email, '.')) {
        return (boolean) preg_match(EMAIL_FORMAT, $user_email);
      } // if
    } // if

    return false;
  } // end func is_valid_email
  
  /**
   * Verify the syntax of the given URL.
   * 
   * - samples
   *    http://127.0.0.1 : valid
   *    http://pero_mara.google.com : valid
   *    http://pero-mara.google.com : valid
   *    https://pero-mara.goo-gle.com/something : valid
   *    http://pero-mara.goo_gle.com/~we_use : valid
   *    http://www.google.com : valid
   *    http://activecollab.dev : valid
   *    http://127.0.0.1/~something : valid
   *    http://127.0.0.1/something : valid
   *    http://333.0.0.1 : invalid
   *    http://dev : invalid
   *    .dev : invalid
   *    activecollab.dev : invalid
   *    http://something : invalid
   *    http://127.0 : invalid
   *
   * @param string $url The URL to verify.
   * @return boolean
   */
  function is_valid_url($url) {
    if(str_starts_with(strtolower($url), 'http://localhost')) {
      return true;
    } else {
      if(function_exists('filter_var')) {
        return filter_var($url, FILTER_VALIDATE_URL);
      } else {
        return preg_match(IP_URL_FORMAT, $url) || preg_match(URL_FORMAT, $url);
      } // if
    } // if
  } // is_valid_url
  
  /**
   * verify that given string is valid ip address
   *
   * @param string $ip_address
   * @return boolean
   */
  function is_valid_ip_address($ip_address) {
    if (preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ip_address)) {
      return true;
    } // if
    return false;
  } // is_valid_ip_address
  
  /**
   * This function will return true if $str is valid function name (made out of 
   * alpha numeric characters + underscore)
   *
   * @param string $str
   * @return boolean
   */
  function is_valid_function_name($str) {
    $check_str = trim($str);
    if($check_str == '') {
      return false; // empty string
    } // if
    
    $first_char = substr_utf($check_str, 0, 1);
    if(is_numeric($first_char)) {
      return false; // first char can't be number
    } // if
    
    return (boolean) preg_match("/^([a-zA-Z0-9_]*)$/", $check_str);
  } // is_valid_function_name
  
  /**
   * Check if specific string is valid hash. Lenght is not checked!
   *
   * @param string $hash
   * @return boolean
   */
  function is_valid_hash($hash) {
    return preg_match("/^([a-f0-9]*)$/", $hash);
  } // is_valid_hash
  
  /**
   * Validate CC number
   * 
   * @param $credit_card_number
   * @return mixed
   */
  function is_valid_cc($credit_card_number) {
    // Get the first digit
    $firstnumber = substr($credit_card_number, 0, 1);
    // Make sure it is the correct amount of digits. Account for dashes being present.
    switch ($firstnumber) {
      case 3:
        if (!preg_match('/^3\d{3}[ \-]?\d{6}[ \-]?\d{5}$/', $credit_card_number)) {
            return lang('This is not a valid American Express card number');
        }
        break;
      case 4:
        if (!preg_match('/^4\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number)) {
            return lang('This is not a valid Visa card number');
        }
        break;
      case 5:
        if (!preg_match('/^5\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number)) {
            return lang('This is not a valid MasterCard card number');
        }
        break;
      case 6:
        if (!preg_match('/^6011[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $credit_card_number)) {
            return lang('This is not a valid Discover card number');
        }
        break;
      default:
        return lang('This is not a valid credit card number');
    } // switch

    // Here's where we use the Luhn Algorithm
    $credit_card_number = str_replace('-', '', $credit_card_number);
    $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
    $sum = 0;
    $last = strlen($credit_card_number) - 1;

    for ($i = 0; $i <= $last; $i++) {
        $sum += $map[$credit_card_number[$last - $i] + ($i & 1) * 10];
    }//for

    if ($sum % 10 != 0) {
        return lang('This is not a valid credit card number');
    }//if

    // If we made it this far the credit card number is in a valid format
    return true;
  }//is_valid_cc
  
  // ---------------------------------------------------
  //  Cleaning
  // ---------------------------------------------------
  
  /**
   * This function will return clean variable info
   *
   * @param mixed $var
   * @param string $indent Indent is used when dumping arrays recursivly
   * @param string $indent_close_bracet Indent close bracket param is used
   *   internaly for array output. It is shorter that var indent for 2 spaces
   * @return mixed
   */
  function clean_var_info($var, $indent = '&nbsp;&nbsp;', $indent_close_bracet = '') {
    if(is_object($var)) {
      return 'Object (class: ' . get_class($var) . ')';
    } elseif(is_resource($var)) {
      return 'Resource (type: ' . get_resource_type($var) . ')';
    } elseif(is_array($var)) {
      $result = 'Array (';
      if(count($var)) {
        foreach($var as $k => $v) {
          $k_for_display = is_integer($k) ? $k : "'" . clean($k) . "'";
          $result .= "\n" . $indent . '[' . $k_for_display . '] => ' . clean_var_info($v, $indent . '&nbsp;&nbsp;', $indent_close_bracet . $indent);
        } // foreach
      } // if
      return $result . "\n$indent_close_bracet)";
    } elseif(is_int($var)) {
      return '(int)' . $var;
    } elseif(is_float($var)) {
      return '(float)' . $var;
    } elseif(is_bool($var)) {
      return $var ? 'true' : 'false';
    } elseif(is_null($var)) {
      return 'NULL';
    } else {
      return "(string) '" . clean($var) . "'";
    } // if
  } // clean_var_info
  
  /**
   * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
   *
   * @param string $str
   * @return string
   * @throws InvalidParamError
   */
  function clean($str) {
    if(is_scalar($str)) { 
      $str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
      $str = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $str);
    
      return $str;
    } elseif($str === null) {
      return '';
    } else {
      throw new InvalidParamError('str', $str, '$str needs to be scalar value');
    } // if
  } // clean
  
  /**
   * Convert entities back to valid characteds
   *
   * @param string $escaped_string
   * @return string
   */
  function undo_htmlspecialchars($escaped_string) {
    $search = array('&amp;', '&lt;', '&gt;', '&quot;');
    $replace = array('&', '<', '>', '"');
    return str_replace($search, $replace, $escaped_string);
  } // undo_htmlspecialchars
  
  // ---------------------------------------------------
  //  Array handling functions
  // ---------------------------------------------------

  /**
   * Check to see are two array equal
   *
   * @param $arr1 array
   * @param $arr2 array
   * @return bool
   */
  function array_equal($arr1, $arr2) {
    return !array_diff($arr1, $arr2) && !array_diff($arr2, $arr1);
  }//array_equal

  /**
   * Is $var foreachable
   * 
   * This function will return true if $var is array or if it can be iterated 
   * over and it is not empty
   *
   * @param mixed $var
   * @return boolean
   */
  function is_foreachable($var) {
    return !empty($var) && (is_array($var) || $var instanceof IteratorAggregate);
  } // is_foreachable
  
  /**
   * Return variable from an array
   * 
   * If field $name does not exists in array this function will return $default
   *
   * @param array $from Hash
   * @param string $name
   * @param mixed $default
   * @param boolean $and_unset
   * @param string $instance_of
   * @return mixed
   */
  function array_var(&$from, $name, $default = null, $and_unset = false, $instance_of = null) {
    if(is_array($from) || (is_object($from) && $from instanceof ArrayAccess)) {
      if($and_unset) {
        if(array_key_exists($name, $from)) {
          $result = $from[$name];
          unset($from[$name]);
          return $result;
        } // if
      } else {
        return array_key_exists($name, $from) ? $from[$name] : $default;
      } // if
    } // if
    
    return $default;
  } // array_var
  
  /**
   * Return required value from array
   * 
   * @param array $from
   * @param string $name
   * @param boolean $and_unset
   * @param string $instance_of
   * @return mixed
   * @throws InvalidParamError
   * @throws InvalidInstanceError
   */
  function array_required_var(&$from, $name, $and_unset = false, $instance_of = null) {
    if((is_array($from) || (is_object($from) && $from instanceof ArrayAccess)) && array_key_exists($name, $from)) {
      if($instance_of !== null && !($from[$name] instanceof $instance_of)) {
        throw new InvalidInstanceError($name, $from[$name], $instance_of);
      } // if
      
      if($and_unset) {
        $result = $from[$name];
        unset($from[$name]);
        return $result;
      } else {
        return $from[$name];
      } // if
    } // if
    
    throw new InvalidParamError('name', $name, "'$name' not found in array");
  } // array_required_var
  
  /**
   * Flattens the array
   * 
   * This function will walk recursivly throug $array and all array values will be appended to $array and removed from
   * subelements. Keys are not preserved (it just returns array indexed form 0 .. count - 1)
   *
   * @param array $array If this value is not array it will be returned as one
   * @return array
   */
  function array_flat($array) {
    if(!is_array($array)) {
      return array($array);
    } // if
    
    $result = array();
    
    foreach($array as $value) {
      if(is_array($value)) {
        $value = array_flat($value);
        foreach($value as $subvalue) {
          $result[] = $subvalue;
        } // if
      } else {
        $result[] = $value;
      } // if
    } // if
    
    return $result;
  } // array_flat
  
  /**
   * Unset elements from array that have a given value
   * 
   * Implemented approach recommended on StackOverflow (answer by zombat):
   * 
   * http://stackoverflow.com/questions/1883421/removing-array-item-by-value
   *  
   * @param array $array
   * @param mixed $value
   * @param boolean $preserve_keys
   */
  function array_remove_by_value(&$array, $value, $preserve_keys = true) {
    if(is_array($array)) {
      $keys = array_keys($array, $value);
      if($keys) {
        foreach($keys as $k) {
          unset($array[$k]);
        } // foreach
      } // if
    } // if
    
    if(!$preserve_keys) {
      $array = array_values($array);
    } // if
  } // array_remove_by_value
  
  /**
   * This function will return $str as an array
   *
   * @param string $str
   * @return array
   */
  function string_to_array($str) {
    if(!is_string($str) || (strlen($str) == 0)) {
      return array();
    } // if
    
    $result = array();
    for($i = 0, $strlen = strlen($str); $i < $strlen; $i++) {
      $result[] = $str[$i];
    } // if
    
    return $result;
  } // string_to_array
  
  /**
   * Extract results of specific method from an array of objects
   * 
   * This method will go through all items of an $array and call $method. 
   * Results will be agregated into one array that will be returned
   *
   * @param array $array
   * @param string $method
   * @param array $arguments
   * @return array
   */
  function objects_array_extract(&$array, $method, $arguments = null) {
    if(is_foreachable($array)) {
      $results = array();
      foreach($array as $element) {
        $call = array($element, $method);
        if(is_callable($call, false)) {
          if(is_array($arguments)) {
            $result = call_user_func_array($call, $arguments);
          } elseif(is_string($arguments)) {
            $result = call_user_func($call, $arguments);
          } else {
            $result = call_user_func($call);
          } // if
          
          $results[] = $result;
        } // if
      } // foreach
      return $results;
    } else {
      return null;
    } // if
  } // objects_array_extract
  
  /**
   * Array to CSV
   * 
   * Every $array record is an array of values that need to be exported in CSV
   *
   * @param array $array
   * @return string
   */
  function array_to_csv($array) {
    if(is_array($array)){
      $result = '';
      
      foreach($array as $value_set) {
        $result .= array_to_csv_row($value_set);
      } // foreach
      
      return $result;
    } else {
      return null;
    } // if
  } // array_to_csv
  
  /**
   * Convert an array to a single CSV row
   * 
   * @param array $value_set
   * @return string
   */
  function array_to_csv_row($value_set) {
    $values = array();
    $separator = defined('DEFAULT_CSV_SEPARATOR') ? DEFAULT_CSV_SEPARATOR : ",";
        
    foreach($value_set as $value) {
      $value = str_replace('"', '""', $value);
    
      if(strpos($value, $separator) !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
        $values[] = '"' . $value . '"';
      } else {
        $values[] = $value;
      } // if
    } // foreach
    
    return implode($separator, $values) . "\n";
  } // array_to_csv_row
  
  /**
   * Returns first element of an array
   * 
   * If $key is true first key will be returned, value otherwise.
   *
   * @param array $arr
   * @param boolean $key
   * @return mixed
   */
  function first($arr, $key = false) {
    foreach($arr as $k => $v) {
      return $key ? $k : $v;
    } // foreach
  } // first

  /**
   * Sort an array by key
   *
   * @param array $array Array to be sorted $array
   * @param string $sortByKey Key by which the array is going to be sorted $sortByKey
   * @param string $order Order by asc/desc $order
   * @param int $type Order type $type
   * @return array Sorted array
   */
  function array_sort_by_key($array, $sortByKey, $order = 'asc', $type = SORT_STRING) {
    foreach ($array as $key=>$value) {
      $temp[$key] = $value[$sortByKey];
    } // foreach
    
    $order == 'asc' ? asort($temp, $type) : arsort($temp, $type);

    $sortedArray = array();
    foreach ($temp as $key=>$value) {
      $sortedArray[] = $array[$key];
    } // foreach
    
    return count($sortedArray) > 0 ? $sortedArray : array();
  } // sort by key
  
  // ---------------------------------------------------
  //  Converters
  // ---------------------------------------------------
  
  /**
   * Cast row data to date value (object of DateValue class)
   *
   * @param mixed $value
   * @return DateValue
   */
  function dateval($value) {
    if(empty($value)) {
      return null;
    } // if
    
    if($value instanceof DateValue) {
      return $value;
    } elseif(is_int($value) || is_string($value)) {
      return new DateValue($value);
    } else {
      return null;
    } // if
  } // dateval
  
  /**
   * Cast raw datetime format (string) to DateTimeValue object
   *
   * @param string $value
   * @return DateTimeValue
   */
  function datetimeval($value) {
    if(empty($value)) {
      return null;
    } // if
    
    if($value instanceof DateTimeValue) {
      return $value;
    } elseif($value instanceof DateValue) {
      return new DateTimeValue($value->toMySQL());
    } elseif(is_int($value) || is_string($value)) {
      return new DateTimeValue($value);
    } else {
      return null;
    } // if
  } // datetimeval

  /**
   * Cast raw datetime format (string) to DateTimeValue object
   *
   * @param string $value
   * @return DateTimeValue
   */
  function timeval($value) {
    if(empty($value)) {
      return null;
    } // if

    return (string) $value;
  } // timeval

  // PHP 5.5 introduces boolval() function
  if(!function_exists('boolval')) {

    /**
     * Cast raw value to boolean value
     *
     * @param mixed $value
     * @return boolean
     */
    function boolval($value) {
      return (boolean) $value;
    } // boolval

  } // if
  
  /**
   * Conver money string to float (culture aware)
   * 
   * @param mixed $value
   * @return float
   */
  function moneyval($value) {
    if(is_float($value) || is_int($value)) {
      return $value;
    } else {
      $point_pos = strrpos($value, '.');
      $comma_pos = strrpos($value, ',');
      
      if($point_pos !== false && $comma_pos !== false) {
        if($point_pos > $comma_pos) {
          return (float) str_replace(',', '', $value);
        } else {
          $result = '';
          $value = str_replace(',', '.', $value);
          
          for($i = 0; $i < strlen($value); $i++) {
            if($value[$i] == '.' && $i != $comma_pos) {
              continue;
            } // if
            
            $result .= $value[$i];
          } // for
          
          return (float) $result;
        } // if
      } elseif($comma_pos) {
        return (float) str_replace(',', '.', $value);
      } else {
        return (float) $value;
      } // if
    } // if
  } // moneyval
  
  // ---------------------------------------------------
  //  Misc functions
  // ---------------------------------------------------
  
  /**
   * Show var dump. pre_var_dump() is used for testing only!
   *
   * @param mixed $var
   */
  function pre_var_dump($var) {
    print "<pre style=\"text-align: left\">\n";
    
    ob_start();
    var_dump($var);
    print clean(ob_get_clean());
    
    print "</pre>\n";
  } // pre_var_dump
  
  /**
   * Return max upload size
   * 
   * This function will check for max upload size and return value in bytes. By default it will compare values of 
   * upload_max_filesize and post_max_size from php.ini, but it can also take additional values provided as arguments 
   * (for instance, if you store data in MySQL database one of the limiting factors can be max_allowed_packet 
   * configuration value). 
   * 
   * Examples:
   * <pre>
   * $max_size = get_max_upload_size(); // check only data from php.ini
   * $max_size = get_max_upload_size(12000, 18000); // take this values into calculation too
   * </pre>
   *
   * @param mixed
   * @return integer
   */
  function get_max_upload_size() {
    static $size = false;
    
    if($size === false) {
      $size = php_config_value_to_bytes(ini_get('upload_max_filesize')); // Start with upload max size
      
      $post_max_size = php_config_value_to_bytes(ini_get('post_max_size'));
      
      if($size > $post_max_size) {
        $size = $post_max_size;
      } // if
    } // if

    return $size;
  } // get_max_upload_size
  
  /**
   * Convert filesize value from php.ini to bytes
   * 
   * Convert PHP config value (2M, 8M, 200K...) to bytes. This function was taken from PHP documentation. $val is string 
   * value that need to be converted
   *
   * @param string $val
   * @return integer
   */
  function php_config_value_to_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
      // The 'G' modifier is available since PHP 5.1.0
      case 'g':
        $val *= 1024;
      case 'm':
        $val *= 1024;
      case 'k':
        $val *= 1024;
    } // if

    return floor((float) $val);
  } // php_config_value_to_bytes

  /**
   * Return last day in a given month
   *
   * @param integer $month
   * @param boolean $leap_year
   * @return int
   */
  function get_last_month_day($month, $leap_year = false) {
    if($month == 2) {
      return $leap_year ? 29 : 28;
    } else {
      return in_array($month, array(1, 3, 5, 7, 8, 10, 12)) ? 31 : 30;
    } // if
  } // get_last_month_day

  /**
   * Get letter map
   *
   * @return array
   */
  function get_letter_map() {
    return array(
      'a' => 'A',
      'b' => 'B',
      'c' => 'C',
      'd' => 'D',
      'e' => 'E',
      'f' => 'F',
      'g' => 'G',
      'h' => 'H',
      'i' => 'I',
      'j' => 'J',
      'k' => 'K',
      'l' => 'L',
      'm' => 'M',
      'n' => 'N',
      'o' => 'O',
      'p' => 'P',
      'q' => 'Q',
      'r' => 'R',
      's' => 'S',
      't' => 'T',
      'u' => 'U',
      'v' => 'V',
      'w' => 'W',
      'x' => 'X',
      'y' => 'Y',
      'z' => 'Z',
      '#' => '#',
    );
  } // get_letter_map
  
  /**
   * Compare $value1 and $value2 with $comparision and return boolean result
   * 
   * Examples:
   * <pre>
   * is_true_statement(1, COMPARE_EQ, 1); // true
   * is_true_statement(1, COMPARE_EQ, 3); // false
   * </pre>
   *
   * @param mixed $value1
   * @param string $comparision
   * @param mixed $value2
   * @return boolean
   */
  function is_true_statement($value1, $comparision = COMPARE_EQ, $value2) {
    switch($comparision) {
      case COMPARE_LT:
        if($value1 < $value2) {
          return true;
        } // if
        break;
      case COMPARE_LE:
        if($value1 <= $value2) {
          return true;
        } // if
        break;
      case COMPARE_GT:
        if($value1 > $value2) {
          return true;
        } // if
        break;
      case COMPARE_GE:
        if($value1 >= $value2) {
          return true;
        } // if
        break;
      case COMPARE_EQ:
        if($value1 == $value2) {
          return true;
        } // if
        break;
      case COMPARE_NE:
        if($value1 != $value2) {
          return true;
        } // if
        break;
    } // switch
    return false;
  } // is_true_statement
  
  // ---------------------------------------------------
  //  Image management
  // ---------------------------------------------------

  /**
   * Check if we have valid image for manipulation
   *
   * @param string $path - path to the image
   * @param boolean $throw - if true, function will throw errors
   * @return boolean
   * @throws Error
   */
  function check_image($path, $throw = true) {
    if (!is_file($path)) {
      if ($throw) {
        throw new Error(lang('File :file does not exist', array('file' => $path)));
      } else {
        return false;
      } // if
    } // if

    list($max_width, $max_height) = explode('x', strtolower(IMAGE_SIZE_CONSTRAINT));
    if ($max_width && $max_height) {
      list ($image_width, $image_height) = getimagesize($path);
      if (!$image_width || !$image_height) {
        if ($throw) {
          throw new Error(lang('Image could not be loaded. Check if file you are uploading is corrupted'));
        } else {
          return false;
        } // if
      } // if

      // switch dimensions
      if ($image_height > $image_width) {
        $temp = $image_width;
        $image_width = $image_height;
        $image_height = $temp;
      } // if

      if ($image_width > $max_width || $image_height > $max_height) {
        if ($throw) {
          throw new Error(lang('Uploaded image is too large. Maximum size of image is :dimension pixels', array('dimension' => IMAGE_SIZE_CONSTRAINT)));
        } else {
          return false;
        } // if
      } // if
    } // if

    // if file size is not right
    if (filesize($path) > RESIZE_SMALLER_THAN) {
      if ($throw) {
        throw new Error(lang('File size of image you uploaded is too large. Max file size is :size', array('size' => format_file_size(RESIZE_SMALLER_THAN))));
      } else {
        return false;
      } // if
    } // if

    return true;
  }; // check_image
  
  /**
   * Open image file
   * 
   * This function will try to open image file
   *
   * @param string $file
   * @return resource
   */
  function open_image($file) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    $info = getimagesize($file);
    if($info) {
      switch($info[2]) {
        case IMAGETYPE_JPEG:
          return array(
            'type' => IMAGETYPE_JPEG,
            'resource' => imagecreatefromjpeg($file)
          ); // array
        case IMAGETYPE_GIF:
          return array(
            'type' => IMAGETYPE_GIF,
            'resource' => imagecreatefromgif($file)
          ); // array
        case IMAGETYPE_PNG:
          return array(
            'type' => IMAGETYPE_PNG,
            'resource' => imagecreatefrompng($file)
          ); // array
        case IMAGETYPE_BMP:
          return array(
            'type' => IMAGETYPE_BMP,
            'resource' => imagecreatefrombmp($file)
          ); // array
      } // switch
    } // if
    
    return null;
  } // open_image

  /**
   * Create a new BMP image from file or URL
   *
   * @param string $filename - path to the bmp file
   * @return bool|resource - resource an image resource identifier on success, false on errors.
   */
  function imagecreatefrombmp($filename)
  {
    //Ouverture du fichier en mode binaire
    if (! $f1 = fopen($filename,"rb")) return FALSE;

    //1 : Chargement des ent�tes FICHIER
    $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
    if ($FILE['file_type'] != 19778) return FALSE;

    //2 : Chargement des ent�tes BMP
    $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
        '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
        '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
    $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
    if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
    $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
    $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
    $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
    $BMP['decal'] = 4-(4*$BMP['decal']);
    if ($BMP['decal'] == 4) $BMP['decal'] = 0;

    //3 : Chargement des couleurs de la palette
    $PALETTE = array();
    if ($BMP['colors'] < 16777216)
    {
      $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
    }

    //4 : Cr�ation de l'image
    $IMG = fread($f1,$BMP['size_bitmap']);
    $VIDE = chr(0);

    $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
    $P = 0;
    $Y = $BMP['height']-1;
    while ($Y >= 0)
    {
      $X=0;
      while ($X < $BMP['width'])
      {
        if ($BMP['bits_per_pixel'] == 24)
          $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
        elseif ($BMP['bits_per_pixel'] == 16)
        {
          $COLOR = unpack("n",substr($IMG,$P,2));
          $COLOR[1] = $PALETTE[$COLOR[1]+1];
        }
        elseif ($BMP['bits_per_pixel'] == 8)
        {
          $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
          $COLOR[1] = $PALETTE[$COLOR[1]+1];
        }
        elseif ($BMP['bits_per_pixel'] == 4)
        {
          $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
          if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
          $COLOR[1] = $PALETTE[$COLOR[1]+1];
        }
        elseif ($BMP['bits_per_pixel'] == 1)
        {
          $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
          if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
          elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
          elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
          elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
          elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
          elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
          elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
          elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
          $COLOR[1] = $PALETTE[$COLOR[1]+1];
        }
        else
          return FALSE;
        imagesetpixel($res,$X,$Y,$COLOR[1]);
        $X++;
        $P += $BMP['bytes_per_pixel'];
      }
      $Y--;
      $P+=$BMP['decal'];
    }

    //Fermeture du fichier
    fclose($f1);

    return $res;
  }
  
  /**
   * This function will save image resource into desired file
   * 
   * @param mixed $image
   * @param string $filename
   * @param string $type
   * @param integer $quality
   * @param boolean $close_after_saving
   * @return resource
   */
  function save_image($image, $filename, $type, $quality=80, $close_after_saving = true) {
    $result = false;
    
    switch($type) {
      case IMAGETYPE_JPEG:
        $result = imagejpeg($image, $filename, $quality);
        break;
        
      case IMAGETYPE_GIF:
        if(!function_exists('imagegif')) {
          return false; // If GD is compiled without GIF support
        } // if
        $result = imagegif($image, $filename);
        break;
        
      case IMAGETYPE_PNG:
        $result = imagepng($image, $filename);
        break;
    } // switch
    
    if ($close_after_saving) {
      imagedestroy($image);
    } // if
    
    return $result;
  } // save_image
  
  /**
   * Resize input image to fit given constraints
   *
   * @param mixed $input
   * @param string $dest_file
   * @param integer $max_width
   * @param integer $max_height
   * @param string $output_type
   * @param integer $quality
   * @param boolean $enlarge
   * @return boolean
   * @throws Error
   */
  function scale_image($input, $dest_file, $max_width, $max_height, $output_type = null, $quality = 80, $enlarge = false) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    if (is_array($input) && array_key_exists('type', $input) && array_key_exists('resource', $input)) {
      $open_image = $input;
      $close_resource = false;
    } else {
      $open_image = open_image($input);
      $close_resource = true;
      if (!is_array($open_image)) {
        throw new Error(lang('Could not parse image: '.$input));
      } // if
    } // if

    $image_type = $open_image['type'];
    $image = $open_image['resource'];
      
    if ($output_type === null) {
      $output_type = $image_type;
    } // if
      
    $width  = imagesx($image);
    $height = imagesy($image);
      
    $scale  = min($max_width / $width, $max_height / $height);

    if ($scale <= 1) {
      $new_width  = floor($scale * $width);
      $new_height = floor($scale * $height);

      $resulting_image = imagecreatetruecolor($new_width, $new_height);
      if (can_use_image_alpha($output_type)) {
        imagealphablending($resulting_image, false);
        imagesavealpha($resulting_image, true);
        $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
        imagefilledrectangle($resulting_image, 0, 0, $new_width, $new_height, $alpha);
      } else {
        $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
        imagefill($resulting_image, 0, 0, $white_color);
      } // if
      
      imagecopyresampled($resulting_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);      
    } else if ($scale > 1) {

      $resulting_image = imagecreatetruecolor($max_width, $max_height);
      if (can_use_image_alpha($output_type)) {
        imagealphablending($resulting_image, false);
        imagesavealpha($resulting_image, true);
        $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
        imagefilledrectangle($resulting_image, 0, 0, $max_width, $max_height, $alpha);
      } else {
        $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
        imagefill($resulting_image, 0, 0, $white_color);
      } // if

      if ($enlarge) {
        $new_width = floor($width * $scale);
        $new_height = floor($height * $scale);
        imagecopyresampled($resulting_image, $image, round(($max_width - $new_width) / 2), round(($max_height - $new_height) / 2), 0, 0, $new_width, $new_height, $width, $height);
      } else {
        imagecopy($resulting_image, $image, round(($max_width - $width) / 2), round(($max_height - $height) / 2), 0, 0, $width, $height);
      } // if
    } // if
    
    if ($close_resource) {
      imagedestroy($image);
    } // if

    return save_image($resulting_image, $dest_file, $output_type, $quality);
  } // scale_image
  
  /**
   * Scales image to fit specified dimensions
   * 
   * @param mixed $input
   * @param string $dest_file
   * @param integer $width
   * @param integer $height
   * @param integer $output_type
   * @param integer $quality
   * @return boolean
   * @throws Error
   */
  function scale_and_fit_image($input, $dest_file, $width, $height, $output_type = null, $quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    if (is_array($input) && array_key_exists('type', $input) && array_key_exists('resource', $input)) {
      $open_image = $input;
      $close_resource = false;
    } else {
      $open_image = open_image($input);
      $close_resource = true;
      if (!is_array($open_image)) {
        throw new Error(lang('Could not parse image: '.$input));
      } // if
    } // if
    
    $image_type = $open_image['type'];
    $image = $open_image['resource'];
    
    if($output_type === null) {
      $output_type = $image_type;
    } // if
    
    $src_width  = imagesx($image);
    $src_height = imagesy($image);
          
    $scale  = min($width / $src_width, $height / $src_height);
    
    if ($scale < 1) {
      $destination_width = floor($src_width * $scale);
      $destination_height = floor($src_height * $scale);
    } else {
      $destination_width = $src_width;
      $destination_height = $src_height;
    } // if
    
    $destination_x_offset = 0;
    $destination_y_offset = 0;
    
    $resulting_image = imagecreatetruecolor($destination_width, $destination_height);
    if (can_use_image_alpha($output_type)) {
      imagealphablending($resulting_image, false);
      imagesavealpha($resulting_image,true);
      $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
      imagefilledrectangle($resulting_image, 0, 0, $width, $height, $alpha);
    } else {
      $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
      imagefill($resulting_image, 0, 0, $white_color);
    } // if
        
    imagecopyresampled($resulting_image, $image, $destination_x_offset, $destination_y_offset, 0, 0, $destination_width, $destination_height, $src_width, $src_height);
    
    if ($close_resource) {
      imagedestroy($image);
    } // if
    
    return save_image($resulting_image, $dest_file, $output_type, $quality);
  } // scale_and_fit_image
  
  /**
   * Resize image, and crop it so you get squared image (best for thumbnails)
   * if $input_file is smaller than $dimension, resulting image will still have square shape and $dimension, and $input_file will be stretched to $dimension
   * 
   * @param mixed $input
   * @param string $dest_file
   * @param integer $dimension
   * @param integer $offset_x
   * @param integer $offset_y
   * @param integer $output_type
   * @param integer $quality
   * @return boolean
   * @throws Error
   */
  function scale_and_crop_image($input, $dest_file, $dimension, $offset_x = null, $offset_y = null, $output_type = null, $quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    if (is_array($input) && array_key_exists('type', $input) && array_key_exists('resource', $input)) {
      $open_image = $input;
      $close_resource = false;
    } else {
      $open_image = open_image($input);
      $close_resource = true;
      if (!is_array($open_image)) {
        throw new Error(lang('Could not parse image: '.$input));
      } // if
    } // if
            
    $image_type = $open_image['type'];
    $image = $open_image['resource'];
    
    if($output_type === null) {
      $output_type = $image_type;
    } // if

    $width  = imagesx($image);
    $height = imagesy($image);
    
    $current_dimension = min(array($width, $height));
    
    if ($offset_x === null && $offset_y === null) {
      $offset_x = round(($width - $current_dimension) /2);
      $offset_y = round(($height - $current_dimension) / 2);
    } // if
    
    $resulting_image = imagecreatetruecolor($dimension, $dimension);
    if (can_use_image_alpha($output_type)) {
      imagealphablending($resulting_image, false);
      imagesavealpha($resulting_image,true);
      $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
      imagefilledrectangle($resulting_image, 0, 0, $dimension, $dimension, $alpha);
    } else {
      $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
      imagefill($resulting_image, 0, 0, $white_color);
    } // if
    
    imagecopyresampled($resulting_image, $image, 0, 0, $offset_x, $offset_y, $dimension, $dimension, $current_dimension, $current_dimension);
    
    if ($close_resource) {
      imagedestroy($image);
    } // if
        
    return save_image($resulting_image, $dest_file, $output_type, $quality);
  } // scale_crop_image
  
  /**
   * Convert image to desired $type
   * 
   * @param mixed $input
   * @param string $dest_file
   * @param string $type
   * @param integer $quality
   * @return boolean
   * @throws Error
   */
  function convert_image($input, $dest_file, $type, $quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    if (is_array($input) && array_key_exists('type', $input) && array_key_exists('resource', $input)) {
      $open_image = $input;
      $close_resource = false;
    } else {
      $open_image = open_image($input);
      $close_resource = true;
      if (!is_array($open_image)) {
        throw new Error(lang('Could not parse image: '.$input));
      } // if
    } // if
    
    $image_type = $open_image['type'];
    $image = $open_image['resource'];
    
    $width  = imagesx($image);
    $height = imagesy($image);

    $resulting_image = imagecreatetruecolor($width, $height);
    if (can_use_image_alpha($type)) {
      imagealphablending($resulting_image, false);
      imagesavealpha($resulting_image,true);
      $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
      imagefilledrectangle($resulting_image, 0, 0, $width, $height, $alpha);
    } else {
      $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
      imagefill($resulting_image, 0, 0, $white_color);
    } // if

    imagecopyresampled($resulting_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
    
    if ($close_resource) {
      imagedestroy($image);
    } // if
    
    return save_image($resulting_image, $dest_file, $type);
  } // convert_image
  
  /**
   * Stretch image
   *
   * @param mixed $input
   * @param string $dest_file
   * @param integer $new_width
   * @param integer $new_height
   * @param mixed $output_type
   * @param integer $quality
   * @return null
   * @throws Error
   */
  function stretch_image($input, $dest_file, $new_width, $new_height, $output_type = null, $quality = 80) {
    if(!extension_loaded('gd')) {
      return false;
    } // if
    
    if (is_array($input) && array_key_exists('type', $input) && array_key_exists('resource', $input)) {
      $open_image = $input;
      $close_resource = false;
    } else {
      $open_image = open_image($input);
      $close_resource = true;
      if (!is_array($open_image)) {
        throw new Error(lang('Could not parse image: '.$input));
      } // if
    } // if
    
      $image_type = $open_image['type'];
      $image = $open_image['resource'];
      
      if($output_type === null) {
        $output_type = $image_type;
      } // if
      
      $width  = imagesx($image);
      $height = imagesy($image);

      $resulting_image = imagecreatetruecolor($new_width, $new_height);
      if (can_use_image_alpha($output_type)) {
        imagealphablending($resulting_image, false);
        imagesavealpha($resulting_image,true);
        $alpha = imagecolorallocatealpha($resulting_image, 255, 255, 255, 127);
        imagefilledrectangle($resulting_image, 0, 0, $new_width, $new_height, $alpha);
      } else {
        $white_color = imagecolorallocate($resulting_image, 255, 255, 255);
        imagefill($resulting_image, 0, 0, $white_color);
      } // if
      
      imagecopyresampled($resulting_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      
      if ($close_resource) {
        imagedestroy($image);
      } // if

    $result = save_image($image, $dest_file, $output_type, $quality);

    return $result;
  } // stretch_image
  
  /**
   * check if hex color code is valid
   *
   * @param string $color_code
   * @return boolean
   */
  function is_valid_hex_color($color_code) {
    if (!$color_code) {
      return false;
    } // if
    
    if (strlen($color_code) !=7) {
      return false;
    } // if
    
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_code)) {
      return false;
    } // if
    
    return true;
  } // is_valid_hex_color
  
  
  /**
   * Returns true if php is able to resize images
   *  
   * @return boolean 
   */
  function can_resize_images() {
    return extension_loaded('gd');
  } // can_resize_images
  
  /**
   * Whether image transformations can be done with preserving alpha or not
   *
   * @param integer $output_type
   * @return boolean
   */
  function can_use_image_alpha($output_type) {
    if (!extension_loaded('gd')) {
      return false;
    } // if
    
    if (!(function_exists('imagealphablending') && function_exists('imagesavealpha') && function_exists('imagecolorallocatealpha'))) {
      return false;
    } // if
        
    return ($output_type == IMAGETYPE_PNG);
  } // can_use_image_alpha

  /**
   * Convert comma-separated values to array and trim values, also remove empty ones
   *
   * @param string $string
   * @return array
   */
  function csv_to_array($string) {
    // convert csv into array
    $array = (array) explode(",", $string);

    // trim all array elements
    $array = array_map('trim', $array);

    // remove empty array elements
    $array = array_filter($array);

    return $array;
  } // csv_to_array