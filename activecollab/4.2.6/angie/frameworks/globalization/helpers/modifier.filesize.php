<?php

  /**
   * filesize modifier implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Format filesize
   *
   * @param string $value
   * @return string
   */
  function smarty_modifier_filesize($value) {
    return format_file_size($value);
  } // smarty_modifier_filesize