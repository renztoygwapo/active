<?php

  /**
   * on_empty_trash event handler
   *
   * @package activeCollab.modules.tracking
   * @subpackage handlers
   */

  /**
   * Handle on_empty_trash event
   *
   * @param NamedList $sections
   * @param User $user
   */
  function tracking_handle_on_empty_trash(User &$user) {

    // delete trashed time records
    TimeRecords::deleteTrashed($user);
    
    // delete trashed expenses
    Expenses::deleteTrashed($user);
    
  } // tracking_handle_on_empty_trash