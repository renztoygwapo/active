<?php

  /**
   * Async toggler JavaScript callback implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage model
   */
  class AsyncTogglerCallback extends JavaScriptCallback {
    
    /**
     * On action settings
     *
     * @var array
     */
    protected $on;
    
    /**
     * Off action settings
     *
     * @var array
     */
    protected $off;
    
    /**
     * On/off switch
     *
     * @var boolean
     */
    protected $is_on;
  
    /**
     * Construct toggler callback
     * 
     * @param array $on
     * @param array $off
     * @param boolean $is_on
     */
    function __construct($on, $off, $is_on = false) {
      $this->on = $on;
      $this->off = $off;
      $this->is_on = (boolean) $is_on;
    } // __construct
    
    /**
     * Render callback call
     * 
     * @return string
     */
    function render() {
      if(isset($this->on['success_event']) && $this->off['success_event']) {
        $success_event = array(
          0 => $this->on['success_event'], 
          1 => $this->off['success_event'], 
        );
      } else {
        $success_event = null;
      } // if
      
      if(isset($this->on['success_message']) && $this->off['success_message']) {
        $success_message = array(
          0 => $this->on['success_message'], 
          1 => $this->off['success_message'], 
        );
      } else {
        $success_message = null;
      } // if
      
      return '(function () { $(this).asyncToggler(' . JSON::encode(array(
        'is_on' => $this->is_on ? 1 : 0, 
        'content_when_on' => isset($this->on['icon']) ? '<img src="' . $this->on['icon'] . '">' : $this->on['text'], 
        'content_when_off' => isset($this->off['icon']) ? '<img src="' . $this->off['icon'] . '">' : $this->off['text'], 
        'title_when_on' => isset($this->on['title']) ? $this->on['title'] : null,
        'title_when_off' => isset($this->off['title']) ? $this->off['title'] : null,
        'url_when_on' => $this->on['url'], 
        'url_when_off' => $this->off['url'], 
        'confirmation_when_on' => isset($this->on['confirmation']) && $this->on['confirmation'] ? $this->on['confirmation'] : null,
        'confirmation_when_off' => isset($this->off['confirmation']) && $this->off['confirmation'] ? $this->off['confirmation'] : null,
        'success_event' => $success_event, 
        'success_message' => $success_message, 
      )) .'); })';
    } // render
    
  }