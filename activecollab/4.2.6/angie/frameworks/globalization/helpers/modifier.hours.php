<?php

  /**
   * hours modifier implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */
  
  /**
   * Return formatted hours based on float value
   *
   * @param float $content
   * @param Language|null $language
   * @param int $decimal_spaces
   * @param bool $trim_zeros
   * @return string
   */
  function smarty_modifier_hours($content, $language = null, $decimal_spaces = 2, $trim_zeros = true) {
    if($decimal_spaces) {
      return Globalization::formatNumber($content, $language, $decimal_spaces, $trim_zeros);
    } else {
      return float_to_time($content);
    } // if
  } // smarty_modifier_hours