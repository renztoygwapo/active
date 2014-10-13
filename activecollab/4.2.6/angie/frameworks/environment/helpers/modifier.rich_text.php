<?php

  /**
   * rich_text helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Convert raw text to rich text (HTML)
   * 
   * @param string $content
   * @param string $for
   * @return string
   */
  function smarty_modifier_rich_text($content, $for = null) {
    return HTML::toRichText($content, $for);
  } // smarty_modifier_rich_text