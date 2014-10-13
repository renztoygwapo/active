<?php

  /**
   * Markdown for Angie
   *
   * @package angie.vendor.hyperlight
   */
  final class MarkdownForAngie {

    /**
     * Convert $text from plain text to HTML
     *
     * @param string $text
     * @return string
     */
    static function textToHtml($text) {
      return \Michelf\MarkdownExtra::defaultTransform($text);
    } // textToHtml

  }