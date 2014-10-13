<?php

  /**
   * Format number (number_format function interface)
   * 
   * @param float $number
   * @param Language $language
   * @param integer $num_decimal_places
   * @return string
   */
  function smarty_modifier_number($number, Language $language = null, $num_decimal_places = 2) {
    return Globalization::formatNumber($number, $language, $num_decimal_places);
  } // smarty_modifier_number