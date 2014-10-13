<?php

  /**
   * html_to_text modifier implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Convet HTML content into plain text
   *
   * @param string $content
   * @return string
   */
  function smarty_modifier_html_to_text($content) {
    return HTML::toPlainText($content);
  } // smarty_modifier_html_to_text