<?php

  /**
   * time modifier implementation
   * 
   * @package angie.library.smarty
   */

  /**
   * Return formated time
   *
   * @param string $content
   * @param integer $offset
   * @throws InvalidInstanceError
   * @return string
   */
  function smarty_modifier_time($content, $offset = null) {
    if($content instanceof DateTimeValue || ($content && is_string($content))) {
      $content = $content instanceof DateTimeValue ? $content : DateTimeValue::makeFromString($content);

      return $content->formatTimeForUser(Authentication::getLoggedUser(), $offset);
    } else {
      throw new InvalidInstanceError('content', $content, 'DateTimeValue');
    } // if
  } // smarty_modifier_time