<?php

  /**
   * Home screen widget that displays reminders
   *
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  class RemindersHomescreenWidget extends HomescreenWidget {

    /**
     * Return widget name
     *
     * @return string
     */
    function getName() {
      return lang('Reminders');
    } // getName

    /**
     * Return widget description
     *
     * @return string
     */
    function getDescription() {
      return lang("Displays a list of reminders sent to user that has this widget on his home screen");
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
      AngieApplication::useHelper('user_reminders', REMINDERS_FRAMEWORK);

      return smarty_function_user_reminders(array(
        'user' => $user,
      ), SmartyForAngie::getInstance());
    } // renderBody

  }