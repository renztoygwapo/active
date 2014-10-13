<?php

  /**
   * Class description
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IActiveCollabUserInspectorImplementation extends IUserInspectorImplementation {

    /**
     * Do load data for given interface
     *
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);

      $this->removeProperty('role');
      $this->removeProperty('email');
      $this->removeProperty('last_visit');
      $this->removeProperty('local_time');

      $this->addProperty('company', lang('Company'), new SimplePermalinkInspectorProperty($this->object, 'company.permalink', 'company.name'));
      $this->addProperty('title', lang('Title'), new SimpleFieldInspectorProperty($this->object, 'title'));
      if ($user->isPeopleManager()) {
        $this->addProperty('role', lang('Role'), new SimpleFieldInspectorProperty($this->object, 'role.name'));
      } // if

      if ($this->object->canContact($user)) {
        $this->addProperty('email', lang('Email'), new SimplePermalinkInspectorProperty($this->object, 'email', 'email'));
        $this->addProperty('work_phone', lang('Work #'), new SimpleFieldInspectorProperty($this->object, 'phone_work'));
        $this->addProperty('mobile_phone', lang('Mobile #'), new SimpleFieldInspectorProperty($this->object, 'phone_mobile'));
        $this->addProperty('instant_messenger', lang('IM'), new SimpleFieldInspectorProperty($this->object, 'im_value', array('label_field' => 'im_type')));
      } // if

      if ($this->object->getId() != $user->getId()) {
        $this->addProperty('last_visit', lang('Last Visit On'), new SimpleFieldInspectorProperty($this->object, 'last_activity_on.formatted_date'));
      } // if

      if($user->isPeopleManager() && (!($this->object->getInvitedOn() instanceof DateTimeValue) || $this->object->getInvitedOn() instanceof DateTimeValue && strtotime('+1 month', $this->object->getInvitedOn()->getTimestamp()) > DateTimeValue::now()->getTimestamp())) {
        $this->addProperty('invited_on', lang('Invited On'), new InvitedOnInspectorProperty($this->object));
      } // if

      $this->addProperty('local_time', lang('Local Time'), new SimpleFieldInspectorProperty($this->object, 'local_time'));

      $this->addWidget('avatar', lang('Avatar'), new AvatarInspectorWidget($this->object, $this->object->avatar()->getSizeName(IUserAvatarImplementation::SIZE_PHOTO)));
    } // do_load

  }