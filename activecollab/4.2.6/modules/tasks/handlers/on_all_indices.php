<?php
/**
 * on_all_indices event handler
 *
 * @package activeCollab.modules.tasks
 * @subpackage handlers
 */

/**
 * Handle on_all_indices event
 *
 * @param array $indices
 */
function tasks_handle_on_all_indices(&$indices) {
  $indices[] = array(
    'name' => lang('Task IDs'),
    'icon' => AngieApplication::getImageUrl('admin_panel/tasks.png', TASKS_MODULE),
    'rebuild_url' => Router::assemble('tasks_admin_resolve_duplicate_id'),
  );
} // tasks_handle_on_all_indices