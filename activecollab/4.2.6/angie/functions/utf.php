<?php

  /**
   * MB string extension wrapper functions
   * 
   * This function will check if MB string extension is availalbe and use mb_ 
   * functions if it is. Otherwise it will use old PHP functions
   * 
   * @package angie.functions
   */
  
  define('CAN_USE_MBSTRING', extension_loaded('mbstring'));

  /**
   * Extended substr function. If it finds mbstring extension it will use, else 
   * it will use old substr() function
   *
   * @param string $string
   * @param integer $start
   * @param integer $length
   * @return string
   */
  function substr_utf($string, $start = 0, $length = null) {
    $start = (integer) $start >= 0 ? (integer) $start : 0;

    if(is_null($length)) {
      $length = strlen_utf($string) - $start;
    } // if

    return CAN_USE_MBSTRING ? mb_substr($string, $start, $length, 'UTF-8') : substr($string, $start, $length);
  } // substr_utf

  /**
   * UTF-8 safe str_replace
   *
   * @param string $search
   * @param string $replace
   * @param string $subject
   * @param integer $count
   * @return array|mixed|string
   */
  function str_replace_utf8($search, $replace, $subject, $count = null) {
    return CAN_USE_MBSTRING ? mb_str_replace($search, $replace, $subject, 'UTF-8', $count) : str_replace($search, $replace, $subject, $count);
  } // str_replace_utf8

  /**
   * mb_str_replace function found on php documentation page
   *
   * @param $search
   * @param $replace
   * @param $subject
   * @param null|string $encoding
   * @param int|null $count
   * @return array|string
   */
  function mb_str_replace($search, $replace, $subject, $encoding = null, int &$count = null) {

    if (is_array($subject)) {
      $result = array();
      foreach ($subject as $item) {
        $result[] = mb_str_replace($search, $replace, $item, $encoding, $count);
      }
      return $result;
    }

    if (!is_array($search)) {
      return _mb_str_replace($search, $replace, $subject, $encoding, $count);
    }

    $replace_is_array = is_array($replace);
    foreach ($search as $key => $value) {
      $subject = _mb_str_replace(
        $value,
        $replace_is_array ? $replace[$key] : $replace,
        $subject,
        $encoding,
        $count
      );
    }
    return $subject;
  }

  /**
   * Implementation of mb_str_replace. Do not call directly. Enforces string parameters.
   */
  function _mb_str_replace($search, $replace, $subject, $encoding = null, int &$count = null) {
    $search_length = mb_strlen($search, $encoding);
    $subject_length = mb_strlen($subject, $encoding);
    $offset = 0;
    $result = '';

    while ($offset < $subject_length) {
      $match = mb_strpos($subject, $search, $offset, $encoding);
      if ($match === false) {
        if ($offset === 0) {
          // No match was ever found, just return the subject.
          return $subject;
        }
        // Append the final portion of the subject to the replaced.
        $result .= mb_substr($subject, $offset, $subject_length - $offset, $encoding);
        break;
      }
      if ($count !== null) {
        $count++;
      }
      $result .= mb_substr($subject, $offset, $match - $offset, $encoding);
      $result .= $replace;
      $offset = $match + $search_length;
    }

    return $result;
  } // function
  
  /**
   * Return UTF safe string lenght
   *
   * @param string $string
   * @return integer
   */
  function strlen_utf($string) {
    return CAN_USE_MBSTRING ? mb_strlen($string, 'UTF-8') : strlen($string);
  } // strlen_utf
  
  /**
   * UTF safe strpos
   *
   * @param string $haystack
   * @param string $needle
   * @param integer $offset
   * @return mixed
   */
  function strpos_utf($haystack, $needle, $offset = 0) {
    return CAN_USE_MBSTRING ? mb_strpos($haystack, $needle, $offset, 'UTF-8') : strpos($haystack, $needle, $offset);
  } // strpos_utf

  /**
   * UTF safe stripos
   *
   * @param string $haystack
   * @param string $needle
   * @param int $offset
   * @return mixed
   */
  function stripos_utf($haystack, $needle, $offset = 0) {
    return CAN_USE_MBSTRING ? mb_stripos($haystack, $needle, $offset) : stripos($haystack, $needle, $offset);
  } // stripos_utf
  
  /**
   * UTF friendly strtolower function
   *
   * @param string $string
   * @return string
   */
  function strtolower_utf($string) {
  	return CAN_USE_MBSTRING ? mb_strtolower($string, 'UTF-8') : strtolower($string);
  } // strtolower_utf

  /**
   * Return uppercase string
   *
   * @param strin $string
   * @return string
   */
  function strtoupper_utf($string) {
    return CAN_USE_MBSTRING ? mb_strtoupper($string, 'UTF-8') : strtoupper($string);
  } // strtoupper_utf
  
  /**
   * Capitalize first letter, UTF-8 safe
   *
   * @param string $string
   * @return string
   */
  function ucfirst_utf($string) {
    if(trim($string) == '') {
      return $string; // Nothing to work with
    } // if
    
    if(CAN_USE_MBSTRING) {
      return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    } else {
      return ucfirst($string);
    } // if
  } // ucfirst_utf

  /**
   * Return number of $needle occurances in $haystack
   *
   * @param string $haystack
   * @param string $needle
   * @return integer
   */
  function substr_count_utf($haystack , $needle) {
    if(CAN_USE_MBSTRING) {
      return mb_substr_count($haystack , $needle);
    } else {
      return substr_count($haystack, $needle);
    } // if
  } // substr_count_utf
  
  /**
   * Convert text from source encoding to utf8, source encoding needs to be predetermined
   * 
   * If operation is successful return is converted string, if not boolean false
   *
   * @param string $what
   * @param string $from_encoding
   * @return string
   */
  function convert_to_utf8($what, $from_encoding = 'UTF-8') {
    $from_encoding = strtoupper($from_encoding);
    
    if (!$from_encoding) {
      $from_encoding = 'UTF-8';
    } // if
    
    if (!$what) {
      return $what;
    } // if
    
    if ($from_encoding == 'UTF-8') {
      return $what;
    } // if
    
    // if encoding is ISO-8859-1 we use utf8 encode function, no need for wodoo magic
    if ($from_encoding == 'ISO-8859-1' && function_exists('utf8_encode')) {
      return utf8_encode($what);
    } // if
       
    // check if inconv is present and try to convert with it
    if (function_exists('iconv') && $from_encoding != 'UTF-7') {
      $output = iconv($from_encoding, 'UTF-8//IGNORE', $what);
      
      // if iconv succeded return output, otherwise try conversion with mbstring
      if ($output != false) {
        return $output;
      } // if
    } // if
  
    // check if mbstring extension is loaded and try to convert with it
    if (CAN_USE_MBSTRING) {
      $output = mb_convert_encoding($what, 'UTF-8', $from_encoding);
      
      // if mbstring suceeds with importing we return output, and if not we use custom conversion class
      if ($output) {
        return $output;
      } // if
    } // if
    
    // convert WINDOWS from encoding to CP
    if (strpos($from_encoding,'WINDOWS-') === 0) {
      $from_encoding = str_replace('WINDOWS-','CP', $from_encoding);
    } // if

    // if custom conversion class is present use it to convert
    if (function_exists('utf8converter_can_convert_encoding')) {
      return utf8converter_encode_utf8($what, $from_encoding);
    } // if
    
    return $what;
  } // convert_to_utf8
  
  /**
   * Convert from utf8 encoding to any available encoding
   *
   * @param string $what
   * @param string $to_encoding
   */
  function convert_from_utf8($what, $to_encoding = 'Windows-1252') {
    $to_encoding = strtoupper($to_encoding);
    
    if (!$what) {
      return $what;
    } // if
    
    if ($to_encoding == 'UTF-8') {
      return $what;
    } // if
    
    if ($to_encoding == 'ISO-8859-1' && !function_exists('iconv')) {
      return utf8_decode($what);
    } // if
    
    if (function_exists('iconv') && $to_encoding != 'UTF-7') {
      $output = iconv('UTF-8', $to_encoding , $what);
      // if iconv succeded return output, otherwise try conversion with mbstring
      if ($output != false) {
        return $output;
      } // if
    } // if
    
    if (CAN_USE_MBSTRING) {
      $output = mb_convert_encoding($what, $to_encoding, 'UTF-8');
      
      // if mbstring suceeds with importing we return output, and if not we use custom conversion class
      if ($output) {
        return $output;
      } // if
    } // if
    
    // convert WINDOWS from encoding to CP
    if (strpos($to_encoding,'WINDOWS-') === 0) {
      $to_encoding = str_replace('WINDOWS-','CP', $to_encoding);
    } // if
    
    // if custom conversion class is present use it to convert
    if (function_exists('utf8converter_can_convert_encoding')) {
      return utf8converter_decode_utf8($what, $to_encoding);
    } // if
    
    return $what;
  } // convert_from_utf8

?>