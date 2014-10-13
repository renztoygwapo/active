<?php

  // Build on top of avatar controller implementation
  AngieApplication::useController('fw_avatar', AVATAR_FRAMEWORK);

  /**
   * Application level avatar controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AvatarController extends FwAvatarController {

  }