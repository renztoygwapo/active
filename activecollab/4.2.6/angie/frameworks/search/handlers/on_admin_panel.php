<?php

  /**
   * on_admin_panel event handler
   * 
   * @package activeCollab.frameworks.search
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function search_handle_on_admin_panel(AdminPanel &$admin_panel) {
//    $admin_panel->addToTools('search_settings', lang('Search Engine'), Router::assemble('search_settings'), AngieApplication::getImageUrl('admin_panel/search-settings.png', SEARCH_FRAMEWORK), array(
//      'onclick' => new FlyoutFormCallback(array(
//        'success_event' => 'search_settings_updated',
//        'success_message' => lang('Settings updated'),
//      )),
//    ));
  } // search_handle_on_admin_panel