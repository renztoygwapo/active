<?php

  /**
   * Preview implementation
   *
   * @package angie.frameworks.preview
   * @subpackage models
   */
  abstract class IPreviewImplementation {
    
    /**
     * Parent instance
     *
     * @var IPreview
     */
    protected $object;
    
    /**
     * Construct preview helper
     *
     * @param IPreview $object
     */
    function __construct(IPreview $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Describe preview information
     * 
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     * @return array
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['preview']['icons'] = array(
        'small' => $this->getSmallIconUrl(), 
        'large' => $this->getLargeIconUrl()
      );
      
      if($detailed && $this->has()) {
        $result['preview']['rendered'] = array(
          'small' => $this->renderSmall(), 
          'large' => $this->renderLarge()
        );
      } // if
    } // describe

    /**
     * Describe preview information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     * @return array
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['preview']['icons'] = array(
        'small' => $this->getSmallIconUrl(),
        'large' => $this->getLargeIconUrl()
      );

      if($detailed && $this->has()) {
        $result['preview']['rendered'] = array(
          'small' => $this->renderSmall(),
          'large' => $this->renderLarge()
        );
      } // if
    } // describeForApi
    
    /**
     * Returns true if parent object has preview available
     *
     * @return boolean
     */
    abstract function has();
    
    /**
     * Returns true if preview for the parent object can be used in email 
     * messages
     * 
     * Non-safe previews are ones that use OBJECT or IFRAME elements, embed 
     * content etc (YouTube, Google Docs etc)
     * 
     * @return boolean
     */
    function isEmailFriendly() {
      return true;
    } // isEmailFriendly
    
    /**
     * Render large preview
     *
     * @return string
     */
    abstract function renderLarge();
    
    /**
     * Render small preview
     *
     * @return string
     */
    abstract function renderSmall();
    
    /**
     * Return small icon URL
     *
     * @return string
     */
    abstract function getSmallIconUrl();
    
    /**
     * Return large icon URL
     *
     * @return string
     */
    abstract function getLargeIconUrl();
    
  }