<?php

  /**
   * SimpleHTMLDOMForAngie for angie implementation
   * 
   * @package angie.vendor.simplehtmldom
   */
  final class SimpleHTMLDOMForAngie {
    
    /**
     * Get parser with loaded html
     *
     * @param String $html
     * @return simple_html_dom
     */
    static function getInstance($html) {
      $dom = new simple_html_dom(null, true, true, 'UTF-8', "\r\n");
      if (empty($html)) {
        $dom->clear();
        return false;
      } // if
      $dom->load($html, true, true);
      return $dom;
    } // instance
    
  }