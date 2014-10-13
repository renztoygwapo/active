<?php

  /**
   * Owner company identity home screen widget
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class WelcomeHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Welcome Message');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return 'Show company logo and welcome message to users. This widget is used to welcome users, as well as to show of your corporate branding to them';
    } // getDescription
    
    /**
     * Return widget title
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderTitle(IUser $user, $widget_id, $column_wrapper_class = null) {
      return lang('Welcome to :name', array(
        'name' => $this->getIdentityName()
      ));
    } // renderTitle
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $view = SmartyForAngie::getInstance()->createTemplate(AngieApplication::getViewPath('welcome_body', 'homescreen_widgets', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      list($logo_small, $logo_medium, $logo_large, $logo_larger, $logo_photo) = $this->getIdentityLogo();
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'name' => $this->getIdentityName(),
        'logo_url' => $logo_larger,
        'welcome_message' => $this->getWelcomeMessage(),
        'logo_on_white' => $this->getLogoOnWhite(),
      ));
      
      return $view->fetch();
    } // renderBody
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Returns true if this widget has additional options
     * 
     * @return boolean
     */
    function hasOptions() {
      return true;
    } // hasOptions
    
    /**
     * Render widget options form section
     * 
     * @param IUser $user
     * @return string
     */
    function renderOptions(IUser $user) {
      $view = SmartyForAngie::getInstance()->createTemplate(AngieApplication::getViewPath('welcome_options', 'homescreen_widgets', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      list($logo_small, $logo_medium, $logo_large, $logo_larger, $logo_photo) = $this->getIdentityLogo();
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'widget_data' => array(
          'name' => $this->getIdentityName(), 
          'logo_small' => $logo_small, 
          'logo_medium' => $logo_medium, 
          'logo_large' => $logo_large,
          'logo_larger' => $logo_larger,
          'logo_photo' => $logo_photo, 
          'welcome_message' => $this->getWelcomeMessage(),
          'logo_on_white' => $this->getLogoOnWhite(),
        ), 
      ));
      
      return $view->fetch();
    } // renderOptions
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      $this->setWelcomeMessage(isset($attributes['welcome_message']) ? $attributes['welcome_message'] : null);
      $this->setLogoOnWhite(isset($attributes['logo_on_white']) ? $attributes['logo_on_white'] : false);

      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Cached identity name value
     *
     * @var string
     */
    private $identity_name = false;
    
    /**
     * Return identity name value
     * 
     * @return string
     */
    protected  function getIdentityName() {
      if($this->identity_name === false) {
        $this->identity_name = ConfigOptions::getValue('identity_name');
      } // if
      
      return $this->identity_name;
    } // getIdentityName
    
    /**
     * Cached company logo value
     *
     * @var array
     */
    private $identity_logo = false;
    
    /**
     * Return company logo
     * 
     * @return string
     */
    protected function getIdentityLogo() {
      if($this->identity_logo === false) {
        $this->identity_logo = array(
          AngieApplication::getBrandImageUrl('logo.16x16.png'), 
          AngieApplication::getBrandImageUrl('logo.40x40.png'), 
          AngieApplication::getBrandImageUrl('logo.80x80.png'), 
          AngieApplication::getBrandImageUrl('logo.128x128.png'),
          AngieApplication::getBrandImageUrl('logo.256x256.png'),
        );
      } // if
      
      return $this->identity_logo;
    } // getIdentityLogo
    
    /**
     * Return welcome message
     * 
     * @return string
     */
    function getWelcomeMessage() {
      return $this->getAdditionalProperty('welcome_message');
    } // getWelcomeMessage
    
    /**
     * Set welcome message
     * 
     * @param string $value
     * @return string
     */
    function setWelcomeMessage($value) {
      return $this->setAdditionalProperty('welcome_message', $value);
    } // setWelcomeMessage

    /**
     * Return whether logo should be on white background or not
     *
     * @return boolean
     */
    function getLogoOnWhite() {
      return (boolean) $this->getAdditionalProperty('logo_on_white', true);
    } // getLogoOnWhite

    /**
     * Set whether logo should be on white background
     *
     * @param boolean $value
     * @return boolean
     */
    function setLogoOnWhite($value) {
      return $this->setAdditionalProperty('logo_on_white', (boolean) $value);
    } // getLogoOnWhite
  
  }