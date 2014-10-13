<?php

  /**
   * on_phone_homescreen event handler
   * 
   * @package activeCollab.modules.status
   * @subpackage handlers
   */

  /**
   * Handle on_phone_homescreen event
   * 
   * @param NamedList $items
   * @param IUser $user
   */
  function status_handle_on_phone_homescreen(NamedList &$items, IUser &$user) {
    if(StatusUpdates::canUse($user)) {
      $items->add('status', array(
        'text' => lang('Status Updates'),
      	'url' => Router::assemble('status_updates'),
      	'icon' => AngieApplication::getImageUrl('icons/homescreen/status.png', STATUS_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // status_handle_on_phone_homescreen