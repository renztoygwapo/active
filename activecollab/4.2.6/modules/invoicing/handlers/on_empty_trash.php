<?php

/**
 * on_empty_trash event handler
 *
 * @package activeCollab.modules.invoicing
 * @subpackage handlers
 */

/**
 * Handle on_empty_trash event
 *
 * @param NamedList $sections
 * @param User $user
 */
function invoicing_handle_on_empty_trash(User &$user) {

  // delete trashed recurring profiles
  RecurringProfiles::deleteTrashed($user);

} // invoicing_handle_on_empty_trash