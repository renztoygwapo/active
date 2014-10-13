<?php

  /**
   * Base Project Inspector implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectInspectorImplementation extends IInspectorImplementation {

    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	parent::load($user, $interface);
    	
			$this->supports_body = false;
			$this->supports_actions = false;
			$this->body_field = 'overview';
			
			if ($interface != AngieApplication::INTERFACE_PRINTER) {
				$this->custom_renderer = '(function (wrapper) { App.Inspector.Renderers.Project(wrapper) })';
			} // if

			if($interface != AngieApplication::INTERFACE_DEFAULT) {
				$this->supports_indicators = false;
			} // if
    } // load
      
    /**
     * do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      $this->addIndicator('my_tasks', lang('My Tasks'), new HyperlinkInspectorIndicator($this->object, Router::assemble('project_user_tasks', array('project_slug' => $this->object->getSlug())), AngieApplication::getImageUrl('icons/16x16/my-assignments.png', 'system'), lang('My Tasks')));
      $this->addIndicator('my_subscriptions', lang('My Subscriptions'), new HyperlinkInspectorIndicator($this->object, Router::assemble('project_user_subscriptions', array('project_slug' => $this->object->getSlug())), AngieApplication::getImageUrl('icons/16x16/my-subscriptions.png', 'system'), lang('My Subscriptions')));
      if ($user->isFeedUser()) {
        $this->addIndicator('ical_feed', lang('iCalendar Feed'), new HyperlinkInspectorIndicator($this->object, Router::assemble('project_ical_subscribe', array('project_slug' => $this->object->getSlug())), AngieApplication::getImageUrl('icons/16x16/calendar.png', 'system'), lang('iCalendar Feed'), array(
          'flyout_type' => 'flyout',
          'flyout_options' => array(
            'width' => 'narrow'
          )
        )));
      }
      if($user instanceof User && $user->isFeedUser()) {
        $this->addIndicator('rss_feed', lang('RSS Feed'), new HyperlinkInspectorIndicator($this->object, $this->object->getRssUrl($user), AngieApplication::getImageUrl('icons/16x16/rss.png', 'environment'), lang('RSS Feed'), array('target' => '_blank')));
      } // if


      parent::do_load($user, $interface);

      $this->addProperty('client', lang('Client'), new SimplePermalinkInspectorProperty($this->object, 'company.permalink', 'company.name'));
      $this->addProperty('permalink', lang('Leader'), new SimplePermalinkInspectorProperty($this->object, 'leader.permalink', 'leader.display_name'));

      $this->addProperty('status', lang('Status'), new SimpleFieldInspectorProperty($this->object, 'status_verbose'));
      
      if(AngieApplication::isModuleLoaded('tracking') && $user instanceof User && $user->canSeeProjectBudgets()) {
        $this->addProperty('budget', lang('Budget'), new ProjectBudgetInspectorProperty($this->object));
      } // if

      $this->addProperty('based_on', lang('Based On'), new SimplePermalinkInspectorProperty($this->object, 'based_on.permalink', 'based_on.name'));
      $this->addProperty('label', lang('Label'), new LabelInspectorProperty($this->object));

      foreach(CustomFields::getEnabledCustomFieldsByType('Project') as $field_name => $details) {
        $this->addProperty($field_name, $details['label'], new CustomFieldInspectorProperty($this->object));
      } // foreach

      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        $avatar_size = IProjectAvatarImplementation::SIZE_BIG;
      } elseif($interface == AngieApplication::INTERFACE_PRINTER) {
        $avatar_size = IProjectAvatarImplementation::SIZE_LARGE;
      } else {
        $avatar_size = IProjectAvatarImplementation::SIZE_PHOTO;
      } // if
      
      $this->addWidget('avatar', lang('Avatar'), new AvatarInspectorWidget($this->object, $this->object->avatar()->getSizeName($avatar_size)));
    } // do_load
    
  }