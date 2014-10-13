<?php

  /**
   * Framework level implementation
   *
   * @package angie.frameworks.labels
   * @subpackage models
   */
  abstract class ILabelImplementation {
    
    /**
     * Parent object instance
     *
     * @var ILabel
     */
    protected $object;
    
    /**
     * Construct labels implementation helper
     *
     * @param ILabel $object
     * @param array $additional
     */
    function __construct(ILabel &$object, $additional = null) {
      $this->object = $object;
    } // __construct
    
    /**
     * Return label for the given object
     *
     * @return Label
     */
    function get() {
    	if($this->getLabelType() && $this->object->getLabelId()) {
    		return DataObjectPool::get('Label', $this->object->getLabelId());
    	} else {
    		return null;
    	} // if
    } // get
    
    /**
     * Set label
     *
     * @param Label $value
     * @param bool $save
     * @return Label
     */
    function set($value, $save = false) {
      $label_id = 0;

      if($value instanceof Label) {
        $label_id = $value->getId();
      } else {
        if($value) {
          $value = Labels::findById($value);
          if($value instanceof Label) {
            $label_id = $value->getId();
          } // if
        } // if
      } // if
      
      if($this->object->fieldExists('label_id')) {
        $this->object->setLabelId($label_id);
        
        if($save) {
          $this->object->save();
        } // if
      } // if
      
      return $this->get();
    } // set
    
    /**
     * Return new label instance for this specific implementation
     *
     * @return Label
     */
    abstract function newLabel();
    
    /**
     * Return type of label used
     * 
     * @return string
     */
    public function getLabelType() {
    	return get_class($this->newLabel());
    } // getLabelType
    
    /**
     * Update label url
     * 
     * @return string
     */
    public function getUpdateLabelUrl() {
			return Router::assemble($this->object->getRoutingContext() . '_update_label', $this->object->getRoutingContextParams());
    } // getUpdateLabelUrl
    
    /**
     * Describe labels related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      if($detailed) {
        $result['label'] = $this->get() instanceof Label ? $this->get()->describe($user, false, $for_interface) : null;
      } else {
        $result['label_id'] = $this->object->getLabelId();
      } // if
      
      $result['urls']['update_label'] = $this->getUpdateLabelUrl();
    } // describe

    /**
     * Describe labels related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['label_id'] = $this->object->getLabelId();
      if($detailed) {
        $result['label'] = $this->get() instanceof Label ? $this->get()->describeForApi($user) : null;
        $result['urls']['update_label'] = $this->getUpdateLabelUrl();
      } // if
    } // describeForApi
    
  }