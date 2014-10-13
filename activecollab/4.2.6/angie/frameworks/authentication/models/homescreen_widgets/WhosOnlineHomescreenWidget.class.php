<?php

  /**
   * Show list of people who were online in the past N minutes
   * 
   * @package angie.frameworks.auth
   * @subpackage models
   */
  class WhosOnlineHomescreenWidget extends HomescreenWidget {
    
    /**
     * Return widget name
     * 
     * @return string
     */
    function getName() {
      return lang('Who is Online?');
    } // getName
    
    /**
     * Return widget description
     * 
     * @return string
     */
    function getDescription() {
      return 'Display a list of users who visited the system in the last N minutes';
    } // getDescription
    
    /**
     * Return widget body
     * 
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      $online_users = Users::findWhoIsOnline($user, $this->getMinutes());
      
      switch($this->getMinutes()) {
        case 1:
          $minutes = lang('one minute');
          break;
        case 60:
          $minutes = lang('hour');
          break;
        default:
          $minutes = lang(':num minutes', array('num' => $this->getMinutes()));
      } // if
      
      if($online_users) {
        $result = '<p>' . lang('People who were online in the last :minutes', array('minutes' => $minutes)) . ':</p>';
        $result .= '<ul>';
        
        foreach($online_users as $online_user) {
          $result .= '<li class="with_image"><img src="' . $online_user->avatar()->getUrl(IAvatarImplementation::SIZE_SMALL) . '">' . object_link($online_user, null, array('class' => 'quick_view_item')) . '</li>';
        } // foreach
        
        return "$result</ul>";
      } else {
        return '<p>' . lang('Nobody was online in the last :minutes', array('minutes' => $minutes)) . '</p>';
      } // if
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
      $view = SmartyForAngie::getInstance()->createTemplate(get_view_path('_whos_online_options', null, AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
      
      $view->assign(array(
        'widget' => $this, 
        'user' => $user, 
        'widget_data' => array('minutes' => $this->getMinutes()), 
      ));
      
      return $view->fetch();
    } // renderOptions
    
    /**
     * Bulk set widget attributes
     * 
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(array_key_exists('minutes', $attributes)) {
        $this->setMinutes((integer) array_var($attributes, 'minutes', 15, true));
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
    /**
     * Return minutes option value
     * 
     * @return integer
     */
    function getMinutes() {
      return (integer) $this->getAdditionalProperty('minutes', 15);
    } // getMinutes
    
    /**
     * Set minutes value
     * 
     * @param integer $minutes
     * @return integer
     */
    function setMinutes($minutes) {
      if($minutes < 1) {
        $minutes = 15;
      } // if
      
      if($minutes > 60) {
        $minutes = 60;
      } // if
      
      return $this->setAdditionalProperty('minutes', $minutes);
    } // setMinutes
    
  }