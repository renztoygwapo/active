<?php

  /**
   * timestamp modifier implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Return timestamp based on $content value
   *
   * @param mixed $content
   * @return integer
   */
  function smarty_modifier_timestamp($content) {
    if($content instanceof DateValue) {
      return $content->getTimestamp();
    } elseif(is_int($content)) {
      return $content;
    } else {
      return strtotime($content);
    } // if
  } // smarty_modifier_timestamp