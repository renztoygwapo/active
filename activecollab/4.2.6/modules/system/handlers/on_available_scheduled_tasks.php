<?php

  /**
   * on_available_scheduled_tasks event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * On available scheduled tasks
   *
   * @param array $tasks
   */
  function system_handle_on_available_scheduled_tasks(&$tasks) {
    $last_activity = ConfigOptions::getValue('morning_paper_last_activity');

    $tasks['paper'] = array(
      'text' => lang('Morning Paper'),
      'last_activity' => $last_activity ? new DateTimeValue((integer) $last_activity) : null,
    );
  } // system_handle_on_available_scheduled_tasks