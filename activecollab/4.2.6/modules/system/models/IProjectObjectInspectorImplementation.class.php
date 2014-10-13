<?php

  /**
   * Base project object inspector implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class IProjectObjectInspectorImplementation extends IInspectorImplementation {

    /**
     * does the real loading
     *
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      if($this->getRenderScope() == IInspectorImplementation::RENDER_SCOPE_QUICK_VIEW) {
        $this->addProperty('project', lang('Project'), new ProjectInspectorProperty($this->object));
      } // if

      parent::do_load($user, $interface);
    } // do_load

  }