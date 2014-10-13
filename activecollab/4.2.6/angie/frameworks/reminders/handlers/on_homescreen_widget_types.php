<?php

/**
 * on_homescreen_widget_types event handler
 *
 * @package angie.frameworks.reminders
 * @subpackage handlers
 */

/**
 * Handle on_homescreen_widget_types event
 *
 * @param array $types
 * @param IUser $user
 */
function reminders_handle_on_homescreen_widget_types(&$types, IUser &$user) {
  $types[] = new RemindersHomescreenWidget();
} // reminders_handle_on_homescreen_widget_types