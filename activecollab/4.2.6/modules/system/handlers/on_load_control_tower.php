<?php

/**
 * on_load_control_tower event handler
 *
 * @package angie.frameworks.system
 * @subpackage handlers
 */

/**
 * Handle on_load_control_tower event
 *
 * @param ControlTower $control_tower
 * @param User $user
 */
function system_handle_on_load_control_tower(ControlTower &$control_tower, User &$user) {

  // ---------------------------------------------------
  //  Email to Comment
  // ---------------------------------------------------

  $reply_to_comment_label = null;
  if(ConfigOptions::getValue('control_tower_check_for_new_version') && !AngieApplication::isOnDemand()) {

    $control_tower->widgets()->add('check_for_new_version', array(
      'label' => lang('activeCollab Version'),
      'renderer' => function() {
        $updated_on = DateTimeValue::makeFromTimestamp(ConfigOptions::getValue('license_details_updated_on'));
        $settings = array(
          'id'                          => 'control_tower_widget_check_for_new_version',
          'current_version'             => AngieApplication::getVersion(),
          'latest_version'              => ConfigOptions::getValue('latest_version'),
          'latest_available_version'    => ConfigOptions::getValue('latest_available_version'),
          'new_modules_available'       => ConfigOptions::getValue('new_modules_available'),
          'license_details_updated_on'  => $updated_on, // $updated_on,
          'new_version_details_url'     => Router::assemble('new_version_details'),
          'update_url'                  => Router::assemble('application_update'),
          'update_now_url'              => Router::assemble('admin', array('check_for_new_version' => true))
        );
        return '<script type="text/javascript">App.widgets.controlTowerVersionInfo(' . JSON::encode($settings) . ');</script>';
      }
    ));
  } // if

} // system_handle_on_load_control_tower