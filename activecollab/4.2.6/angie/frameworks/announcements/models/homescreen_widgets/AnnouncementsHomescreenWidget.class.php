<?php

  /**
   * Home screen widget that displays announcements
   *
   * @package angie.frameworks.announcements
   * @subpackage models
   */
  class AnnouncementsHomescreenWidget extends HomescreenWidget {

    /**
     * Return widget name
     *
     * @return string
     */
    function getName() {
      return lang('Announcements');
    } // getName

    /**
     * Return widget description
     *
     * @return string
     */
    function getDescription() {
      return lang("Displays announcements to user that has this widget on his homescreen");
    } // getDescription

    /**
     * Return widget body
     *
     * @param IUser $user
     * @param string $widget_id
     * @param string $column_wrapper_class
     * @return string
     */
    function renderBody(IUser $user, $widget_id, $column_wrapper_class = null) {
      AngieApplication::useHelper('user_announcements', ANNOUNCEMENTS_FRAMEWORK);

      return smarty_function_user_announcements(array(
        'user' => $user,
      ), SmartyForAngie::getInstance());
    } // renderBody

  }