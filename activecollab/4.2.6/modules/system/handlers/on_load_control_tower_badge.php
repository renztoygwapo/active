<?php

/**
 * on_load_control_tower_badge event handler implementation
 *
 * @package angie.frameworks.system
 * @subpackage handlers
 */

/**
 * Handle on_load_control_tower_badge
 *
 * @param integer $badge_value
 * @param User $user
 */
function system_handle_on_load_control_tower_badge(&$badge_value, User &$user) {
  if($user->isAdministrator() && !AngieApplication::isOnDemand()) {
    if(ConfigOptions::getValue('control_tower_check_for_new_version')) {
      $current_version = AngieApplication::getVersion();
      $latest_version = ConfigOptions::getValue('latest_version');
      $new_modules = ConfigOptions::getValue('new_modules_available');

      if ($current_version != 'current' && version_compare($latest_version, $current_version) > 0) {
        $badge_value++;
      } else if ($new_modules) {
        $badge_value++;
      } // if
    } // if
  } // if
} // system_handle_on_load_control_tower_badge