<?php

  /**
   * Globalization related functions
   * 
   * @package angie.frameworks.globalization
   */

  /**
   * Shortcut function to Globalization::lang()
   *
   * @param string $content
   * @param array $params
   * @param boolean $clean_params
   * @param Language $language
   * @return string
   */
  function lang($content, $params = null, $clean_params = true, $language = null) {
    return Globalization::lang($content, $params, $clean_params, $language);
  } // lang
  
  // ---------------------------------------------------
  //  Time zone
  // ---------------------------------------------------

  /**
   * Return offset based on a current user
   *
   * @param boolean $reload
   * @return integer
   */
  function get_system_gmt_offset($reload = false) {
    static $offset = null;

    if($reload || $offset === null) {
      $timezone_offset = ConfigOptions::getValue('time_timezone');
      $dst = ConfigOptions::getValue('time_dst');

      $offset = $dst ? $timezone_offset + 3600 : $timezone_offset;
    } // if

    return $offset;
  } // get_system_gmt_offset

  /**
   * Return user GMT offset
   *
   * Return number of seconds that current user is away from the GMT. If user is
   * not logged in this function should return system offset
   *
   * @param User $user
   * @param boolean $reload
   * @return integer
   */
  function get_user_gmt_offset($user = null, $reload = false) {
    static $offset = array();

    if($user === null) {
      $user = Authentication::getLoggedUser();
    } // if

    if($user instanceof User) {
      if($reload || !isset($offset[$user->getId()])) {
        $timezone_offset = ConfigOptions::getValueFor('time_timezone', $user);
        $dst = ConfigOptions::getValueFor('time_dst', $user);

        $offset[$user->getId()] = $dst ? $timezone_offset + 3600 : $timezone_offset;
      } // if

      return $offset[$user->getId()];
    } else {
      return get_system_gmt_offset($reload);
    } // if
  } // get_user_gmt_offset
  
  // ---------------------------------------------------
  //  Conversion
  // ---------------------------------------------------
  
  /**
   * Convert Time to Float Value
   *
   * @param mixed $time
   * @return float
   */
  function time_to_float($time) {
    if(strpos($time, ':') !== false) {
      $time_arr = explode(':', $time);
    	
    	if(count($time_arr) < 2) {
    	  $float_time = round($time, 2);
    	} else {
    	  $minutes = ($time_arr[1] > 60) ? 60 : $time_arr[1];
    		$float_time = round($time_arr[0] + ($minutes/60), 2);
    	} // if
    	
    	$time = round($float_time, 2);
    } // if
    
    if(strpos($time, ',') !== false) {
      $time = str_replace(',', '.', $time);
    } // if
    
    return (float) $time;
  } // time_to_float 
  
  /**
   * Convert time to number of seconds
   *
   * @param string $time
   * @return integer
   */
  function time_to_int($time) {
    if(strpos($time, ':') !== false) {
      $time_arr = explode(':', $time);
      foreach($time_arr as $k => $v) {
        $time_arr[$k] = (integer) trim($v);
      } // foreach
      
      if(isset($time_arr[1]) && $time_arr[1] > 59) {
        $time_arr[1] = 59;
      } // if
      
      if(isset($time_arr[2]) && $time_arr[2] > 59) {
        $time_arr[2] = 59;
      } // if
      
      if(count($time_arr) == 2) {
        return $time_arr[0] * 3600 + $time_arr[1] * 60;
      } else {
        return $time_arr[0] * 3600 + $time_arr[1] * 60 + (integer) $time_arr[2];
      } // if
      
    } elseif(strpos($time, ',') !== false || strpos($time, '.') !== false) {
      $time = str_replace(',', '.', $time);
      
      return floor((float) $time * 3600);
    } else {
      return ((integer) $time) * 3600;
    } // if
  } // time_to_int
  
  /**
   * Convert Float Value to Time
   *
   * @param float $time
   * @return string
   */
  function float_to_time($time) {
    if (is_float($time)) {
    	$time_dec = $time - floor($time);
    	
    	$hours = floor($time);
    	$minutes = round($time_dec * 60);
    	
    	return $hours . ':' . ($minutes < 10 ? "0{$minutes}" : $minutes);
    } else if (is_int($time)) {
      return $time . ':00';
    } else {
    	return $time;
    } // if
  } // float_to_time