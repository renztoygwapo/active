<?php

  /**
   * Application level status bar implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class StatusBar extends FwStatusBar {
  
    /**
     * Load status bar
     * 
     * @param IUser $user
     */
    function load(IUser $user) {
      if($this->isLoaded()) {
        return;
      } // if
      
      $this->add('quick-add', lang('Quick Add'), Router::assemble('quick_add'), AngieApplication::getImageUrl('status-bar/quick-add.png', SYSTEM_MODULE), array(
      	'onclick' => new QuickAddCallback(),
        'hotkey' => 'q',
      ));

      parent::load($user);

      $add_branding = AngieApplication::isOnDemand() ? !$user->isAdministrator() : !AngieApplication::getAdapter()->getBrandingRemoved();
      if ($add_branding) {
      	$this->add('branding', 'Powered', 'https://www.activecollab.com/r/backend', AngieApplication::getApplicationBrandImageUrl('footer-branding.png'), array(
	        'group' => StatusBar::GROUP_RIGHT,
	      	'target' => '_blank'
      	));
      } // if

    } // load
    
  }