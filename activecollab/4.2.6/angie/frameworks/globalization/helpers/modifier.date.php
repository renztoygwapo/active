<?php

  /**
   * date modifier implementation
   *
   * @package angie.frameworks.environment
   */

  /**
   * Return formated date
   *
   * @param string|DateTimeValue|DateValue $content
   * @param string $offset
   * @return string
   * @throws InvalidInstanceError
   */
  function smarty_modifier_date($content, $offset = null) {
    if ($content && is_string($content)) {
      $content = DateTimeValue::makeFromString($content); // first try making object from string
    } //if

  	if ($content instanceof DateTimeValue) {
  		return $content->formatDateForUser(Authentication::getLoggedUser(), $offset);
  	} else if($content instanceof DateValue) {
      return $content->formatForUser(Authentication::getLoggedUser(), $offset);
    } else {
      throw new InvalidInstanceError('content', $content, 'DateValue');
    } // if
  } // smarty_modifier_date