<?php

  /**
   * Base Company Inspector implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ICompanyInspectorImplementation extends IInspectorImplementation {
    
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
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);
      
      if ($this->object->canContact($user)) {
      	$this->addProperty('address', lang('Address'), new SimpleFieldInspectorProperty($this->object, 'office_address', array('modifier' => 'App.nl2br')));
      	$this->addProperty('office_phone', lang('Phone Number'), new SimpleFieldInspectorProperty($this->object, 'office_phone'));
      	$this->addProperty('office_fax', lang('Fax Number'), new SimpleFieldInspectorProperty($this->object, 'office_fax'));
        $this->addProperty('homepage', lang('Website'), new SimplePermalinkInspectorProperty($this->object, 'office_homepage', 'office_homepage'));
        $this->addProperty('note', lang('Note'), new SimpleFieldInspectorProperty($this->object, 'note', array('modifier' => 'App.nl2br')));
      } // if
      
      $this->addWidget('avatar', lang('Avatar'), new AvatarInspectorWidget($this->object, $this->object->avatar()->getSizeName(ICompanyAvatarImplementation::SIZE_PHOTO)));
    } // do_load
    
  }