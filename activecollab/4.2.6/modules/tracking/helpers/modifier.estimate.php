<?php

  /**
   * estimate modifier implementation
   *
   * @package angie.frameworks.estimates
   * @subpackage helpers
   */

  /**
   * Properly display estimate value
   *
   * @param integer $value
   * @param boolean $short
   */
  function smarty_modifier_estimate($value, $short = true) {
    $days = floor($value / 8);
    $hours = floor($value - ($days * 8));
    $minutes = floor(($value - floor($value)) * 60);
        
    $result = array();
    
    if($days) {
      if($short) {
        $result[] = lang(':daysd', array('days' => $days));
      } else {
        if($days == 1) {
          $result[] = lang('1 day');
        } else {
          $result[] = lang(':days days', array('days' => $days));
        } // if
      } // if
    } // if
    
    if($hours || ($days && $minutes)) {
      if($short) {
        $result[] = lang(':hoursh', array('hours' => $hours));
      } else {
        if($hours == 1) {
          $result[] = lang('1 hour');
        } else {
          $result[] = lang(':hours hours', array('hours' => $hours));
        } // if
      } // if
    } // if
    
    if($minutes) {
      if($short) {
        $result[] = lang(':minutesm', array('minutes' => $minutes));
      } else {
        if($minutes == 1) {
          $result[] = lang('1 minute');
        } else {
          $result[] = lang(':minutes minutes', array('minutes' => $minutes));
        } // if
      } // if
    } // if
    
    return count($result) ? implode(' ', $result) : '0h';
  } // smarty_modifier_estimate