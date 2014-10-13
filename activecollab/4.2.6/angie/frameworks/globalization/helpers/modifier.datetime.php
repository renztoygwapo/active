<?php

  /**
   * datetime modifier implementation
   * 
   * @package angie.library.smarty
   */

  /**
   * Return formated datetime
   *
   * @param string $content
   * @param integer $offset
   * @throws InvalidInstanceError
   * @return string
   */
  function smarty_modifier_datetime($content, $offset = null) {
    if($content instanceof DateValue) {
      return $content->formatForUser(Authentication::getLoggedUser(), $offset);
    } else {
      throw new InvalidInstanceError('content', $content, 'DateTimeValue');
    } // if
  } // smarty_modifier_datetime