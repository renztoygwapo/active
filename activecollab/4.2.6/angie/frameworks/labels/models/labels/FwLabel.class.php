<?php

  /**
   * Framework level label row implementation
   *
   * @package angie.frameworks.labels
   * @subpackage models
   */
  abstract class FwLabel extends BaseLabel implements IRoutingContext {
    
    /**
     * List of accepted fields
     *
     * @var array
     */
  	protected $accept = array('name', 'is_default');
    
    /**
     * Set if you wish to have name always uppercased for this label type
     *
     * @var boolean
     */
    protected $always_uppercase = true;
    
    /**
     * Set if you wish this type of labels to suppor foreground colors
     *
     * @var boolean
     */
    protected $supports_fg_color = true;
    
    /**
     * Set if you wish this type of labels to suppor background colors
     *
     * @var boolean
     */
    protected $supports_bg_color = true;
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'label' : 'labels';
    } // getBaseTypeName
    
    /**
     * Render label
     *
     * @param bool $short_label
     * @return string
     */
    function render($short_label = false) {
      $fg_color = $this->supports_fg_color ? $this->getForegroundColor() : null;
      $bg_color = $this->supports_bg_color ? $this->getBackgroundColor() : null;
      
      $style = '';
      if($fg_color) {
        $style .= "color: $fg_color;";
      } // if
      
      if($bg_color) {
        $style .= "background-color: $bg_color";
      } // if
      
      $label = $this->getName();
      if($this->always_uppercase) {
        $label = function_exists('mb_strtoupper') ? mb_strtoupper($label) : strtoupper($label);
      } // if

      if ($short_label) {
        $str = '<span class="label_tag" title="' . $this->getName() . '"><span class="label_background" style="background-color: ' . $bg_color . '"></span><span class="label_overlay"></span></span>';
      } else {
        $str = HTML::openTag('span', array(
          'class' => 'pill',
          'style' => $style,
        ), $label);
      } // if
      
      return $str;
    } // render
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['fg_color'] = $this->getForegroundColor();
      $result['bg_color'] = $this->getBackgroundColor();
      $result['always_uppercase'] = $this->getAlwaysUppercase();
      $result['is_default'] = $this->getIsDefault();
      
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'fg_color' => $this->getForegroundColor(),
        'bg_color' => $this->getBackgroundColor(),
        'type' => get_class($this), // Legacy (for grouping in Timer), @todo
        'is_default' => $this->getIsDefault(),
      );
    } // describeForApi
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Set object attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('fg_color', $attributes)) {
        $this->setForegroundColor($attributes['fg_color']);
      } // if
      
      if(array_key_exists('bg_color', $attributes)) {
        $this->setBackgroundColor($attributes['bg_color']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return foreground color
     *
     * @return string
     */
    function getForegroundColor() {
      return $this->supports_fg_color ? $this->getAdditionalProperty('fg_color') : null;
    } // getForegroundColor
    
    /**
     * Set foreground color
     *
     * @param string $value
     * @return string
     */
    function setForegroundColor($value) {
      return $this->supports_fg_color ? $this->setAdditionalProperty('fg_color', $value) : null;
    } // setForegroundColor
    
    /**
     * Return background_color
     *
     * @return string
     */
    function getBackgroundColor() {
    	return $this->supports_bg_color ? $this->getAdditionalProperty('bg_color') : null;
    } // getBackgroundColor
    
    /**
     * Set background_color
     *
     * @param string $value
     * @return string
     */
    function setBackgroundColor($value) {
      return $this->supports_bg_color ? $this->setAdditionalProperty('bg_color', $value) : null;
    } // setBackgroundColor

    /**
     * Return true if name of this label needs to be displayed in uppercase
     *
     * @return bool
     */
    function getAlwaysUppercase() {
      return (boolean) $this->always_uppercase;
    } // getAlwaysUppercase
    
     // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can define a new label
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user->isAdministrator();
    } // canAdd
    
    /**
     * Returns true if $user can update this label
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user instanceof User && $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this label
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return !$this->getIsDefault();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return set as default currency URL
     *
     * @return string
     */
    function getSetAsDefaultUrl() {
      $params = $this->getRoutingContextParams();
      
      if(empty($params)) {
        $params = array('label_id' => $this->getId());
      } else {
        $params['label_id'] = $this->getId();
      } // if
      
      return Router::assemble($this->getRoutingContext() . '_set_as_default', $params);
    } // getSetAsDefaultUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('type', 'name')) {
          $errors->addError(lang('Name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Name is required'), 'name');
      } // if
    } // validate
    
    /**
     * Save label
     */
    function save() {
      $save = parent::save();

      AngieApplication::cache()->removeByModel('labels');
      AngieApplication::cache()->remove($this->getType());
      AngieApplication::cache()->remove('labels_id_name_map');

      return $save;
    } // save
  }