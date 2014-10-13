<?php

/**
 * on_trash_sections event handler
 *
 * @package activeCollab.modules.invoicing
 * @subpackage handlers
 */

/**
 * Handle on_trash_sections event
 *
 * @param NamedList $sections
 * @param array $map
 * @param User $user
 */
function invoicing_handle_on_trash_sections(NamedList &$sections, &$map, User &$user) {
  $trashed_recurring_profiles = RecurringProfiles::findTrashed($user, $map);
  if ($trashed_recurring_profiles) {
    $sections->add('recurring_profiles', array(
      'label' => lang('Recurring Profiles'),
      'count' => count($trashed_recurring_profiles),
      'items' => $trashed_recurring_profiles
    ));
  } // if
} // invoicing_handle_on_trash_sections