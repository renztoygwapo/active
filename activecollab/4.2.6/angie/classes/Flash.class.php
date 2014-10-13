<?php

  /**
   * Flash service
   *
   * Purpose of this service is to make some data available across pages. Flash
   * data is available on the next page but deleted when execution reach its end
   *
   * Usual use of Flash is to make possible that current page pass some data
   * to the next one (for instance success or error message before HTTP 
   * redirect).
   *
   * Flash service as a concep is taken from Rails.
   *
   * @package angie.library
   */
  final class Flash {
  
    /**
     * Data that prevous page left in the Flash
     *
     * @var array
     */
    private $previous = array();
    
    /**
     * Data that current page is saving for the next page
     *
     * @var array
     */
    private $next = array();
    
    /**
     * Construct flash object
     */
    function __construct() {
    	$this->readFlash();
    } // __construct
    
    /**
     * Return specific variable from the flash. If value is not found NULL is
     * returned
     *
     * @param string $var
     * @return mixed
     */
    function getVariable($var) {
      return isset($this->previous[$var]) ? $this->previous[$var] : null;
    } // end func getVariable
    
    /**
     * Add specific variable to the flash. This variable will be available on 
     * the next page unlease removed with the removeVariable() or clear() method
     *
     * @param string $var
     * @param mixed $value
     */
    function addVariable($var, $value) {
      $this->next[$var] = $value;
      $this->writeFlash();
    } // end func addVariable
    
    /**
     * Remove specific variable for the Flash
     *
     * @param string $var
     */
    function removeVariable($var) {
      if(isset($this->next[$var])) {
        unset($this->next[$var]);
      } // if
      $this->writeFlash();
    } // end func removeVariable
    
    /**
     * Call this function to clear flash. Note that data that previous page
     * stored will not be deleted - just the data that this page saved for
     * the next page
     */
    function clear() {
      $this->next = array();
    } // clear
    
    /**
     * This function will read flash data from the $_SESSION variable
     * and load it into $this->previous array
     */
    function readFlash() {
      $flash_data = array_var($_SESSION, 'flash_data');
      if($flash_data !== null) {
        if(is_array($flash_data)) {
          $this->previous = $flash_data;
        } // if
        unset($_SESSION['flash_data']);
      } // if
    } // readFlash
    
    /**
     * Save content of the $this->next array into the $_SESSION autoglobal var
     */
    function writeFlash() {
      $_SESSION['flash_data'] = $this->next;
    } // writeFlash
    
    /**
     * Shortcut method for adding success var to flash
     *
     * @param string $content
     * @param array $params
     * @param boolean $clean_params
     * @param Langauge $language
     */
    function success($content, $params = null, $clean_params = true, $language = null) {
      $this->addVariable('success', lang($content, $params, $clean_params, $language));
    } // success
    
    /**
     * Shortcut method for adding error var to flash
     *
     * @param string $content
     * @param array $params
     * @param boolean $clean_params
     * @param Langauge $language
     */
    function error($content, $params = null, $clean_params = true, $language = null) {
    	$this->addVariable('error', lang($content, $params, $clean_params, $language));
    } // error
    
  }