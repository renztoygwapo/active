<?php

  /**
   * Single wireframe action
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  class WireframeAction implements IJSON {
    
    /**
     * Action label
     *
     * @var string
     */
    protected $text;
    
    /**
     * Action URL
     *
     * @var string
     */
    protected $url;
    
    /**
     * Action icon, a single icon for all interfaces or a special icon per 
     * interface
     *
     * @var mixed
     */
    protected $icon;
    
    /**
     * On click handler, when applicable
     *
     * @var IJavaScriptCallback
     */
    protected $onclick;
    
    /**
     * List of subitems for this action
     *
     * @var NamedList
     */
    protected $subitems;
    
    /**
     * Array of additional properties
     *
     * @var array
     */
    protected $additional = array();
  
    /**
     * Construct new wireframe action
     * 
     * @param unknown_type $text
     * @param unknown_type $url
     * @param unknown_type $additional
     */
    function __construct($text, $url, $additional = null) {
      $this->text = $text;
      $this->url = $url;
      
      $this->subitems = new NamedList();
      
      if($additional && is_foreachable($additional)) {
        foreach($additional as $k => $v) {
          switch($k) {
            case 'subitems':
              foreach($additional['subitems'] as $_k => $_v) {
                $this->subitems->add($_k, $_v);
              } // foreach
              break;
            case 'icon':
              $this->icon = $additional['icon'];
              break;
            case 'onclick':
              $this->setOnClick($additional['onclick']);
              break;
            default:
              $this->additional[$k] = $v;
          } // switch
        } // foreach
      } // if
    } // __construct
    
    /**
     * Return action text
     * 
     * @return string
     */
    function getText() {
      return $this->text;
    } // getText
    
    /**
     * Return URL
     * 
     * @return string
     */
    function getUrl() {
      return $this->url;
    } // getUrl
    
    /**
     * Return item ID
     * 
     * @param string $default
     * @return string
     */
    function getId($default = null) {
      return $this->getAdditionalProperty('id', $default);
    } // getId
    
    /**
     * Return value of additional property
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function getAdditionalProperty($name, $default = null) {
      return isset($this->additional[$name]) ? $this->additional[$name] : $default;
    } // getAdditionalProperty
    
    /**
     * Set additional property value
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function setAdditionalProperty($name, $value) {
      $this->additional[$name] = $value;
      
      return $value;
    } // setAdditionalProperty
    
    // ---------------------------------------------------
    //  Icon
    // ---------------------------------------------------
    
    /**
     * Return icon URL, for a given interface
     * 
     * @param string $interface
     * @return string
     */
    function getIcon($interface = null) {
      if($interface === null) {
        $interface = AngieApplication::getPreferedInterface();
      } // if
      
      if($this->icon === null || is_string($this->icon)) {
        return $this->icon;
      } elseif(is_array($this->icon)) {
        return $this->icon[$interface];
      } else {
        return null;
      } // if
    } // getIcon
    
    // ---------------------------------------------------
    //  Onclick
    // ---------------------------------------------------
    
    /**
     * Return on click callback instance
     * 
     * @return IJavaScriptCallback
     */
    function getOnClick() {
      return $this->onclick;
    } // getOnClick
    
    /**
     * Set on click callback instance
     * 
     * @param IJavaScriptCallback $value
     * @return IJavaScriptCallback
     */
    function setOnClick(IJavaScriptCallback $value) {
      if($value instanceof IJavaScriptCallback || $value === null) {
        $this->onclick = $value;
      } else {
        throw new InvalidInstanceError('value', $value, 'IJavaScriptCallback');
      } // if
    } // setOnClick
    
    // ---------------------------------------------------
    //  Subitems
    // ---------------------------------------------------
    
    /**
     * Returns true if this action has subitems attached to it
     * 
     * @return boolean
     */
    function hasSubitems() {
      return $this->subitems->count() > 0;
    } // hasSubitems
    
    /**
     * Returns list of action subitems
     * 
     * @return NamedList
     */
    function getSubitems() {
      return $this->subitems;
    } // getSubitems
    
    // ---------------------------------------------------
    //  Interface implemnetaiton
    // ---------------------------------------------------
    
    /**
     * Convert current object to JSON
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return string
     */
    function toJSON(IUser $user, $detailed = false, $for_interface = false) {
      return JSON::encode(array(
        'text' => $this->text, 
        'url' => $this->url,
      	'icon' => $this->getIcon(), 
      	'subitems' => $this->subitems->count() ? $this->subitems : null, 
        'onclick' => $this->onclick, 
      ), $user, $detailed, $for_interface);
    } // toJSON
    
  }