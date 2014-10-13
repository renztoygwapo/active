<?php

  /**
   * Base User Inspector implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class IUserInspectorImplementation extends IInspectorImplementation {

    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	parent::load($user, $interface);
    	
			$this->supports_body = false;
			$this->supports_indicators = false;			
			
			if ($interface != AngieApplication::INTERFACE_PRINTER) {
				$this->custom_renderer = '(function (wrapper) { App.Inspector.Renderers.User(wrapper) })';
			} // if
    } // load
      
    /**
     * Do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {      
      parent::do_load($user, $interface);

      $this->addProperty('role', lang('Role'), new SimpleFieldInspectorProperty($this->object, 'role.name'));
      $this->addProperty('email', lang('Email'), new SimplePermalinkInspectorProperty($this->object, 'email', 'email'));

      if ($this->object->getId() != $user->getId()) {
        $this->addProperty('last_visit', lang('Last Visit On'), new SimpleFieldInspectorProperty($this->object, 'last_activity_on.formatted_date'));
      } // if

			$this->addProperty('local_time', lang('Local Time'), new SimpleFieldInspectorProperty($this->object, 'local_time'));
			
			$this->addWidget('avatar', lang('Avatar'), new AvatarInspectorWidget($this->object, $this->object->avatar()->getSizeName(IUserAvatarImplementation::SIZE_PHOTO)));
    } // do_load
    
  }