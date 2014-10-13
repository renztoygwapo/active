<?php

/**
 * Ago value modifier
 *
 * @package angie.frameworks.globalization
 * @subpackage helpers
 */

/**
 * Return '*** ago' message
 *
 * @param DateTimeValue $input
 * @param integer $offset
 * @param boolean $strict_ago
 * @return string
 * @throws InvalidInstanceError
 */
function smarty_modifier_ago($input, $offset = null, $strict_ago = false) {
  if($input instanceof DateValue) {
    if($offset === null) {
      $offset = get_user_gmt_offset();
    } // if

    $datetime = new DateTimeValue($input->getTimestamp() + $offset);
    $reference = new DateTimeValue(time() + $offset);

    $diff = $reference->getTimestamp() - $datetime->getTimestamp();

    // Get exact number of seconds between current time and yesterday morning
    $reference_timestamp = $reference->getTimestamp();
    $yesterday_begins_at = 86400 + (date('G', $reference_timestamp) * 3600) + (date('i', $reference_timestamp) * 60) + date('s', $reference_timestamp);

    if($diff < 60) {
      $value = lang('Few seconds ago');
    } elseif($diff < 120) {
      $value = lang('A minute ago');
    } elseif($diff < 3600) {
      $value = lang(':num minutes ago', array('num' => floor($diff / 60)));
    } elseif($diff < 7200) {
      $value = lang('An hour ago');
    } elseif($diff < 86400) {
      if(date('j', $datetime->getTimestamp()) != date('j', $reference->getTimestamp())) {
        $value = lang('Yesterday');
      } else {
        $mod = $diff % 3600;
        if($mod > 2700) {
          $value = lang(':num hours ago', array('num' => ceil($diff / 3600)));
        } else {
          $value = lang(':num hours ago', array('num' => floor($diff / 3600)));
        } // if
      } // if
    } elseif($diff <= $yesterday_begins_at) {
      $value = lang('Yesterday');
    } elseif($strict_ago || $diff < 2592000) {
      $days_ago_value = floor($diff / 86400);
      if ($days_ago_value > 29) {
        $months_ago_value = floor($days_ago_value / 30);
        $days_ago_value = $days_ago_value % 30;
        if ($months_ago_value > 11) {
          $years_ago_value = floor($months_ago_value / 12);
          $months_ago_value = $days_ago_value % 12;
        } // if
      } // if

      $value = '';
      if (isset($years_ago_value) && $years_ago_value > 0) {
        $value .= $years_ago_value == 1 ? $years_ago_value . ' ' .lang('year') : $years_ago_value . ' ' .lang('years');
        $value .= ' ';
      } // if

      if (isset($months_ago_value) && $months_ago_value > 0) {
        $value .= $months_ago_value == 1 ? $months_ago_value . ' ' .lang('month') : $months_ago_value . ' ' .lang('months');
        $value .= ' ';
      } // if

      if ($days_ago_value > 0) {
        $value .= $days_ago_value == 1 ? $days_ago_value . ' ' .lang('day') : $days_ago_value . ' ' .lang('days');
        $value .= ' ';
      } // if

      $value .= lang('ago');
    } else {
      AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
      AngieApplication::useHelper('datetime', GLOBALIZATION_FRAMEWORK, 'modifier');
      return '<span class="ago" title="' . clean(smarty_modifier_datetime($datetime, 0)) . '">' . lang('On') . ' ' . smarty_modifier_date($datetime, 0) . '</span>';
    } // if

    AngieApplication::useHelper('datetime', GLOBALIZATION_FRAMEWORK, 'modifier');
    return '<span class="ago" title="' . clean(smarty_modifier_datetime($datetime, 0)) . '">' . $value . '</span>';
  } else {
    throw new InvalidInstanceError('input', $input, 'DateTimeValue');
  } // if
} // smarty_modifier_ago