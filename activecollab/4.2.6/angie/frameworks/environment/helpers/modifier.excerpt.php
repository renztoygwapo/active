<?php

  /**
   * Excerpt modifier definition
   */

  /**
   * Return excerpt from string
   *
   * @param string $string
   * @param integer $length
   * @param string $etc
   * @param boolean $flat
   * @return string
   */
  function smarty_modifier_excerpt($string, $length = 100, $etc = '...', $flat = false) {
    return $flat ? str_excerpt(strip_tags($string), $length, $etc) : str_excerpt($string, $length, $etc);
  } // smarty_modifier_excerpt