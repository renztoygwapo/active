<?php

  /**
   * Wireframe actions optimized for phone interface
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class PhoneWireframeBreadcrumbs extends WireframeBreadcrumbs {
    
    /**
     * Returns true if current page, based on defined bread crumbs, has a back 
     * page
     * 
     * @return boolean
     */
    function hasBackPage() {
      return $this->count() > 2;
    } // hasBackPage
    
    /**
     * Return back page
     * 
     * @return string
     */
    function getBackPage() {
      $keys = $this->keys();
      
      if($keys) {
        $keys_count = count($keys);
        
        if($keys_count > 1) {
          return array_merge(array('name' => $keys[$keys_count - 3]), $this->get($keys[$keys_count - 3]));
        } // if
      } // if
      
      return null;
    } // getBackPage
    
    /**
     * Return back page URL
     * 
     * @return string
     */
    function getBackPageUrl() {
      return array_var($this->getBackPage(), 'url');
    } // getBackPageUrl
    
    /**
     * Return back page title
     * 
     * @param boolean $skip_first
     * @return string
     */
    function getBackPageText($skip_first = true) {
      return array_var($this->getBackPage(), 'text');
    } // getBackPageText
    
  }