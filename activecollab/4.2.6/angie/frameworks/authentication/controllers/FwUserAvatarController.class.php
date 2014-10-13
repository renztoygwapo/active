<?php

  // Build on top of avatar controller implementation
  AngieApplication::useController('avatar', AVATAR_FRAMEWORK_INJECT_INTO);

  /**
   * User avatar controller
   *
   * @package angie.frameworks.authentication
   * @subpackage controllers
   */
  abstract class FwUserAvatarController extends AvatarController {

  }