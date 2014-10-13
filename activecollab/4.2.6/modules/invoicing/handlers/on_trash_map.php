<?php

/**
 * on_trash_map event handler
 *
 * @package activeCollab.modules.invoicing
 * @subpackage handlers
 */

/**
 * Handle on_trash_map event
 *
 * @param NamedList $sections
 * @param array $map
 * @param User $user
 */
function invoicing_handle_on_trash_map(&$map, User &$user) {

  $map = array_merge(
    (array) $map,
    (array) RecurringProfiles::getTrashedMap($user)
  );

} // invoicing_handle_on_trash_map