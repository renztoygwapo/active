<?php
  
  return function ($path, $query_string) {
    $matches = null;
    if(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/discussions$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_discussions', 'admin/indices/object-contexts/rebuild/discussions', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_discussions', 'module' => 'discussions', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/discussions$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_discussions', 'admin/indices/activity-logs/rebuild/discussions', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_discussions', 'module' => 'discussions', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_copy_to_project', 'projects/:project_slug/discussions/:discussion_id/copy-to-project', array ( 'controller' => 'discussions', 'action' => 'project_discussion_copy_to_project', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_move_to_project', 'projects/:project_slug/discussions/:discussion_id/move-to-project', array ( 'controller' => 'discussions', 'action' => 'project_discussion_move_to_project', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/sharing$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_sharing_settings', 'projects/:project_slug/discussions/:discussion_id/sharing', array ( 'controller' => 'discussions', 'action' => 'project_discussion_sharing_settings', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_untrash', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/untrash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_state_untrash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_trash', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/trash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_state_trash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_unarchive', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_state_unarchive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_archive', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/archive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_state_archive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_preview', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/preview', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_preview_content', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_download', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/download', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_download_content', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_delete', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachment_state_delete', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment_edit', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id/edit', array ( 'controller' => 'discussions', 'action' => 'project_discussion_edit_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachment', 'projects/:project_slug/discussions/:discussion_id/attachments/:attachment_id', array ( 'controller' => 'discussions', 'action' => 'project_discussion_view_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachments_add', 'projects/:project_slug/discussions/:discussion_id/attachments/add', array ( 'controller' => 'discussions', 'action' => 'project_discussion_add_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_attachments', 'projects/:project_slug/discussions/:discussion_id/attachments', array ( 'controller' => 'discussions', 'action' => 'project_discussion_attachments', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminder_delete', 'projects/:project_slug/discussions/:discussion_id/reminders/:reminder_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_delete_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminder_dismiss', 'projects/:project_slug/discussions/:discussion_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'discussions', 'action' => 'project_discussion_dismiss_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminder_send', 'projects/:project_slug/discussions/:discussion_id/reminders/:reminder_id/send', array ( 'controller' => 'discussions', 'action' => 'project_discussion_send_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminder_edit', 'projects/:project_slug/discussions/:discussion_id/reminders/:reminder_id/edit', array ( 'controller' => 'discussions', 'action' => 'project_discussion_edit_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminder', 'projects/:project_slug/discussions/:discussion_id/reminders/:reminder_id', array ( 'controller' => 'discussions', 'action' => 'project_discussion_view_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminders_nudge', 'projects/:project_slug/discussions/:discussion_id/reminders/nudge', array ( 'controller' => 'discussions', 'action' => 'project_discussion_nudge_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminders_add', 'projects/:project_slug/discussions/:discussion_id/reminders/add', array ( 'controller' => 'discussions', 'action' => 'project_discussion_add_reminder', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_reminders', 'projects/:project_slug/discussions/:discussion_id/reminders', array ( 'controller' => 'discussions', 'action' => 'project_discussion_reminders', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_unsubscribe_all', 'projects/:project_slug/discussions/:discussion_id/unsubscribe_all', array ( 'controller' => 'discussions', 'action' => 'project_discussion_unsubscribe_all', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_unsubscribe', 'projects/:project_slug/discussions/:discussion_id/unsubscribe', array ( 'controller' => 'discussions', 'action' => 'project_discussion_unsubscribe', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_subscribe', 'projects/:project_slug/discussions/:discussion_id/subscribe', array ( 'controller' => 'discussions', 'action' => 'project_discussion_subscribe', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_subscriptions', 'projects/:project_slug/discussions/:discussion_id/subscriptions', array ( 'controller' => 'discussions', 'action' => 'project_discussion_manage_subscriptions', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_untrash', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/untrash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_state_untrash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_trash', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/trash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_state_trash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_unarchive', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/unarchive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_state_unarchive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_archive', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/archive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_state_archive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_untrash', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_state_untrash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_trash', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_state_trash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_unarchive', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_state_unarchive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_archive', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_state_archive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_preview', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_preview_content', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_download', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_download_content', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_delete', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachment_state_delete', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment_edit', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_edit_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachment', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_view_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachments_add', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments/add', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_add_attachment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_attachments', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/attachments', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_attachments', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_delete', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comment_state_delete', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment_edit', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id/edit', array ( 'controller' => 'discussions', 'action' => 'project_discussion_edit_comment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comment', 'projects/:project_slug/discussions/:discussion_id/comments/:comment_id', array ( 'controller' => 'discussions', 'action' => 'project_discussion_view_comment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comments_unlock', 'projects/:project_slug/discussions/:discussion_id/comments/unlock', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comments_unlock', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comments_lock', 'projects/:project_slug/discussions/:discussion_id/comments/lock', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comments_lock', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comments_add', 'projects/:project_slug/discussions/:discussion_id/comments/add', array ( 'controller' => 'discussions', 'action' => 'project_discussion_add_comment', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_comments', 'projects/:project_slug/discussions/:discussion_id/comments', array ( 'controller' => 'discussions', 'action' => 'project_discussion_comments', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/update-category$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_update_category', 'projects/:project_slug/discussions/update-category', array ( 'controller' => 'discussions', 'action' => 'project_discussion_update_category', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_category_delete', 'projects/:project_slug/discussions/categories/:category_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_delete_category', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_category_edit', 'projects/:project_slug/discussions/categories/:category_id/edit', array ( 'controller' => 'discussions', 'action' => 'project_discussion_edit_category', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_category', 'projects/:project_slug/discussions/categories/:category_id', array ( 'controller' => 'discussions', 'action' => 'project_discussion_view_category', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_categories_add', 'projects/:project_slug/discussions/categories/add', array ( 'controller' => 'discussions', 'action' => 'project_discussion_add_category', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_categories', 'projects/:project_slug/discussions/categories', array ( 'controller' => 'discussions', 'action' => 'project_discussion_categories', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_delete', 'projects/:project_slug/discussions/:discussion_id/delete', array ( 'controller' => 'discussions', 'action' => 'project_discussion_state_delete', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_untrash', 'projects/:project_slug/discussions/:discussion_id/untrash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_state_untrash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_trash', 'projects/:project_slug/discussions/:discussion_id/trash', array ( 'controller' => 'discussions', 'action' => 'project_discussion_state_trash', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_unarchive', 'projects/:project_slug/discussions/:discussion_id/unarchive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_state_unarchive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_archive', 'projects/:project_slug/discussions/:discussion_id/archive', array ( 'controller' => 'discussions', 'action' => 'project_discussion_state_archive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/discussions$/', $path, $matches)) {
      return Router::doMatch($path, 'milestone_discussions', 'projects/:project_slug/milestones/:milestone_id/discussions', array ( 'controller' => 'milestone_discussions', 'action' => 'index', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/unpin$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_unpin', 'projects/:project_slug/discussions/:discussion_id/unpin', array ( 'controller' => 'discussions', 'action' => 'unpin', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/pin$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_pin', 'projects/:project_slug/discussions/:discussion_id/pin', array ( 'controller' => 'discussions', 'action' => 'pin', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion_edit', 'projects/:project_slug/discussions/:discussion_id/edit', array ( 'controller' => 'discussions', 'action' => 'edit', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussion', 'projects/:project_slug/discussions/:discussion_id', array ( 'controller' => 'discussions', 'action' => 'view', 'module' => 'discussions', ), array (
  0 => 'project_slug',
  1 => 'discussion_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussions_export', 'projects/:project_slug/discussions/export', array ( 'controller' => 'discussions', 'action' => 'export', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussions_add', 'projects/:project_slug/discussions/add', array ( 'controller' => 'discussions', 'action' => 'add', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussions_mass_edit', 'projects/:project_slug/discussions/mass-edit', array ( 'controller' => 'discussions', 'action' => 'mass_edit', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussions_archive', 'projects/:project_slug/discussions/archive', array ( 'controller' => 'discussions', 'action' => 'archive', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/discussions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_discussions', 'projects/:project_slug/discussions', array ( 'controller' => 'discussions', 'action' => 'index', 'module' => 'discussions', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/source$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_source', 'admin/indices/object-contexts/rebuild/source', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_source', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/source$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_source', 'admin/indices/activity-logs/rebuild/source', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_source', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/source\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'source_search_index_admin_build', 'admin/search/source/build', array ( 'controller' => 'source_search_index_admin', 'action' => 'build', 'search_index_name' => 'source', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/(\\d+)\\/usage$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repository_usage', '/admin/tools/source/mercurial-repositories/:source_repository_id/usage', array ( 'controller' => 'mercurial_source_admin', 'action' => 'usage', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repository_delete', '/admin/tools/source/mercurial-repositories/:source_repository_id/delete', array ( 'controller' => 'mercurial_source_admin', 'action' => 'delete', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repository_edit', '/admin/tools/source/mercurial-repositories/:source_repository_id/edit', array ( 'controller' => 'mercurial_source_admin', 'action' => 'edit', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repository', '/admin/tools/source/mercurial-repositories/:source_repository_id', array ( 'controller' => 'mercurial_source_admin', 'action' => 'view', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/test$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_test', '/admin/tools/source/mercurial-repositories/test', array ( 'controller' => 'mercurial_source_admin', 'action' => 'test_mercurial', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/test_connection$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repository_test_connection', '/admin/tools/source/mercurial-repositories/test_connection', array ( 'controller' => 'mercurial_source_admin', 'action' => 'test_repository_connection', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repositories_add', '/admin/tools/source/mercurial-repositories/add', array ( 'controller' => 'mercurial_source_admin', 'action' => 'add', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-repositories$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_repositories', '/admin/tools/source/mercurial-repositories', array ( 'controller' => 'mercurial_source_admin', 'action' => 'index', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/mercurial-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_mercurial_settings', '/admin/tools/source/mercurial-settings', array ( 'controller' => 'mercurial_source_admin', 'action' => 'settings', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/(\\d+)\\/usage$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repository_usage', '/admin/tools/source/git-repositories/:source_repository_id/usage', array ( 'controller' => 'git_source_admin', 'action' => 'usage', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repository_delete', '/admin/tools/source/git-repositories/:source_repository_id/delete', array ( 'controller' => 'git_source_admin', 'action' => 'delete', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repository_edit', '/admin/tools/source/git-repositories/:source_repository_id/edit', array ( 'controller' => 'git_source_admin', 'action' => 'edit', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repository', '/admin/tools/source/git-repositories/:source_repository_id', array ( 'controller' => 'git_source_admin', 'action' => 'view', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/test_connection$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repository_test_connection', '/admin/tools/source/git-repositories/test_connection', array ( 'controller' => 'git_source_admin', 'action' => 'test_repository_connection', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repositories_add', '/admin/tools/source/git-repositories/add', array ( 'controller' => 'git_source_admin', 'action' => 'add', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/git-repositories$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_git_repositories', '/admin/tools/source/git-repositories', array ( 'controller' => 'git_source_admin', 'action' => 'index', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/(\\d+)\\/usage$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repository_usage', '/admin/tools/source/svn-repositories/:source_repository_id/usage', array ( 'controller' => 'svn_source_admin', 'action' => 'usage', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repository_delete', '/admin/tools/source/svn-repositories/:source_repository_id/delete', array ( 'controller' => 'svn_source_admin', 'action' => 'delete', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repository_edit', '/admin/tools/source/svn-repositories/:source_repository_id/edit', array ( 'controller' => 'svn_source_admin', 'action' => 'edit', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repository', '/admin/tools/source/svn-repositories/:source_repository_id', array ( 'controller' => 'svn_source_admin', 'action' => 'view', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/test$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_test', '/admin/tools/source/svn-repositories/test', array ( 'controller' => 'svn_source_admin', 'action' => 'test_svn', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/test_connection$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repository_test_connection', '/admin/tools/source/svn-repositories/test_connection', array ( 'controller' => 'svn_source_admin', 'action' => 'test_repository_connection', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repositories_add', '/admin/tools/source/svn-repositories/add', array ( 'controller' => 'svn_source_admin', 'action' => 'add', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-repositories$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_repositories', '/admin/tools/source/svn-repositories', array ( 'controller' => 'svn_source_admin', 'action' => 'index', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/svn-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_svn_settings', '/admin/tools/source/svn-settings', array ( 'controller' => 'svn_source_admin', 'action' => 'settings', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^source\\/repositories\\/(\\d+)\\/users\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_user_delete', '/source/repositories/:source_repository_id/users/delete', array ( 'controller' => 'repository_users', 'action' => 'delete', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^source\\/repositories\\/(\\d+)\\/users\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_user_add', '/source/repositories/:source_repository_id/users/add', array ( 'controller' => 'repository_users', 'action' => 'add', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^source\\/repositories\\/(\\d+)\\/users$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_users', '/source/repositories/:source_repository_id/users', array ( 'controller' => 'repository_users', 'action' => 'index', 'module' => 'source', ), array (
  0 => 'source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source\\/source_settings$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source_settings', '/admin/tools/source/source_settings', array ( 'controller' => 'source_admin', 'action' => 'settings', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tools\\/source$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_source', '/admin/tools/source', array ( 'controller' => 'source_admin', 'action' => 'index', 'module' => 'source', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/project-object-commits\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_project_object_commits', '/projects/:project_slug/project-object-commits/:object_id', array ( 'controller' => 'repository', 'action' => 'project_object_commits', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/file_download$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_file_download', '/projects/:project_slug/repositories/:project_source_repository_id/file_download', array ( 'controller' => 'repository', 'action' => 'file_download', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/info$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_item_info', '/projects/:project_slug/repositories/:project_source_repository_id/info', array ( 'controller' => 'repository', 'action' => 'info', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/revision\\/(\\d+)\\/paths$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_commit_paths', '/projects/:project_slug/repositories/:project_source_repository_id/revision/:r/paths', array ( 'controller' => 'repository', 'action' => 'commit_paths', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'r',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/revision\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_commit', '/projects/:project_slug/repositories/:project_source_repository_id/revision/:r', array ( 'controller' => 'repository', 'action' => 'commit', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'r',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_copy_to_project', '/projects/:project_slug/repositories/:project_source_repository_id/copy-to-project', array ( 'controller' => 'repository', 'action' => 'project_source_repository_copy_to_project', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_move_to_project', '/projects/:project_slug/repositories/:project_source_repository_id/move-to-project', array ( 'controller' => 'repository', 'action' => 'project_source_repository_move_to_project', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminder_delete', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/:reminder_id/delete', array ( 'controller' => 'repository', 'action' => 'project_source_repository_delete_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminder_dismiss', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'repository', 'action' => 'project_source_repository_dismiss_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminder_send', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/:reminder_id/send', array ( 'controller' => 'repository', 'action' => 'project_source_repository_send_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminder_edit', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/:reminder_id/edit', array ( 'controller' => 'repository', 'action' => 'project_source_repository_edit_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminder', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/:reminder_id', array ( 'controller' => 'repository', 'action' => 'project_source_repository_view_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminders_nudge', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/nudge', array ( 'controller' => 'repository', 'action' => 'project_source_repository_nudge_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminders_add', '/projects/:project_slug/repositories/:project_source_repository_id/reminders/add', array ( 'controller' => 'repository', 'action' => 'project_source_repository_add_reminder', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_reminders', '/projects/:project_slug/repositories/:project_source_repository_id/reminders', array ( 'controller' => 'repository', 'action' => 'project_source_repository_reminders', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_unsubscribe_all', '/projects/:project_slug/repositories/:project_source_repository_id/unsubscribe_all', array ( 'controller' => 'repository', 'action' => 'project_source_repository_unsubscribe_all', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_unsubscribe', '/projects/:project_slug/repositories/:project_source_repository_id/unsubscribe', array ( 'controller' => 'repository', 'action' => 'project_source_repository_unsubscribe', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_subscribe', '/projects/:project_slug/repositories/:project_source_repository_id/subscribe', array ( 'controller' => 'repository', 'action' => 'project_source_repository_subscribe', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_subscriptions', '/projects/:project_slug/repositories/:project_source_repository_id/subscriptions', array ( 'controller' => 'repository', 'action' => 'project_source_repository_manage_subscriptions', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_delete', '/projects/:project_slug/repositories/:project_source_repository_id/delete', array ( 'controller' => 'repository', 'action' => 'project_source_repository_state_delete', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_untrash', '/projects/:project_slug/repositories/:project_source_repository_id/untrash', array ( 'controller' => 'repository', 'action' => 'project_source_repository_state_untrash', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_trash', '/projects/:project_slug/repositories/:project_source_repository_id/trash', array ( 'controller' => 'repository', 'action' => 'project_source_repository_state_trash', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_unarchive', '/projects/:project_slug/repositories/:project_source_repository_id/unarchive', array ( 'controller' => 'repository', 'action' => 'project_source_repository_state_unarchive', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_source_repository_archive', '/projects/:project_slug/repositories/:project_source_repository_id/archive', array ( 'controller' => 'repository', 'action' => 'project_source_repository_state_archive', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_project_source_repository', '/projects/:project_slug/repositories/:project_source_repository_id', array ( 'controller' => 'repository', 'action' => 'history', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/do_change_branch$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_do_change_branch', '/projects/:project_slug/repositories/:project_source_repository_id/do_change_branch', array ( 'controller' => 'repository', 'action' => 'do_change_branch', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/change_branch$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_change_branch', '/projects/:project_slug/repositories/:project_source_repository_id/change_branch', array ( 'controller' => 'repository', 'action' => 'change_branch', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/compare_form_dialog$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_dialog_form_compare', '/projects/:project_slug/repositories/:project_source_repository_id/compare_form_dialog', array ( 'controller' => 'repository', 'action' => 'compare_dialog_form', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/compare$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_compare', '/projects/:project_slug/repositories/:project_source_repository_id/compare', array ( 'controller' => 'repository', 'action' => 'compare', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/browse_change_revision$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_browse_change_revision', '/projects/:project_slug/repositories/:project_source_repository_id/browse_change_revision', array ( 'controller' => 'repository', 'action' => 'find_revision_number', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/browse_toggle$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_browse_toggle', '/projects/:project_slug/repositories/:project_source_repository_id/browse_toggle', array ( 'controller' => 'repository', 'action' => 'browse_toggle', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/browse$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_browse', '/projects/:project_slug/repositories/:project_source_repository_id/browse', array ( 'controller' => 'repository', 'action' => 'browse', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/update$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_update', '/projects/:project_slug/repositories/:project_source_repository_id/update', array ( 'controller' => 'repository', 'action' => 'update', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/revision\\/(\\d+)\\/one_commit_info$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_one_commit_info', '/projects/:project_slug/repositories/:project_source_repository_id/revision/:r/one_commit_info', array ( 'controller' => 'repository', 'action' => 'one_commit_info', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
  2 => 'r',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/history_show_thirty_more$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_history_show_thirty_more', '/projects/:project_slug/repositories/:project_source_repository_id/history_show_thirty_more', array ( 'controller' => 'repository', 'action' => 'history_show_thirty_more', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_history', '/projects/:project_slug/repositories/:project_source_repository_id', array ( 'controller' => 'repository', 'action' => 'history', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/test_connection$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_test_connection', '/projects/:project_slug/repositories/test_connection', array ( 'controller' => 'repository', 'action' => 'test_repository_connection', 'module' => 'source', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/remove_from_project$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_remove_from_project', '/projects/:project_slug/repositories/:project_source_repository_id/remove_from_project', array ( 'controller' => 'repository', 'action' => 'remove_from_project', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_edit', '/projects/:project_slug/repositories/:project_source_repository_id/edit', array ( 'controller' => 'repository', 'action' => 'edit', 'module' => 'source', ), array (
  0 => 'project_slug',
  1 => 'project_source_repository_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/add-new$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_add_new', '/projects/:project_slug/repositories/add-new', array ( 'controller' => 'repository', 'action' => 'add_new', 'module' => 'source', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories\\/add-existing$/', $path, $matches)) {
      return Router::doMatch($path, 'repository_add_existing', '/projects/:project_slug/repositories/add-existing', array ( 'controller' => 'repository', 'action' => 'add_existing', 'module' => 'source', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/repositories$/', $path, $matches)) {
      return Router::doMatch($path, 'project_repositories', '/projects/:project_slug/repositories', array ( 'controller' => 'repository', 'action' => 'index', 'module' => 'source', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/documents$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_documents', 'admin/indices/object-contexts/rebuild/documents', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_documents', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/documents$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_documents', 'admin/indices/activity-logs/rebuild/documents', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_documents', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/search\\/names\\/build\\/documents$/', $path, $matches)) {
      return Router::doMatch($path, 'document_names_search_index_admin_build', 'admin/indices/search/names/build/documents', array ( 'controller' => 'document_names_search_index_admin', 'action' => 'build', 'search_index_name' => 'names', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/search\\/documents\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'documents_search_index_admin_build', 'admin/indices/search/documents/build', array ( 'controller' => 'documents_search_index_admin', 'action' => 'build', 'search_index_name' => 'documents', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)\\/unpin$/', $path, $matches)) {
      return Router::doMatch($path, 'document_unpin', 'documents/:document_id/unpin', array ( 'controller' => 'documents', 'action' => 'unpin', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)\\/pin$/', $path, $matches)) {
      return Router::doMatch($path, 'document_pin', 'documents/:document_id/pin', array ( 'controller' => 'documents', 'action' => 'pin', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'document_edit', 'documents/:document_id/edit', array ( 'controller' => 'documents', 'action' => 'edit', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'document_download', 'documents/:document_id/download', array ( 'controller' => 'documents', 'action' => 'download', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'document', 'documents/:document_id', array ( 'controller' => 'documents', 'action' => 'view', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'document_unsubscribe_all', 'document/:document_id/unsubscribe_all', array ( 'controller' => 'documents', 'action' => 'document_unsubscribe_all', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'document_unsubscribe', 'document/:document_id/unsubscribe', array ( 'controller' => 'documents', 'action' => 'document_unsubscribe', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'document_subscribe', 'document/:document_id/subscribe', array ( 'controller' => 'documents', 'action' => 'document_subscribe', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'document_subscriptions', 'document/:document_id/subscriptions', array ( 'controller' => 'documents', 'action' => 'document_manage_subscriptions', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_untrash', 'document/:document_id/attachments/:attachment_id/untrash', array ( 'controller' => 'documents', 'action' => 'document_attachment_state_untrash', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_trash', 'document/:document_id/attachments/:attachment_id/trash', array ( 'controller' => 'documents', 'action' => 'document_attachment_state_trash', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_unarchive', 'document/:document_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'documents', 'action' => 'document_attachment_state_unarchive', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_archive', 'document/:document_id/attachments/:attachment_id/archive', array ( 'controller' => 'documents', 'action' => 'document_attachment_state_archive', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_preview', 'document/:document_id/attachments/:attachment_id/preview', array ( 'controller' => 'documents', 'action' => 'document_attachment_preview_content', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_download', 'document/:document_id/attachments/:attachment_id/download', array ( 'controller' => 'documents', 'action' => 'document_attachment_download_content', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_delete', 'document/:document_id/attachments/:attachment_id/delete', array ( 'controller' => 'documents', 'action' => 'document_attachment_state_delete', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment_edit', 'document/:document_id/attachments/:attachment_id/edit', array ( 'controller' => 'documents', 'action' => 'document_edit_attachment', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachment', 'document/:document_id/attachments/:attachment_id', array ( 'controller' => 'documents', 'action' => 'document_view_attachment', 'module' => 'documents', ), array (
  0 => 'document_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachments_add', 'document/:document_id/attachments/add', array ( 'controller' => 'documents', 'action' => 'document_add_attachment', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'document_attachments', 'document/:document_id/attachments', array ( 'controller' => 'documents', 'action' => 'document_attachments', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'document_delete', 'documents/:document_id/delete', array ( 'controller' => 'documents', 'action' => 'delete', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'document_untrash', 'document/:document_id/untrash', array ( 'controller' => 'documents', 'action' => 'document_state_untrash', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'document_trash', 'document/:document_id/trash', array ( 'controller' => 'documents', 'action' => 'document_state_trash', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'document_unarchive', 'document/:document_id/unarchive', array ( 'controller' => 'documents', 'action' => 'document_state_unarchive', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^document\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'document_archive', 'document/:document_id/archive', array ( 'controller' => 'documents', 'action' => 'document_state_archive', 'module' => 'documents', ), array (
  0 => 'document_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/update-category$/', $path, $matches)) {
      return Router::doMatch($path, 'document_update_category', 'documents/update-category', array ( 'controller' => 'documents', 'action' => 'document_update_category', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'document_category_delete', 'documents/categories/:category_id/delete', array ( 'controller' => 'documents', 'action' => 'document_delete_category', 'module' => 'documents', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'document_category_edit', 'documents/categories/:category_id/edit', array ( 'controller' => 'documents', 'action' => 'document_edit_category', 'module' => 'documents', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'document_category', 'documents/categories/:category_id', array ( 'controller' => 'documents', 'action' => 'document_view_category', 'module' => 'documents', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^documents\\/categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'document_categories_add', 'documents/categories/add', array ( 'controller' => 'documents', 'action' => 'document_add_category', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'document_categories', 'documents/categories', array ( 'controller' => 'documents', 'action' => 'document_categories', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'documents_archive', 'documents/archive', array ( 'controller' => 'documents', 'action' => 'archive', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/upload-file$/', $path, $matches)) {
      return Router::doMatch($path, 'documents_upload_file', 'documents/upload-file', array ( 'controller' => 'documents', 'action' => 'upload_file', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/add-text$/', $path, $matches)) {
      return Router::doMatch($path, 'documents_add_text', 'documents/add-text', array ( 'controller' => 'documents', 'action' => 'add_text', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'documents_mass_edit', 'documents/mass-edit', array ( 'controller' => 'documents', 'action' => 'mass_edit', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^documents$/', $path, $matches)) {
      return Router::doMatch($path, 'documents', 'documents', array ( 'controller' => 'documents', 'action' => 'index', 'module' => 'documents', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/notebooks$/', $path, $matches)) {
      return Router::doMatch($path, 'milestone_notebooks', 'projects/:project_slug/milestones/:milestone_id/notebooks', array ( 'controller' => 'milestone_notebooks', 'action' => 'index', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^s\\/notebook\\/(\\w+)\\/page\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'shared_notebook_page', 's/notebook/:sharing_code/page/:notebook_page_id', array ( 'controller' => 'notebooks_frontend', 'action' => 'notebook_page', 'module' => 'notebooks', ), array (
  0 => 'sharing_code',
  1 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^s\\/notebook\\/(\\w+)$/', $path, $matches)) {
      return Router::doMatch($path, 'shared_notebook', 's/notebook/:sharing_code', array ( 'controller' => 'notebooks_frontend', 'action' => 'default_view_shared_object', 'module' => 'notebooks', ), array (
  0 => 'sharing_code',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/notebooks$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_notebooks', 'admin/indices/object-contexts/rebuild/notebooks', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_notebooks', 'module' => 'notebooks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/notebook-page-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_notbook_pages', 'admin/indices/activity-logs/rebuild/notebook-page-versions', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_notbook_pages', 'module' => 'notebooks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/notebooks$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_notebooks', 'admin/indices/activity-logs/rebuild/notebooks', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_notebooks', 'module' => 'notebooks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/versions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_version_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/versions/:version/delete', array ( 'controller' => 'notebook_page_versions', 'action' => 'delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'version',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/versions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_version', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/versions/:version', array ( 'controller' => 'notebook_page_versions', 'action' => 'view', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'version',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_untrash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/untrash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_trash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/trash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_unarchive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/archive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_preview', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/preview', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_preview_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_download', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/download', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_download_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/delete', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachment_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment_edit', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id/edit', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_edit_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachment', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/:attachment_id', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_view_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachments_add', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments/add', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_add_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_attachments', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/attachments', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_attachments', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_unsubscribe_all', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/unsubscribe_all', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_unsubscribe_all', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_unsubscribe', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/unsubscribe', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_unsubscribe', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_subscribe', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/subscribe', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_subscribe', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_subscriptions', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/subscriptions', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_manage_subscriptions', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_untrash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/untrash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_trash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/trash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_unarchive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/unarchive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/archive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_untrash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_trash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_unarchive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_preview', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_preview_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_download', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_download_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachment_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment_edit', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_edit_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachment', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_view_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
  4 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachments_add', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments/add', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_add_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_attachments', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/attachments', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_attachments', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/delete', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comment_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/([a-z0-9\\-\\._]+)\\/pages\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment_edit', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id/edit', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_edit_comment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/([a-z0-9\\-\\._]+)\\/pages\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comment', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/:comment_id', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_view_comment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comments_unlock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/unlock', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comments_unlock', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comments_lock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/lock', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comments_lock', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comments_add', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments/add', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_add_comment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_comments', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/comments', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_comments', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_delete', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/delete', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_untrash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/untrash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_trash', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/trash', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_unarchive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/unarchive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/archive', array ( 'controller' => 'notebook_pages', 'action' => 'project_notebook_page_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/move$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_move', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/move', array ( 'controller' => 'notebook_pages', 'action' => 'move', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_unlock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/unlock', array ( 'controller' => 'notebook_pages', 'action' => 'unlock', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_lock', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/lock', array ( 'controller' => 'notebook_pages', 'action' => 'lock', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/compare-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_compare_versions', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/compare-versions', array ( 'controller' => 'notebook_pages', 'action' => 'compare_versions', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/revert\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_revert', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/revert/:to', array ( 'controller' => 'notebook_pages', 'action' => 'revert', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
  3 => 'to',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page_edit', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id/edit', array ( 'controller' => 'notebook_pages', 'action' => 'edit', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_page', 'projects/:project_slug/notebooks/:notebook_id/pages/:notebook_page_id', array ( 'controller' => 'notebook_pages', 'action' => 'view', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'notebook_page_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_pages_reorder', 'projects/:project_slug/notebooks/:notebook_id/pages/reorder', array ( 'controller' => 'notebook_pages', 'action' => 'reorder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_pages_add', 'projects/:project_slug/notebooks/:notebook_id/pages/add', array ( 'controller' => 'notebook_pages', 'action' => 'add', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/pages\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_pages_archive', 'projects/:project_slug/notebooks/:notebook_id/pages/archive', array ( 'controller' => 'notebook_pages', 'action' => 'archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/sharing$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_sharing_settings', 'projects/:project_slug/notebooks/:notebook_id/sharing', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_sharing_settings', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_copy_to_project', 'projects/:project_slug/notebooks/:notebook_id/copy-to-project', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_copy_to_project', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_move_to_project', 'projects/:project_slug/notebooks/:notebook_id/move-to-project', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_move_to_project', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminder_delete', 'projects/:project_slug/notebooks/:notebook_id/reminders/:reminder_id/delete', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_delete_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminder_dismiss', 'projects/:project_slug/notebooks/:notebook_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_dismiss_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminder_send', 'projects/:project_slug/notebooks/:notebook_id/reminders/:reminder_id/send', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_send_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminder_edit', 'projects/:project_slug/notebooks/:notebook_id/reminders/:reminder_id/edit', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_edit_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminder', 'projects/:project_slug/notebooks/:notebook_id/reminders/:reminder_id', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_view_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminders_nudge', 'projects/:project_slug/notebooks/:notebook_id/reminders/nudge', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_nudge_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminders_add', 'projects/:project_slug/notebooks/:notebook_id/reminders/add', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_add_reminder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_reminders', 'projects/:project_slug/notebooks/:notebook_id/reminders', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_reminders', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_untrash', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/untrash', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_trash', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/trash', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_unarchive', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_archive', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/archive', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_preview', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/preview', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_preview_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_download', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/download', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_download_content', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_delete', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/delete', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachment_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment_edit', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id/edit', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_edit_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachment', 'projects/:project_slug/notebooks/:notebook_id/attachments/:attachment_id', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_view_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachments_add', 'projects/:project_slug/notebooks/:notebook_id/attachments/add', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_add_attachment', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_attachments', 'projects/:project_slug/notebooks/:notebook_id/attachments', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_attachments', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_unsubscribe_all', 'projects/:project_slug/notebooks/:notebook_id/unsubscribe_all', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_unsubscribe_all', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_unsubscribe', 'projects/:project_slug/notebooks/:notebook_id/unsubscribe', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_unsubscribe', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_subscribe', 'projects/:project_slug/notebooks/:notebook_id/subscribe', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_subscribe', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_subscriptions', 'projects/:project_slug/notebooks/:notebook_id/subscriptions', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_manage_subscriptions', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_avatar_remove', 'projects/:project_slug/notebooks/:notebook_id/avatar/remove', array ( 'controller' => 'notebooks', 'action' => 'project_notebook/avatar_remove', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_avatar_edit', 'projects/:project_slug/notebooks/:notebook_id/avatar/edit', array ( 'controller' => 'notebooks', 'action' => 'project_notebook/avatar_edit', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_avatar_upload', 'projects/:project_slug/notebooks/:notebook_id/avatar/upload', array ( 'controller' => 'notebooks', 'action' => 'project_notebook/avatar_upload', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_avatar_view', 'projects/:project_slug/notebooks/:notebook_id/avatar/view', array ( 'controller' => 'notebooks', 'action' => 'project_notebook/avatar_view', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_delete', 'projects/:project_slug/notebooks/:notebook_id/delete', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_state_delete', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_untrash', 'projects/:project_slug/notebooks/:notebook_id/untrash', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_state_untrash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_trash', 'projects/:project_slug/notebooks/:notebook_id/trash', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_state_trash', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_unarchive', 'projects/:project_slug/notebooks/:notebook_id/unarchive', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_state_unarchive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_archive', 'projects/:project_slug/notebooks/:notebook_id/archive', array ( 'controller' => 'notebooks', 'action' => 'project_notebook_state_archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_edit', 'projects/:project_slug/notebooks/:notebook_id/edit', array ( 'controller' => 'notebooks', 'action' => 'edit', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook_mass_edit', 'projects/:project_slug/notebooks/:notebook_id/mass-edit', array ( 'controller' => 'notebook_pages', 'action' => 'mass_edit', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebook', 'projects/:project_slug/notebooks/:notebook_id', array ( 'controller' => 'notebooks', 'action' => 'view', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
  1 => 'notebook_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/add\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebooks_reorder', 'projects/:project_slug/notebooks/add/reorder', array ( 'controller' => 'notebooks', 'action' => 'reorder', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebooks_add', 'projects/:project_slug/notebooks/add', array ( 'controller' => 'notebooks', 'action' => 'add', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebooks_archive', 'projects/:project_slug/notebooks/archive', array ( 'controller' => 'notebooks', 'action' => 'archive', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/notebooks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_notebooks', 'projects/:project_slug/notebooks', array ( 'controller' => 'notebooks', 'action' => 'index', 'module' => 'notebooks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/project_exporter\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_exporter_download_export', 'projects/:project_slug/project_exporter/download', array ( 'controller' => 'project_exporter', 'action' => 'download', 'module' => 'project_exporter', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/project_exporter\\/finalize$/', $path, $matches)) {
      return Router::doMatch($path, 'project_exporter_finalize_export', 'projects/:project_slug/project_exporter/finalize', array ( 'controller' => 'project_exporter', 'action' => 'finalize', 'module' => 'project_exporter', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/project_exporter\\/export\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_exporter_section_exporter', 'projects/:project_slug/project_exporter/export/:exporter_id', array ( 'controller' => 'project_exporter', 'action' => 'export', 'module' => 'project_exporter', ), array (
  0 => 'project_slug',
  1 => 'exporter_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/project_exporter$/', $path, $matches)) {
      return Router::doMatch($path, 'project_exporter', 'projects/:project_slug/project_exporter', array ( 'controller' => 'project_exporter', 'action' => 'index', 'module' => 'project_exporter', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/files$/', $path, $matches)) {
      return Router::doMatch($path, 'milestone_files', 'projects/:project_slug/milestones/:milestone_id/files', array ( 'controller' => 'milestone_files', 'action' => 'index', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/files$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_files', 'admin/indices/object-contexts/rebuild/files', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_files', 'module' => 'files', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/text-document-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_text_document_versions', 'admin/indices/activity-logs/rebuild/text-document-versions', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_text_document_versions', 'module' => 'files', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/file-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_file_versions', 'admin/indices/activity-logs/rebuild/file-versions', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_file_versions', 'module' => 'files', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/files$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_files', 'admin/indices/activity-logs/rebuild/files', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_files', 'module' => 'files', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/versions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_version_delete', 'projects/:project_slug/files/text-documents/:asset_id/versions/:version_num/delete', array ( 'controller' => 'text_document_versions', 'action' => 'delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'version_num',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/versions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_version', 'projects/:project_slug/files/text-documents/:asset_id/versions/:version_num', array ( 'controller' => 'text_document_versions', 'action' => 'view', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'version_num',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/compare-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_compare_versions', 'projects/:project_slug/files/text-documents/:asset_id/compare-versions', array ( 'controller' => 'text_documents', 'action' => 'compare_versions', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/revert$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_version_revert', 'projects/:project_slug/files/text-documents/:asset_id/revert', array ( 'controller' => 'text_documents', 'action' => 'revert', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/sharing$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_sharing_settings', 'projects/:project_slug/files/text-documents/:asset_id/sharing', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_sharing_settings', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_copy_to_project', 'projects/:project_slug/files/text-documents/:asset_id/copy-to-project', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_copy_to_project', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_move_to_project', 'projects/:project_slug/files/text-documents/:asset_id/move-to-project', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_move_to_project', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_untrash', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/untrash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_trash', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/trash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_unarchive', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_archive', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/archive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_preview', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/preview', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_preview_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_download', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/download', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_download_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_delete', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/delete', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment_edit', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id/edit', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_edit_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachment', 'projects/:project_slug/files/text-documents/:asset_id/attachments/:attachment_id', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_view_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachments_add', 'projects/:project_slug/files/text-documents/:asset_id/attachments/add', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_add_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_attachments', 'projects/:project_slug/files/text-documents/:asset_id/attachments', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_attachments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminder_delete', 'projects/:project_slug/files/text-documents/:asset_id/reminders/:reminder_id/delete', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_delete_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminder_dismiss', 'projects/:project_slug/files/text-documents/:asset_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_dismiss_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminder_send', 'projects/:project_slug/files/text-documents/:asset_id/reminders/:reminder_id/send', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_send_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminder_edit', 'projects/:project_slug/files/text-documents/:asset_id/reminders/:reminder_id/edit', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_edit_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminder', 'projects/:project_slug/files/text-documents/:asset_id/reminders/:reminder_id', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_view_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminders_nudge', 'projects/:project_slug/files/text-documents/:asset_id/reminders/nudge', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_nudge_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminders_add', 'projects/:project_slug/files/text-documents/:asset_id/reminders/add', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_add_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_reminders', 'projects/:project_slug/files/text-documents/:asset_id/reminders', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_reminders', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_unsubscribe_all', 'projects/:project_slug/files/text-documents/:asset_id/unsubscribe_all', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_unsubscribe_all', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_unsubscribe', 'projects/:project_slug/files/text-documents/:asset_id/unsubscribe', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_unsubscribe', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_subscribe', 'projects/:project_slug/files/text-documents/:asset_id/subscribe', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_subscribe', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_subscriptions', 'projects/:project_slug/files/text-documents/:asset_id/subscriptions', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_manage_subscriptions', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_untrash', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/untrash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_trash', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/trash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_unarchive', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/unarchive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_archive', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/archive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_untrash', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_trash', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_unarchive', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_archive', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_preview', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_preview_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_download', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_download_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_delete', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment_edit', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_edit_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachment', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_view_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachments_add', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments/add', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_add_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_attachments', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/attachments', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_attachments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_delete', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/delete', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment_edit', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id/edit', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_edit_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comment', 'projects/:project_slug/files/text-documents/:asset_id/comments/:comment_id', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_view_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comments_unlock', 'projects/:project_slug/files/text-documents/:asset_id/comments/unlock', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comments_unlock', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comments_lock', 'projects/:project_slug/files/text-documents/:asset_id/comments/lock', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comments_lock', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comments_add', 'projects/:project_slug/files/text-documents/:asset_id/comments/add', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_add_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_comments', 'projects/:project_slug/files/text-documents/:asset_id/comments', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_comments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_delete', 'projects/:project_slug/files/text-documents/:asset_id/delete', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_untrash', 'projects/:project_slug/files/text-documents/:asset_id/untrash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_trash', 'projects/:project_slug/files/text-documents/:asset_id/trash', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_unarchive', 'projects/:project_slug/files/text-documents/:asset_id/unarchive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_archive', 'projects/:project_slug/files/text-documents/:asset_id/archive', array ( 'controller' => 'text_documents', 'action' => 'project_assets_text_document_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_edit', 'projects/:project_slug/files/text-documents/:asset_id/edit', array ( 'controller' => 'text_documents', 'action' => 'edit', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document_add', 'projects/:project_slug/files/text-documents/add', array ( 'controller' => 'text_documents', 'action' => 'add', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_document', 'projects/:project_slug/files/text-documents/:asset_id', array ( 'controller' => 'text_documents', 'action' => 'view', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_documents_archive', 'projects/:project_slug/files/text-documents/archive', array ( 'controller' => 'text_documents', 'action' => 'archive', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/text-documents$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_text_documents', 'projects/:project_slug/files/text-documents', array ( 'controller' => 'text_documents', 'action' => 'index', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/versions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_version_delete', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num/delete', array ( 'controller' => 'file_versions', 'action' => 'delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'file_version_num',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/versions\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_version_download', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num/download', array ( 'controller' => 'file_versions', 'action' => 'download', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'file_version_num',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/versions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_version', 'projects/:project_slug/files/files/:asset_id/versions/:file_version_num', array ( 'controller' => 'file_versions', 'action' => 'view', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'file_version_num',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/versions\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_versions_add', 'projects/:project_slug/files/files/:asset_id/versions/add', array ( 'controller' => 'file_versions', 'action' => 'add', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/refresh-details$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_refresh_details', 'projects/:project_slug/files/files/:asset_id/refresh-details', array ( 'controller' => 'files', 'action' => 'refresh_details', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_download', 'projects/:project_slug/files/files/:asset_id/download', array ( 'controller' => 'files', 'action' => 'download', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^s\\/file\\/(\\w+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'shared_file_download', 's/file/:sharing_code/download', array ( 'controller' => 'files_frontend', 'action' => 'download', 'module' => 'files', ), array (
  0 => 'sharing_code',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/sharing$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_sharing_settings', 'projects/:project_slug/files/:asset_id/sharing', array ( 'controller' => 'files', 'action' => 'project_assets_file_sharing_settings', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_copy_to_project', 'projects/:project_slug/files/:asset_id/copy-to-project', array ( 'controller' => 'files', 'action' => 'project_assets_file_copy_to_project', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_move_to_project', 'projects/:project_slug/files/:asset_id/move-to-project', array ( 'controller' => 'files', 'action' => 'project_assets_file_move_to_project', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_untrash', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/untrash', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_trash', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/trash', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_unarchive', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_archive', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/archive', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_preview', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/preview', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_preview_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_download', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/download', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_download_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_delete', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/delete', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment_edit', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id/edit', array ( 'controller' => 'files', 'action' => 'project_assets_file_edit_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachment', 'projects/:project_slug/files/:asset_id/attachments/:attachment_id', array ( 'controller' => 'files', 'action' => 'project_assets_file_view_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachments_add', 'projects/:project_slug/files/:asset_id/attachments/add', array ( 'controller' => 'files', 'action' => 'project_assets_file_add_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_attachments', 'projects/:project_slug/files/:asset_id/attachments', array ( 'controller' => 'files', 'action' => 'project_assets_file_attachments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminder_delete', 'projects/:project_slug/files/:asset_id/reminders/:reminder_id/delete', array ( 'controller' => 'files', 'action' => 'project_assets_file_delete_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminder_dismiss', 'projects/:project_slug/files/:asset_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'files', 'action' => 'project_assets_file_dismiss_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminder_send', 'projects/:project_slug/files/:asset_id/reminders/:reminder_id/send', array ( 'controller' => 'files', 'action' => 'project_assets_file_send_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminder_edit', 'projects/:project_slug/files/:asset_id/reminders/:reminder_id/edit', array ( 'controller' => 'files', 'action' => 'project_assets_file_edit_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminder', 'projects/:project_slug/files/:asset_id/reminders/:reminder_id', array ( 'controller' => 'files', 'action' => 'project_assets_file_view_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminders_nudge', 'projects/:project_slug/files/:asset_id/reminders/nudge', array ( 'controller' => 'files', 'action' => 'project_assets_file_nudge_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminders_add', 'projects/:project_slug/files/:asset_id/reminders/add', array ( 'controller' => 'files', 'action' => 'project_assets_file_add_reminder', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_reminders', 'projects/:project_slug/files/:asset_id/reminders', array ( 'controller' => 'files', 'action' => 'project_assets_file_reminders', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_unsubscribe_all', 'projects/:project_slug/files/:asset_id/unsubscribe_all', array ( 'controller' => 'files', 'action' => 'project_assets_file_unsubscribe_all', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_unsubscribe', 'projects/:project_slug/files/:asset_id/unsubscribe', array ( 'controller' => 'files', 'action' => 'project_assets_file_unsubscribe', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_subscribe', 'projects/:project_slug/files/:asset_id/subscribe', array ( 'controller' => 'files', 'action' => 'project_assets_file_subscribe', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_subscriptions', 'projects/:project_slug/files/:asset_id/subscriptions', array ( 'controller' => 'files', 'action' => 'project_assets_file_manage_subscriptions', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_untrash', 'projects/:project_slug/files/:asset_id/comments/:comment_id/untrash', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_trash', 'projects/:project_slug/files/:asset_id/comments/:comment_id/trash', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_unarchive', 'projects/:project_slug/files/:asset_id/comments/:comment_id/unarchive', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_archive', 'projects/:project_slug/files/:asset_id/comments/:comment_id/archive', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_untrash', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_trash', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_unarchive', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_archive', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_preview', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_preview_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_download', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_download_content', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_delete', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment_edit', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_edit_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachment', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_view_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachments_add', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments/add', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_add_attachment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_attachments', 'projects/:project_slug/files/:asset_id/comments/:comment_id/attachments', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_attachments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_delete', 'projects/:project_slug/files/:asset_id/comments/:comment_id/delete', array ( 'controller' => 'files', 'action' => 'project_assets_file_comment_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment_edit', 'projects/:project_slug/files/:asset_id/comments/:comment_id/edit', array ( 'controller' => 'files', 'action' => 'project_assets_file_edit_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comment', 'projects/:project_slug/files/:asset_id/comments/:comment_id', array ( 'controller' => 'files', 'action' => 'project_assets_file_view_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comments_unlock', 'projects/:project_slug/files/:asset_id/comments/unlock', array ( 'controller' => 'files', 'action' => 'project_assets_file_comments_unlock', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comments_lock', 'projects/:project_slug/files/:asset_id/comments/lock', array ( 'controller' => 'files', 'action' => 'project_assets_file_comments_lock', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comments_add', 'projects/:project_slug/files/:asset_id/comments/add', array ( 'controller' => 'files', 'action' => 'project_assets_file_add_comment', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_comments', 'projects/:project_slug/files/:asset_id/comments', array ( 'controller' => 'files', 'action' => 'project_assets_file_comments', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_delete', 'projects/:project_slug/files/:asset_id/delete', array ( 'controller' => 'files', 'action' => 'project_assets_file_state_delete', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_untrash', 'projects/:project_slug/files/:asset_id/untrash', array ( 'controller' => 'files', 'action' => 'project_assets_file_state_untrash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_trash', 'projects/:project_slug/files/:asset_id/trash', array ( 'controller' => 'files', 'action' => 'project_assets_file_state_trash', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_unarchive', 'projects/:project_slug/files/:asset_id/unarchive', array ( 'controller' => 'files', 'action' => 'project_assets_file_state_unarchive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_archive', 'projects/:project_slug/files/:asset_id/archive', array ( 'controller' => 'files', 'action' => 'project_assets_file_state_archive', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_preview', 'projects/:project_slug/files/files/:asset_id/preview', array ( 'controller' => 'files', 'action' => 'preview', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_edit', 'projects/:project_slug/files/files/:asset_id/edit', array ( 'controller' => 'files', 'action' => 'edit', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/upload-compatibility$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file_upload_compatibility', 'projects/:project_slug/files/files/upload-compatibility', array ( 'controller' => 'files', 'action' => 'upload_compatibility', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_file', 'projects/:project_slug/files/files/:asset_id', array ( 'controller' => 'files', 'action' => 'view', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'asset_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_files_upload_single', 'projects/:project_slug/files/files/upload', array ( 'controller' => 'files', 'action' => 'upload', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_files_add', 'projects/:project_slug/files/files/add', array ( 'controller' => 'files', 'action' => 'add', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_files_archive', 'projects/:project_slug/files/files/archive', array ( 'controller' => 'files', 'action' => 'archive', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/files$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_files', 'projects/:project_slug/files/files', array ( 'controller' => 'files', 'action' => 'index', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/update-category$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_update_category', 'projects/:project_slug/files/update-category', array ( 'controller' => 'assets', 'action' => 'project_asset_update_category', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_category_delete', 'projects/:project_slug/files/categories/:category_id/delete', array ( 'controller' => 'assets', 'action' => 'project_asset_delete_category', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_category_edit', 'projects/:project_slug/files/categories/:category_id/edit', array ( 'controller' => 'assets', 'action' => 'project_asset_edit_category', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_category', 'projects/:project_slug/files/categories/:category_id', array ( 'controller' => 'assets', 'action' => 'project_asset_view_category', 'module' => 'files', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_categories_add', 'projects/:project_slug/files/categories/add', array ( 'controller' => 'assets', 'action' => 'project_asset_add_category', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'project_asset_categories', 'projects/:project_slug/files/categories', array ( 'controller' => 'assets', 'action' => 'project_asset_categories', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_mass_edit', 'projects/:project_slug/files/mass-edit', array ( 'controller' => 'assets', 'action' => 'mass_edit', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets_archive', 'projects/:project_slug/files/archive', array ( 'controller' => 'assets', 'action' => 'archive', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/files$/', $path, $matches)) {
      return Router::doMatch($path, 'project_assets', 'projects/:project_slug/files', array ( 'controller' => 'assets', 'action' => 'index', 'module' => 'files', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_report_delete', 'reports/tasks/weekly-completed/:weekly_completed_tasks_report_id/delete', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'delete', ), array (
  0 => 'weekly_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_report_edit', 'reports/tasks/weekly-completed/:weekly_completed_tasks_report_id/edit', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'edit', ), array (
  0 => 'weekly_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_report', 'reports/tasks/weekly-completed/:weekly_completed_tasks_report_id', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'view', ), array (
  0 => 'weekly_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_reports_export', 'reports/tasks/weekly-completed/export', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_reports_run', 'reports/tasks/weekly-completed/run', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_reports_add', 'reports/tasks/weekly-completed/add', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-completed$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_completed_tasks_reports', 'reports/tasks/weekly-completed', array ( 'controller' => 'weekly_completed_tasks_reports', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_report_delete', 'reports/tasks/weekly-created/:weekly_created_tasks_report_id/delete', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'delete', ), array (
  0 => 'weekly_created_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_report_edit', 'reports/tasks/weekly-created/:weekly_created_tasks_report_id/edit', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'edit', ), array (
  0 => 'weekly_created_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_report', 'reports/tasks/weekly-created/:weekly_created_tasks_report_id', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'view', ), array (
  0 => 'weekly_created_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_reports_export', 'reports/tasks/weekly-created/export', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_reports_run', 'reports/tasks/weekly-created/run', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_reports_add', 'reports/tasks/weekly-created/add', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/weekly-created$/', $path, $matches)) {
      return Router::doMatch($path, 'weekly_created_tasks_reports', 'reports/tasks/weekly-created', array ( 'controller' => 'weekly_created_tasks_reports', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_report_delete', 'reports/tasks/open-vs-completed/:open_vs_completed_tasks_report_id/delete', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'delete', ), array (
  0 => 'open_vs_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_report_edit', 'reports/tasks/open-vs-completed/:open_vs_completed_tasks_report_id/edit', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'edit', ), array (
  0 => 'open_vs_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_report', 'reports/tasks/open-vs-completed/:open_vs_completed_tasks_report_id', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'view', ), array (
  0 => 'open_vs_completed_tasks_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_reports_export', 'reports/tasks/open-vs-completed/export', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_reports_run', 'reports/tasks/open-vs-completed/run', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_reports_add', 'reports/tasks/open-vs-completed/add', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tasks\\/open-vs-completed$/', $path, $matches)) {
      return Router::doMatch($path, 'open_vs_completed_tasks_reports', 'reports/tasks/open-vs-completed', array ( 'controller' => 'open_vs_completed_tasks_reports', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/analyzer\\/task-segments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'task_segment_delete', 'reports/analyzer/task-segments/:task_segment_id/delete', array ( 'controller' => 'task_segments', 'action' => 'delete', 'module' => 'tasks', ), array (
  0 => 'task_segment_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/analyzer\\/task-segments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'task_segment_edit', 'reports/analyzer/task-segments/:task_segment_id/edit', array ( 'controller' => 'task_segments', 'action' => 'edit', 'module' => 'tasks', ), array (
  0 => 'task_segment_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/analyzer\\/task-segments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'task_segment', 'reports/analyzer/task-segments/:task_segment_id', array ( 'controller' => 'task_segments', 'action' => 'view', 'module' => 'tasks', ), array (
  0 => 'task_segment_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/analyzer\\/task-segments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'task_segments_add', 'reports/analyzer/task-segments/add', array ( 'controller' => 'task_segments', 'action' => 'add', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/analyzer\\/task-segments$/', $path, $matches)) {
      return Router::doMatch($path, 'task_segments', 'reports/analyzer/task-segments', array ( 'controller' => 'task_segments', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_tasks', 'admin/indices/object-contexts/rebuild/tasks', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_tasks', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_tasks', 'admin/indices/activity-logs/rebuild/tasks', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_tasks', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^task\\/invoicing\\/(\\d+)\\/invoice\\/preview-items$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_invoicing_preview_items', 'task/invoicing/:task_id/invoice/preview-items', array ( 'controller' => 'tasks', 'action' => 'project_task_preview_items', 'module' => 'tasks', ), array (
  0 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^task\\/invoicing\\/(\\d+)\\/invoice\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_invoicing', 'task/invoicing/:task_id/invoice/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_invoice', 'module' => 'tasks', ), array (
  0 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_delete', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_expense_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_untrash', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_expense_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_trash', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_expense_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_unarchive', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_expense_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_archive', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_expense_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense_edit', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_expense', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expense', 'projects/:project_slug/tasks/:task_id/tracking/expenses/:expense_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_expense', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/expenses\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_expenses_add', 'projects/:project_slug/tasks/:task_id/tracking/expenses/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_expense', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_delete', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_time_record_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_untrash', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_time_record_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_trash', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_time_record_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_unarchive', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_time_record_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_archive', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_tracking_time_record_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record_edit', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_time_record', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_record', 'projects/:project_slug/tasks/:task_id/tracking/time/:time_record_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_time_record', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/time\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_time_records_add', 'projects/:project_slug/tasks/:task_id/tracking/time/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_time_record', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/estimates\\/set$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_estimate_set', 'projects/:project_slug/tasks/:task_id/tracking/estimates/set', array ( 'controller' => 'tasks', 'action' => 'project_task_object_tracking_estimate_set', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking\\/estimates$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking_estimates', 'projects/:project_slug/tasks/:task_id/tracking/estimates', array ( 'controller' => 'tasks', 'action' => 'project_task_object_tracking_estimates', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/tracking$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_tracking', 'projects/:project_slug/tasks/:task_id/tracking', array ( 'controller' => 'tasks', 'action' => 'project_task_object_tracking_list', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/project-aggregated-tasks-run$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_aggregated_report_run', 'reports/project-aggregated-tasks-run', array ( 'controller' => 'tasks_reports', 'action' => 'aggregated_tasks_run', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/project-aggregated-tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_aggregated_report', 'reports/project-aggregated-tasks', array ( 'controller' => 'tasks_reports', 'action' => 'aggregated_tasks', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/project\\/([a-z0-9\\-\\._]+)\\/subscribers$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_subscribers', 'admin/tasks/forms/project/:project_id/subscribers', array ( 'controller' => 'public_task_forms_admin', 'action' => 'subscribers', 'module' => 'tasks', ), array (
  0 => 'project_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/delete\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_unsubscribe_all', 'admin/tasks/forms/:public_task_form_id/delete/unsubscribe_all', array ( 'controller' => 'public_task_forms_admin', 'action' => 'public_task_form_unsubscribe_all', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/delete\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_unsubscribe', 'admin/tasks/forms/:public_task_form_id/delete/unsubscribe', array ( 'controller' => 'public_task_forms_admin', 'action' => 'public_task_form_unsubscribe', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/delete\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_subscribe', 'admin/tasks/forms/:public_task_form_id/delete/subscribe', array ( 'controller' => 'public_task_forms_admin', 'action' => 'public_task_form_subscribe', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/delete\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_subscriptions', 'admin/tasks/forms/:public_task_form_id/delete/subscriptions', array ( 'controller' => 'public_task_forms_admin', 'action' => 'public_task_form_manage_subscriptions', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_delete', 'admin/tasks/forms/:public_task_form_id/delete', array ( 'controller' => 'public_task_forms_admin', 'action' => 'delete', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_disable', 'admin/tasks/forms/:public_task_form_id/disable', array ( 'controller' => 'public_task_forms_admin', 'action' => 'disable', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_enable', 'admin/tasks/forms/:public_task_form_id/enable', array ( 'controller' => 'public_task_forms_admin', 'action' => 'enable', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_edit', 'admin/tasks/forms/:public_task_form_id/edit', array ( 'controller' => 'public_task_forms_admin', 'action' => 'edit', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form', 'admin/tasks/forms/:public_task_form_id', array ( 'controller' => 'public_task_forms_admin', 'action' => 'view', 'module' => 'tasks', ), array (
  0 => 'public_task_form_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/forms\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_forms_add', 'admin/tasks/forms/add', array ( 'controller' => 'public_task_forms_admin', 'action' => 'add', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/do-resolve-duplicate$/', $path, $matches)) {
      return Router::doMatch($path, 'tasks_admin_do_resolve_duplicate_id', 'admin/tasks/do-resolve-duplicate', array ( 'controller' => 'tasks_admin', 'action' => 'do_resolve_duplicate_ids', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/resolve-duplicate$/', $path, $matches)) {
      return Router::doMatch($path, 'tasks_admin_resolve_duplicate_id', 'admin/tasks/resolve-duplicate', array ( 'controller' => 'tasks_admin', 'action' => 'resolve_duplicate_ids', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'tasks_admin_settings', 'admin/tasks/settings', array ( 'controller' => 'tasks_admin', 'action' => 'settings', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'tasks_admin', 'admin/tasks', array ( 'controller' => 'tasks_admin', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tasks\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task', 'tasks/:task_id', array ( 'controller' => 'public_tasks', 'action' => 'view', 'module' => 'tasks', ), array (
  0 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^tasks\\/submit-successful$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_success', 'tasks/submit-successful', array ( 'controller' => 'public_task_forms', 'action' => 'success', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tasks\\/submit\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'public_task_form_submit', 'tasks/submit/:public_task_form_slug', array ( 'controller' => 'public_task_forms', 'action' => 'submit', 'module' => 'tasks', ), array (
  0 => 'public_task_form_slug',
), $matches, $query_string);
    } elseif(preg_match('/^tasks\\/check$/', $path, $matches)) {
      return Router::doMatch($path, 'public_tasks_check', 'tasks/check', array ( 'controller' => 'public_tasks', 'action' => 'check', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'public_tasks', 'tasks', array ( 'controller' => 'public_tasks', 'action' => 'index', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'milestone_tasks', 'projects/:project_slug/milestones/:milestone_id/tasks', array ( 'controller' => 'milestone_tasks', 'action' => 'index', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/related\\/(\\d+)\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_related_tasks_remove', 'projects/:project_slug/tasks/:task_id/related/:related_task_id/remove', array ( 'controller' => 'related_tasks', 'action' => 'remove_task', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'related_task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/related\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_related_tasks_add', 'projects/:project_slug/tasks/:task_id/related/add', array ( 'controller' => 'related_tasks', 'action' => 'add_task', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/related$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_related_tasks', 'projects/:project_slug/tasks/:task_id/related', array ( 'controller' => 'related_tasks', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/update-label$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_update_label', 'projects/:project_slug/tasks/:task_id/update-label', array ( 'controller' => 'tasks', 'action' => 'project_task_update_label', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/assignees$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_assignees', 'projects/:project_slug/tasks/:task_id/assignees', array ( 'controller' => 'tasks', 'action' => 'project_task_assignees', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_copy_to_project', 'projects/:project_slug/tasks/:task_id/copy-to-project', array ( 'controller' => 'tasks', 'action' => 'project_task_copy_to_project', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_move_to_project', 'projects/:project_slug/tasks/:task_id/move-to-project', array ( 'controller' => 'tasks', 'action' => 'project_task_move_to_project', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reschedule$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reschedule', 'projects/:project_slug/tasks/:task_id/reschedule', array ( 'controller' => 'tasks', 'action' => 'project_task_reschedule', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/sharing$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_sharing_settings', 'projects/:project_slug/tasks/:task_id/sharing', array ( 'controller' => 'tasks', 'action' => 'project_task_sharing_settings', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminder_delete', 'projects/:project_slug/tasks/:task_id/reminders/:reminder_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_delete_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminder_dismiss', 'projects/:project_slug/tasks/:task_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'tasks', 'action' => 'project_task_dismiss_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminder_send', 'projects/:project_slug/tasks/:task_id/reminders/:reminder_id/send', array ( 'controller' => 'tasks', 'action' => 'project_task_send_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminder_edit', 'projects/:project_slug/tasks/:task_id/reminders/:reminder_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminder', 'projects/:project_slug/tasks/:task_id/reminders/:reminder_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminders_nudge', 'projects/:project_slug/tasks/:task_id/reminders/nudge', array ( 'controller' => 'tasks', 'action' => 'project_task_nudge_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminders_add', 'projects/:project_slug/tasks/:task_id/reminders/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_reminder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reminders', 'projects/:project_slug/tasks/:task_id/reminders', array ( 'controller' => 'tasks', 'action' => 'project_task_reminders', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_untrash', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_trash', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_unarchive', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_archive', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_preview', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/preview', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_preview_content', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_download', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/download', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_download_content', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_delete', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_attachment_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment_edit', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachment', 'projects/:project_slug/tasks/:task_id/attachments/:attachment_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachments_add', 'projects/:project_slug/tasks/:task_id/attachments/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_attachments', 'projects/:project_slug/tasks/:task_id/attachments', array ( 'controller' => 'tasks', 'action' => 'project_task_attachments', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_unsubscribe_all', 'projects/:project_slug/tasks/:task_id/unsubscribe_all', array ( 'controller' => 'tasks', 'action' => 'project_task_unsubscribe_all', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_unsubscribe', 'projects/:project_slug/tasks/:task_id/unsubscribe', array ( 'controller' => 'tasks', 'action' => 'project_task_unsubscribe', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subscribe', 'projects/:project_slug/tasks/:task_id/subscribe', array ( 'controller' => 'tasks', 'action' => 'project_task_subscribe', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subscriptions', 'projects/:project_slug/tasks/:task_id/subscriptions', array ( 'controller' => 'tasks', 'action' => 'project_task_manage_subscriptions', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_untrash', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_trash', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_unarchive', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_archive', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_untrash', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_trash', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_unarchive', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_archive', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_preview', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_preview_content', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_download', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_download_content', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_delete', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachment_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment_edit', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_edit_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachment', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_view_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachments_add', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments/add', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_add_attachment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_attachments', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/attachments', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_attachments', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_delete', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_comment_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment_edit', 'projects/:project_slug/tasks/:task_id/comments/:comment_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_comment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comment', 'projects/:project_slug/tasks/:task_id/comments/:comment_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_comment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comments_unlock', 'projects/:project_slug/tasks/:task_id/comments/unlock', array ( 'controller' => 'tasks', 'action' => 'project_task_comments_unlock', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comments_lock', 'projects/:project_slug/tasks/:task_id/comments/lock', array ( 'controller' => 'tasks', 'action' => 'project_task_comments_lock', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comments_add', 'projects/:project_slug/tasks/:task_id/comments/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_comment', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_comments', 'projects/:project_slug/tasks/:task_id/comments', array ( 'controller' => 'tasks', 'action' => 'project_task_comments', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/subtasks\\/([a-z0-9\\-\\._]+)\\/assignees$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_assignees', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/assignees', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_assignees', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/([a-z0-9\\-\\._]+)\\/subtasks\\/([a-z0-9\\-\\._]+)\\/update-label$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_update_label', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/update-label', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_update_label', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/update-priority$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_update_priority', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/update-priority', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_update_priority', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/reschedule$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_reschedule', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/reschedule', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_reschedule', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_delete', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_untrash', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_trash', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_unarchive', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_archive', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_unsubscribe_all', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/unsubscribe_all', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_unsubscribe_all', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_unsubscribe', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/unsubscribe', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_unsubscribe', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_subscribe', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/subscribe', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_subscribe', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_subscriptions', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/subscriptions', array ( 'controller' => 'tasks', 'action' => 'project_task_subtask_manage_subscriptions', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/reopen$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_reopen', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/reopen', array ( 'controller' => 'tasks', 'action' => 'project_task_reopen_subtask', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/complete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_complete', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/complete', array ( 'controller' => 'tasks', 'action' => 'project_task_complete_subtask', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask_edit', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_subtask', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtask', 'projects/:project_slug/tasks/:task_id/subtasks/:subtask_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_subtask', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
  2 => 'subtask_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtasks_reorder', 'projects/:project_slug/tasks/:task_id/subtasks/reorder', array ( 'controller' => 'tasks', 'action' => 'project_task_reorder_subtasks', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtasks_add', 'projects/:project_slug/tasks/:task_id/subtasks/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_subtask', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtasks_archive', 'projects/:project_slug/tasks/:task_id/subtasks/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_subtasks_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/subtasks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_subtasks', 'projects/:project_slug/tasks/:task_id/subtasks', array ( 'controller' => 'tasks', 'action' => 'project_task_subtasks', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/update-priority$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_update_priority', 'projects/:project_slug/tasks/:task_id/update-priority', array ( 'controller' => 'tasks', 'action' => 'project_task_update_priority', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/reopen$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_reopen', 'projects/:project_slug/tasks/:task_id/reopen', array ( 'controller' => 'tasks', 'action' => 'project_task_reopen', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/complete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_complete', 'projects/:project_slug/tasks/:task_id/complete', array ( 'controller' => 'tasks', 'action' => 'project_task_complete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_delete', 'projects/:project_slug/tasks/:task_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_state_delete', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_untrash', 'projects/:project_slug/tasks/:task_id/untrash', array ( 'controller' => 'tasks', 'action' => 'project_task_state_untrash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_trash', 'projects/:project_slug/tasks/:task_id/trash', array ( 'controller' => 'tasks', 'action' => 'project_task_state_trash', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_unarchive', 'projects/:project_slug/tasks/:task_id/unarchive', array ( 'controller' => 'tasks', 'action' => 'project_task_state_unarchive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_archive', 'projects/:project_slug/tasks/:task_id/archive', array ( 'controller' => 'tasks', 'action' => 'project_task_state_archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/update-category$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_update_category', 'projects/:project_slug/tasks/update-category', array ( 'controller' => 'tasks', 'action' => 'project_task_update_category', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_category_delete', 'projects/:project_slug/tasks/categories/:category_id/delete', array ( 'controller' => 'tasks', 'action' => 'project_task_delete_category', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_category_edit', 'projects/:project_slug/tasks/categories/:category_id/edit', array ( 'controller' => 'tasks', 'action' => 'project_task_edit_category', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_category', 'projects/:project_slug/tasks/categories/:category_id', array ( 'controller' => 'tasks', 'action' => 'project_task_view_category', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_categories_add', 'projects/:project_slug/tasks/categories/add', array ( 'controller' => 'tasks', 'action' => 'project_task_add_category', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_categories', 'projects/:project_slug/tasks/categories', array ( 'controller' => 'tasks', 'action' => 'project_task_categories', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task_edit', 'projects/:project_slug/tasks/:task_id/edit', array ( 'controller' => 'tasks', 'action' => 'edit', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_task', 'projects/:project_slug/tasks/:task_id', array ( 'controller' => 'tasks', 'action' => 'view', 'module' => 'tasks', ), array (
  0 => 'project_slug',
  1 => 'task_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_add', 'projects/:project_slug/tasks/add', array ( 'controller' => 'tasks', 'action' => 'add', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/clean-up$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_clean_up', 'projects/:project_slug/tasks/clean-up', array ( 'controller' => 'tasks', 'action' => 'clean_up', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_reorder', 'projects/:project_slug/tasks/reorder', array ( 'controller' => 'tasks', 'action' => 'reorder', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_mass_edit', 'projects/:project_slug/tasks/mass-edit', array ( 'controller' => 'tasks', 'action' => 'mass_edit', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks_archive', 'projects/:project_slug/tasks/archive', array ( 'controller' => 'tasks', 'action' => 'archive', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tasks', 'projects/:project_slug/tasks', array ( 'controller' => 'tasks', 'action' => 'index', 'module' => 'tasks', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^my-tasks\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'my_tasks_settings', 'my-tasks/settings', array ( 'controller' => 'my_tasks', 'action' => 'settings', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^my-tasks\\/unassigned$/', $path, $matches)) {
      return Router::doMatch($path, 'my_tasks_unassigned', 'my-tasks/unassigned', array ( 'controller' => 'my_tasks', 'action' => 'unassigned', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^my-tasks\\/completed$/', $path, $matches)) {
      return Router::doMatch($path, 'my_tasks_completed', 'my-tasks/completed', array ( 'controller' => 'my_tasks', 'action' => 'completed', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^my-tasks\\/refresh$/', $path, $matches)) {
      return Router::doMatch($path, 'my_tasks_refresh', 'my-tasks/refresh', array ( 'controller' => 'my_tasks', 'action' => 'refresh', 'module' => 'tasks', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/invoicing$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_invoicing', 'admin/indices/object-contexts/rebuild/invoicing', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_invoicing', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/invoicing$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_invoicing', 'admin/indices/activity-logs/rebuild/invoicing', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_invoicing', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoicing\\/settings\\/change-description-formats$/', $path, $matches)) {
      return Router::doMatch($path, 'invoicing_settings_change_description_formats', 'invoicing/settings/change-description-formats', array ( 'controller' => 'invoicing_settings_admin', 'action' => 'change_description_formats', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoicing\\/settings\\/change-counter-value$/', $path, $matches)) {
      return Router::doMatch($path, 'invoicing_settings_change_counter_value', 'invoicing/settings/change-counter-value', array ( 'controller' => 'invoicing_settings_admin', 'action' => 'change_counter_value', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoicing\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'invoicing_settings', 'invoicing/settings', array ( 'controller' => 'invoicing_settings_admin', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_untrash', 'recurring/profile/:recurring_profile_id/untrash', array ( 'controller' => 'recurring_invoice', 'action' => 'recurring_profile_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_trash', 'recurring/profile/:recurring_profile_id/trash', array ( 'controller' => 'recurring_invoice', 'action' => 'recurring_profile_state_trash', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_unarchive', 'recurring/profile/:recurring_profile_id/unarchive', array ( 'controller' => 'recurring_invoice', 'action' => 'recurring_profile_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_archive', 'recurring/profile/:recurring_profile_id/archive', array ( 'controller' => 'recurring_invoice', 'action' => 'recurring_profile_state_archive', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/duplicate$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_duplicate', 'recurring/profile/:recurring_profile_id/duplicate', array ( 'controller' => 'recurring_invoice', 'action' => 'duplicate', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/trigger$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_trigger', 'recurring/profile/:recurring_profile_id/trigger', array ( 'controller' => 'recurring_invoice', 'action' => 'trigger', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_delete', 'recurring/profile/:recurring_profile_id/delete', array ( 'controller' => 'recurring_invoice', 'action' => 'recurring_profile_state_delete', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_edit', 'recurring/profile/:recurring_profile_id/edit', array ( 'controller' => 'recurring_invoice', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile_add', 'recurring/profile/add', array ( 'controller' => 'recurring_invoice', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profile', 'recurring/profile/:recurring_profile_id', array ( 'controller' => 'recurring_invoice', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'recurring_profile_id',
), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profiles_mass_edit', 'recurring/profile/mass-edit', array ( 'controller' => 'recurring_invoice', 'action' => 'mass_edit', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profiles_archive', 'recurring/profile/archive', array ( 'controller' => 'recurring_invoice', 'action' => 'archive', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^recurring\\/profile$/', $path, $matches)) {
      return Router::doMatch($path, 'recurring_profiles', 'recurring/profile', array ( 'controller' => 'recurring_invoice', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/created\\/create-milestones$/', $path, $matches)) {
      return Router::doMatch($path, 'project_created_create_milestones', 'projects/:project_slug/created/create-milestones', array ( 'controller' => 'project_based_on_quote_created', 'action' => 'create_milestones', 'module' => 'invoicing', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/created\\/import-quote-comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_created_import_quote_comments', 'projects/:project_slug/created/import-quote-comments', array ( 'controller' => 'project_based_on_quote_created', 'action' => 'import_quote_comments', 'module' => 'invoicing', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/quotes\\/(\\d+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'company_quote_pdf', 'people/:company_id/quotes/:quote_id/pdf', array ( 'controller' => 'company_quotes', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/quotes\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'company_quote', 'people/:company_id/quotes/:quote_id', array ( 'controller' => 'company_quotes', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(([a-z0-9\\-\\._]+))\\/invoice\\/preview-items$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_invoicing_preview_items', 'quotes/:quote_id/invoice/preview-items', array ( 'controller' => 'quotes', 'action' => 'quote_preview_items', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(([a-z0-9\\-\\._]+))\\/invoice\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_invoicing', 'quotes/:quote_id/invoice/add', array ( 'controller' => 'quotes', 'action' => 'quote_add_invoice', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_untrash', 'quotes/:quote_id/untrash', array ( 'controller' => 'quotes', 'action' => 'quote_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_trash', 'quotes/:quote_id/trash', array ( 'controller' => 'quotes', 'action' => 'quote_state_trash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_unarchive', 'quotes/:quote_id/unarchive', array ( 'controller' => 'quotes', 'action' => 'quote_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_archive', 'quotes/:quote_id/archive', array ( 'controller' => 'quotes', 'action' => 'quote_state_archive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_untrash', 'quotes/:quote_id/attachments/:attachment_id/untrash', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_trash', 'quotes/:quote_id/attachments/:attachment_id/trash', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_state_trash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_unarchive', 'quotes/:quote_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_archive', 'quotes/:quote_id/attachments/:attachment_id/archive', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_state_archive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_preview', 'quotes/:quote_id/attachments/:attachment_id/preview', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_preview_content', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_download', 'quotes/:quote_id/attachments/:attachment_id/download', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_download_content', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_delete', 'quotes/:quote_id/attachments/:attachment_id/delete', array ( 'controller' => 'quotes', 'action' => 'quote_attachment_state_delete', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment_edit', 'quotes/:quote_id/attachments/:attachment_id/edit', array ( 'controller' => 'quotes', 'action' => 'quote_edit_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachment', 'quotes/:quote_id/attachments/:attachment_id', array ( 'controller' => 'quotes', 'action' => 'quote_view_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachments_add', 'quotes/:quote_id/attachments/add', array ( 'controller' => 'quotes', 'action' => 'quote_add_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_attachments', 'quotes/:quote_id/attachments', array ( 'controller' => 'quotes', 'action' => 'quote_attachments', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_unsubscribe_all', 'quotes/:quote_id/unsubscribe_all', array ( 'controller' => 'quotes', 'action' => 'quote_unsubscribe_all', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_unsubscribe', 'quotes/:quote_id/unsubscribe', array ( 'controller' => 'quotes', 'action' => 'quote_unsubscribe', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_subscribe', 'quotes/:quote_id/subscribe', array ( 'controller' => 'quotes', 'action' => 'quote_subscribe', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_subscriptions', 'quotes/:quote_id/subscriptions', array ( 'controller' => 'quotes', 'action' => 'quote_manage_subscriptions', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_untrash', 'quotes/:quote_id/comments/:comment_id/untrash', array ( 'controller' => 'quotes', 'action' => 'quote_comment_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_trash', 'quotes/:quote_id/comments/:comment_id/trash', array ( 'controller' => 'quotes', 'action' => 'quote_comment_state_trash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_unarchive', 'quotes/:quote_id/comments/:comment_id/unarchive', array ( 'controller' => 'quotes', 'action' => 'quote_comment_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_archive', 'quotes/:quote_id/comments/:comment_id/archive', array ( 'controller' => 'quotes', 'action' => 'quote_comment_state_archive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_untrash', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_trash', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_state_trash', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_unarchive', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_archive', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_state_archive', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_preview', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_preview_content', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_download', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_download_content', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_delete', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachment_state_delete', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment_edit', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'quotes', 'action' => 'quote_comment_edit_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachment', 'quotes/:quote_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'quotes', 'action' => 'quote_comment_view_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachments_add', 'quotes/:quote_id/comments/:comment_id/attachments/add', array ( 'controller' => 'quotes', 'action' => 'quote_comment_add_attachment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_attachments', 'quotes/:quote_id/comments/:comment_id/attachments', array ( 'controller' => 'quotes', 'action' => 'quote_comment_attachments', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_delete', 'quotes/:quote_id/comments/:comment_id/delete', array ( 'controller' => 'quotes', 'action' => 'quote_comment_state_delete', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment_edit', 'quotes/:quote_id/comments/:comment_id/edit', array ( 'controller' => 'quotes', 'action' => 'quote_edit_comment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comment', 'quotes/:quote_id/comments/:comment_id', array ( 'controller' => 'quotes', 'action' => 'quote_view_comment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comments_unlock', 'quotes/:quote_id/comments/unlock', array ( 'controller' => 'quotes', 'action' => 'quote_comments_unlock', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comments_lock', 'quotes/:quote_id/comments/lock', array ( 'controller' => 'quotes', 'action' => 'quote_comments_lock', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comments_add', 'quotes/:quote_id/comments/add', array ( 'controller' => 'quotes', 'action' => 'quote_add_comment', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_comments', 'quotes/:quote_id/comments', array ( 'controller' => 'quotes', 'action' => 'quote_comments', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^view-quote\\/(\\w+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_public_pdf', 'view-quote/:quote_public_id/pdf', array ( 'controller' => 'public_quotes', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'quote_public_id',
), $matches, $query_string);
    } elseif(preg_match('/^view-quote\\/(\\w+)\\/check$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_check', 'view-quote/:quote_public_id/check', array ( 'controller' => 'public_quotes', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'quote_public_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/change-language$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_change_language', 'quotes/:quote_id/change-language', array ( 'controller' => 'quotes', 'action' => 'change_language', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/save-client$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_save_client', 'quotes/:quote_id/save-client', array ( 'controller' => 'quotes', 'action' => 'save_client', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_delete', 'quotes/:quote_id/delete', array ( 'controller' => 'quotes', 'action' => 'quote_state_delete', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/notify$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_notify', 'quotes/:quote_id/notify', array ( 'controller' => 'quotes', 'action' => 'notify', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_edit', 'quotes/:quote_id/edit', array ( 'controller' => 'quotes', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/lost$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_lost', 'quotes/:quote_id/lost', array ( 'controller' => 'quotes', 'action' => 'lost', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/won$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_won', 'quotes/:quote_id/won', array ( 'controller' => 'quotes', 'action' => 'won', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_send', 'quotes/:quote_id/send', array ( 'controller' => 'quotes', 'action' => 'send', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'quote_pdf', 'quotes/:quote_id/pdf', array ( 'controller' => 'quotes', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'quote', 'quotes/:quote_id', array ( 'controller' => 'quotes', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'quotes_archive', 'quotes/archive', array ( 'controller' => 'quotes', 'action' => 'archive', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^quotes\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'quotes_add', 'quotes/add', array ( 'controller' => 'quotes', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^quotes$/', $path, $matches)) {
      return Router::doMatch($path, 'quotes', 'quotes', array ( 'controller' => 'quotes', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/quotes\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_quote', 'people/:company_id/quotes/:quote_id', array ( 'controller' => 'company_quotes', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'quote_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/quotes$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_quotes', 'people/:company_id/quotes', array ( 'controller' => 'company_quotes', 'action' => 'index', 'module' => 'invoicing', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/invoices\\/(\\d+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_invoice_pdf', 'people/:company_id/invoices/:invoice_id/pdf', array ( 'controller' => 'company_invoices', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/invoices\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_invoice', 'people/:company_id/invoices/:invoice_id', array ( 'controller' => 'company_invoices', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/invoices\\/payments$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_invoices_payments', 'people/:company_id/invoices/payments', array ( 'controller' => 'company_invoices', 'action' => 'payments', 'module' => 'invoicing', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/invoices$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_invoices', 'people/:company_id/invoices', array ( 'controller' => 'company_invoices', 'action' => 'index', 'module' => 'invoicing', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^pay-invoice\\/(\\d+)\\/(\\d+)\\/(\\w+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'public_invoice_pdf', 'pay-invoice/:client_id/:invoice_id/:invoice_hash/pdf', array ( 'controller' => 'public_invoices', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'client_id',
  1 => 'invoice_id',
  2 => 'invoice_hash',
), $matches, $query_string);
    } elseif(preg_match('/^pay-invoice\\/(\\d+)\\/(\\d+)\\/(\\w+)$/', $path, $matches)) {
      return Router::doMatch($path, 'public_invoice', 'pay-invoice/:client_id/:invoice_id/:invoice_hash', array ( 'controller' => 'public_invoices', 'action' => 'pay', 'module' => 'invoicing', ), array (
  0 => 'client_id',
  1 => 'invoice_id',
  2 => 'invoice_hash',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_untrash', 'invoices/:invoice_id/untrash', array ( 'controller' => 'invoices', 'action' => 'invoice_state_untrash', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_trash', 'invoices/:invoice_id/trash', array ( 'controller' => 'invoices', 'action' => 'invoice_state_trash', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_unarchive', 'invoices/:invoice_id/unarchive', array ( 'controller' => 'invoices', 'action' => 'invoice_state_unarchive', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_archive', 'invoices/:invoice_id/archive', array ( 'controller' => 'invoices', 'action' => 'invoice_state_archive', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filter_delete', 'reports/invoices/filter/summarized/:summarized_invoices_filter_id/delete', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'delete', ), array (
  0 => 'summarized_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filter_edit', 'reports/invoices/filter/summarized/:summarized_invoices_filter_id/edit', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'edit', ), array (
  0 => 'summarized_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filter', 'reports/invoices/filter/summarized/:summarized_invoices_filter_id', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'view', ), array (
  0 => 'summarized_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filters_export', 'reports/invoices/filter/summarized/export', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filters_run', 'reports/invoices/filter/summarized/run', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filters_add', 'reports/invoices/filter/summarized/add', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/summarized$/', $path, $matches)) {
      return Router::doMatch($path, 'summarized_invoices_filters', 'reports/invoices/filter/summarized', array ( 'controller' => 'summarized_invoices_filters', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filter_delete', 'reports/invoices/filter/detailed/:detailed_invoices_filter_id/delete', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'delete', ), array (
  0 => 'detailed_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filter_edit', 'reports/invoices/filter/detailed/:detailed_invoices_filter_id/edit', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'edit', ), array (
  0 => 'detailed_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filter', 'reports/invoices/filter/detailed/:detailed_invoices_filter_id', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'view', ), array (
  0 => 'detailed_invoices_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filters_export', 'reports/invoices/filter/detailed/export', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filters_run', 'reports/invoices/filter/detailed/run', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filters_add', 'reports/invoices/filter/detailed/add', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/invoices\\/filter\\/detailed$/', $path, $matches)) {
      return Router::doMatch($path, 'detailed_invoices_filters', 'reports/invoices/filter/detailed', array ( 'controller' => 'detailed_invoices_filters', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/items\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_item_delete', 'admin/invoicing/items/:item_id/delete', array ( 'controller' => 'invoice_item_templates_admin', 'action' => 'delete', 'module' => 'invoicing', ), array (
  0 => 'item_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/items\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_item_edit', 'admin/invoicing/items/:item_id/edit', array ( 'controller' => 'invoice_item_templates_admin', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'item_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/items\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_item_add', 'admin/invoicing/items/add', array ( 'controller' => 'invoice_item_templates_admin', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/items\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_items_reorder', 'admin/invoicing/items/reorder', array ( 'controller' => 'invoice_item_templates_admin', 'action' => 'reorder', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/items$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_items', 'admin/invoicing/items', array ( 'controller' => 'invoice_item_templates_admin', 'action' => 'index', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/(\\d+)\\/remove-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_note_remove_default', 'admin/invoicing/notes/:note_id/remove-default', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'remove_default', 'module' => 'invoicing', ), array (
  0 => 'note_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_note_set_as_default', 'admin/invoicing/notes/:note_id/set-as-default', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'set_as_default', 'module' => 'invoicing', ), array (
  0 => 'note_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_note_delete', 'admin/invoicing/notes/:note_id/delete', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'delete', 'module' => 'invoicing', ), array (
  0 => 'note_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_note_edit', 'admin/invoicing/notes/:note_id/edit', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'note_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_note', 'admin/invoicing/notes/:note_id', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'note_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_notes_add', 'admin/invoicing/notes/add', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/notes$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_notes', 'admin/invoicing/notes', array ( 'controller' => 'invoice_note_templates_admin', 'action' => 'index', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/invoice-overdue-reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_invoice_overdue_reminders', 'admin/invoicing/invoice-overdue-reminders', array ( 'controller' => 'invoice_overdue_reminders_admin', 'action' => 'index', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_settings', 'admin/invoicing/pdf-settings', array ( 'controller' => 'pdf_settings_admin', 'action' => 'index', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/sample$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_sample', 'admin/invoicing/pdf/sample', array ( 'controller' => 'pdf_settings_admin', 'action' => 'sample', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/footer$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_footer', 'admin/invoicing/pdf/footer', array ( 'controller' => 'pdf_settings_admin', 'action' => 'footer', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/body$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_body', 'admin/invoicing/pdf/body', array ( 'controller' => 'pdf_settings_admin', 'action' => 'body', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/header$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_header', 'admin/invoicing/pdf/header', array ( 'controller' => 'pdf_settings_admin', 'action' => 'header', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/paper\\/remove-background$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_paper_remove_background', 'admin/invoicing/pdf/paper/remove-background', array ( 'controller' => 'pdf_settings_admin', 'action' => 'remove_background', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf\\/paper$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf_paper', 'admin/invoicing/pdf/paper', array ( 'controller' => 'pdf_settings_admin', 'action' => 'paper', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoicing\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_invoicing_pdf', 'admin/invoicing/pdf', array ( 'controller' => 'pdf_settings_admin', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/(\\d+)\\/remove-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rate_remove_default', 'admin/tax_rates/:tax_rate_id/remove-default', array ( 'controller' => 'tax_rates_admin', 'action' => 'remove_default', 'module' => 'invoicing', ), array (
  0 => 'tax_rate_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rate_set_as_default', 'admin/tax_rates/:tax_rate_id/set-as-default', array ( 'controller' => 'tax_rates_admin', 'action' => 'set_as_default', 'module' => 'invoicing', ), array (
  0 => 'tax_rate_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rate_delete', 'admin/tax_rates/:tax_rate_id/delete', array ( 'controller' => 'tax_rates_admin', 'action' => 'delete', 'module' => 'invoicing', ), array (
  0 => 'tax_rate_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rate_edit', 'admin/tax_rates/:tax_rate_id/edit', array ( 'controller' => 'tax_rates_admin', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'tax_rate_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rate', 'admin/tax_rates/:tax_rate_id', array ( 'controller' => 'tax_rates_admin', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'tax_rate_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax_rates\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rates_add', 'admin/tax_rates/add', array ( 'controller' => 'tax_rates_admin', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/tax-rates$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_tax_rates', 'admin/tax-rates', array ( 'controller' => 'tax_rates_admin', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/payment\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_custom_payment_delete', 'invoices/:invoice_id/payment/:invoice_payment_id/delete', array ( 'controller' => 'invoice_payments', 'action' => 'delete', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
  1 => 'invoice_payment_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/payment\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_custom_payment_edit', 'invoices/:invoice_id/payment/:invoice_payment_id/edit', array ( 'controller' => 'invoice_payments', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
  1 => 'invoice_payment_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/payment\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_custom_payments_add_old', 'invoices/:invoice_id/payment/add', array ( 'controller' => 'invoice_payments', 'action' => 'add', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/payments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_payment_delete', 'company/:company_id/invoices/:invoice_id/payments/:payment_id/delete', array ( 'controller' => 'company_invoices', 'action' => 'invoice_payment_delete', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
  2 => 'payment_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/payments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_payment_edit', 'company/:company_id/invoices/:invoice_id/payments/:payment_id/edit', array ( 'controller' => 'company_invoices', 'action' => 'invoice_payment_edit', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
  2 => 'payment_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/payments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_payment', 'company/:company_id/invoices/:invoice_id/payments/:payment_id', array ( 'controller' => 'company_invoices', 'action' => 'invoice_payment_view', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
  2 => 'payment_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/custom\\/payments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_custom_payments_add', 'company/:company_id/invoices/:invoice_id/custom/payments/add', array ( 'controller' => 'company_invoices', 'action' => 'invoice_custom_payments_add', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/payments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_payments_add', 'company/:company_id/invoices/:invoice_id/payments/add', array ( 'controller' => 'company_invoices', 'action' => 'invoice_payments_add', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^company\\/(\\d+)\\/invoices\\/(\\d+)\\/payments$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_payments', 'company/:company_id/invoices/:invoice_id/payments', array ( 'controller' => 'company_invoices', 'action' => 'invoice_payments', 'module' => 'invoicing', ), array (
  0 => 'company_id',
  1 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/paid$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_paid', 'invoices/:invoice_id/paid', array ( 'controller' => 'invoices', 'action' => 'mark_as_paid', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/all-payments$/', $path, $matches)) {
      return Router::doMatch($path, 'invoices_all_payments', 'invoices/all-payments', array ( 'controller' => 'invoices', 'action' => 'list_payments', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/public\\/payment$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_public_payment_info', 'invoices/:invoice_id/public/payment', array ( 'controller' => 'invoices', 'action' => 'public_payment_info', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/change-language$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_change_language', 'invoices/:invoice_id/change-language', array ( 'controller' => 'invoices', 'action' => 'change_language', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/notify$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_notify', 'invoices/:invoice_id/notify', array ( 'controller' => 'invoices', 'action' => 'notify', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/items\\/release$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_items_release', 'invoices/:invoice_id/items/release', array ( 'controller' => 'invoices', 'action' => 'items_release', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/time$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_time', 'invoices/:invoice_id/time', array ( 'controller' => 'invoices', 'action' => 'time', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/pdf$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_pdf', 'invoices/:invoice_id/pdf', array ( 'controller' => 'invoices', 'action' => 'pdf', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/cancel$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_cancel', 'invoices/:invoice_id/cancel', array ( 'controller' => 'invoices', 'action' => 'cancel', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_delete', 'invoices/:invoice_id/delete', array ( 'controller' => 'invoices', 'action' => 'invoice_state_delete', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_edit', 'invoices/:invoice_id/edit', array ( 'controller' => 'invoices', 'action' => 'edit', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)\\/issue$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice_issue', 'invoices/:invoice_id/issue', array ( 'controller' => 'invoices', 'action' => 'issue', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'invoice', 'invoices/:invoice_id', array ( 'controller' => 'invoices', 'action' => 'view', 'module' => 'invoicing', ), array (
  0 => 'invoice_id',
), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'invoices_archive', 'invoices/archive', array ( 'controller' => 'invoices', 'action' => 'archive', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoices\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'invoices_add', 'invoices/add', array ( 'controller' => 'invoices', 'action' => 'add', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^invoices$/', $path, $matches)) {
      return Router::doMatch($path, 'invoices', 'invoices', array ( 'controller' => 'invoices', 'module' => 'invoicing', ), array(), $matches, $query_string);
    } elseif(preg_match('/^status\\/update\\/(\\d+)\\/reply$/', $path, $matches)) {
      return Router::doMatch($path, 'status_update_reply', 'status/update/:status_update_id/reply', array ( 'controller' => 'status', 'action' => 'view', 'module' => 'status', ), array (
  0 => 'status_update_id',
), $matches, $query_string);
    } elseif(preg_match('/^status\\/update\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'status_update', 'status/update/:status_update_id', array ( 'controller' => 'status', 'action' => 'view', 'module' => 'status', ), array (
  0 => 'status_update_id',
), $matches, $query_string);
    } elseif(preg_match('/^status\\/count-new-messages$/', $path, $matches)) {
      return Router::doMatch($path, 'status_updates_count_new_messages', 'status/count-new-messages', array ( 'controller' => 'status', 'action' => 'count_new_messages', 'module' => 'status', ), array(), $matches, $query_string);
    } elseif(preg_match('/^status\\/rss$/', $path, $matches)) {
      return Router::doMatch($path, 'status_updates_rss', 'status/rss', array ( 'controller' => 'status', 'action' => 'rss', 'module' => 'status', ), array(), $matches, $query_string);
    } elseif(preg_match('/^status\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'status_update_delete', 'status/delete', array ( 'controller' => 'status', 'action' => 'delete', 'module' => 'status', ), array(), $matches, $query_string);
    } elseif(preg_match('/^status\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'status_updates_add', 'status/add', array ( 'controller' => 'status', 'action' => 'add', 'module' => 'status', ), array(), $matches, $query_string);
    } elseif(preg_match('/^status$/', $path, $matches)) {
      return Router::doMatch($path, 'status_updates', 'status', array ( 'controller' => 'status', 'action' => 'index', 'module' => 'status', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/tracking$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_tracking', 'admin/indices/object-contexts/rebuild/tracking', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_tracking', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/tracking$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_tracking', 'admin/indices/activity-logs/rebuild/tracking', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_tracking', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tracking-report\\/invoice\\/preview-items$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_report_invoicing_preview_items', 'tracking-report/invoice/preview-items', array ( 'controller' => 'tracking_reports', 'action' => 'tracking_report_preview_items', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tracking-report\\/invoice\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_report_invoicing', 'tracking-report/invoice/add', array ( 'controller' => 'tracking_reports', 'action' => 'tracking_report_add_invoice', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/budget$/', $path, $matches)) {
      return Router::doMatch($path, 'project_budget', 'projects/:project_slug/budget', array ( 'controller' => 'project_budget', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/hourly-rates\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_hourly_rate', 'projects/:project_slug/hourly-rates/:job_type_id/edit', array ( 'controller' => 'project_hourly_rates', 'action' => 'edit', 'module' => 'tracking', ), array (
  0 => 'project_slug',
  1 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/hourly-rates$/', $path, $matches)) {
      return Router::doMatch($path, 'project_hourly_rates', 'projects/:project_slug/hourly-rates', array ( 'controller' => 'project_hourly_rates', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_category_delete', 'admin/expense-categories/:expense_category_id/delete', array ( 'controller' => 'expense_categories_admin', 'action' => 'delete', 'module' => 'tracking', ), array (
  0 => 'expense_category_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_category_set_as_default', 'admin/expense-categories/:expense_category_id/set-as-default', array ( 'controller' => 'expense_categories_admin', 'action' => 'set_as_default', 'module' => 'tracking', ), array (
  0 => 'expense_category_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_category_edit', 'admin/expense-categories/:expense_category_id/edit', array ( 'controller' => 'expense_categories_admin', 'action' => 'edit', 'module' => 'tracking', ), array (
  0 => 'expense_category_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_category', 'admin/expense-categories/:expense_category_id', array ( 'controller' => 'expense_categories_admin', 'action' => 'view', 'module' => 'tracking', ), array (
  0 => 'expense_category_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_categories_add', 'admin/expense-categories/add', array ( 'controller' => 'expense_categories_admin', 'action' => 'add', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/expense-categories$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_categories_admin', 'admin/expense-categories', array ( 'controller' => 'expense_categories_admin', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type_delete', 'admin/job-types/:job_type_id/delete', array ( 'controller' => 'job_types_admin', 'action' => 'delete', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type_unarchive', 'admin/job-types/:job_type_id/unarchive', array ( 'controller' => 'job_types_admin', 'action' => 'unarchive', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type_archive', 'admin/job-types/:job_type_id/archive', array ( 'controller' => 'job_types_admin', 'action' => 'archive', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type_set_as_default', 'admin/job-types/:job_type_id/set-as-default', array ( 'controller' => 'job_types_admin', 'action' => 'set_as_default', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type_edit', 'admin/job-types/:job_type_id/edit', array ( 'controller' => 'job_types_admin', 'action' => 'edit', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'job_type', 'admin/job-types/:job_type_id', array ( 'controller' => 'job_types_admin', 'action' => 'view', 'module' => 'tracking', ), array (
  0 => 'job_type_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'job_types_add', 'admin/job-types/add', array ( 'controller' => 'job_types_admin', 'action' => 'add', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/job-types$/', $path, $matches)) {
      return Router::doMatch($path, 'job_types_admin', 'admin/job-types', array ( 'controller' => 'job_types_admin', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/expense-categories$/', $path, $matches)) {
      return Router::doMatch($path, 'expense_categories_info', 'info/expense-categories', array ( 'controller' => 'tracking_api', 'action' => 'expense_categories', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/job-types$/', $path, $matches)) {
      return Router::doMatch($path, 'job_types_info', 'info/job-types', array ( 'controller' => 'tracking_api', 'action' => 'job_types', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^homescreen\\/widgets\\/(\\d+)\\/refresh$/', $path, $matches)) {
      return Router::doMatch($path, 'my_time_homescreen_widget_refresh', 'homescreen/widgets/:widget_id/refresh', array ( 'controller' => 'my_time_homescreen_widget', 'action' => 'refresh', 'module' => 'tracking', ), array (
  0 => 'widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^homescreen\\/widgets\\/(\\d+)\\/add-time$/', $path, $matches)) {
      return Router::doMatch($path, 'my_time_homescreen_widget_add_time', 'homescreen/widgets/:widget_id/add-time', array ( 'controller' => 'my_time_homescreen_widget', 'action' => 'add_time', 'module' => 'tracking', ), array (
  0 => 'widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^homescreen\\/widgets\\/(\\d+)\\/weekly-time$/', $path, $matches)) {
      return Router::doMatch($path, 'my_time_homescreen_widget_weekly_time', 'homescreen/widgets/:widget_id/weekly-time', array ( 'controller' => 'my_time_homescreen_widget', 'action' => 'weekly_time', 'module' => 'tracking', ), array (
  0 => 'widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/timesheet\\/day-details$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_timesheet_day', 'projects/:project_slug/tracking/timesheet/day-details', array ( 'controller' => 'project_tracking', 'action' => 'timesheet_day', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/timesheet$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_timesheet', 'projects/:project_slug/tracking/timesheet', array ( 'controller' => 'project_tracking', 'action' => 'timesheet', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/get-totals$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_get_totals', 'projects/:project_slug/tracking/get-totals', array ( 'controller' => 'project_tracking', 'action' => 'log_get_totals', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/mass-update$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_mass_update', 'projects/:project_slug/tracking/mass-update', array ( 'controller' => 'project_tracking', 'action' => 'log_mass_update', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/budget-vs-cost$/', $path, $matches)) {
      return Router::doMatch($path, 'budget_vs_cost_report', 'reports/budget-vs-cost', array ( 'controller' => 'budget_vs_cost', 'action' => 'budget_vs_cost', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/estimated-vs-tracked-time-run$/', $path, $matches)) {
      return Router::doMatch($path, 'estiamted_vs_tracked_time_report_run', 'reports/estimated-vs-tracked-time-run', array ( 'controller' => 'estimated_vs_tracked_time', 'action' => 'estimated_vs_tracked_time_run', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/estimated-vs-tracked-time$/', $path, $matches)) {
      return Router::doMatch($path, 'estiamted_vs_tracked_time_report', 'reports/estimated-vs-tracked-time', array ( 'controller' => 'estimated_vs_tracked_time', 'action' => 'estimated_vs_tracked_time', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking\\/change\\/status$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_reports_change_status', 'reports/tracking/change/status', array ( 'controller' => 'tracking_reports', 'action' => 'change_records_status', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_report_delete', 'reports/tracking/:tracking_report_id/delete', array ( 'controller' => 'tracking_reports', 'action' => 'delete', 'module' => 'tracking', ), array (
  0 => 'tracking_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_report_edit', 'reports/tracking/:tracking_report_id/edit', array ( 'controller' => 'tracking_reports', 'action' => 'edit', 'module' => 'tracking', ), array (
  0 => 'tracking_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_report', 'reports/tracking/:tracking_report_id', array ( 'controller' => 'tracking_reports', 'action' => 'view', 'module' => 'tracking', ), array (
  0 => 'tracking_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^tracking\\/tracking\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_reports_export', 'tracking/tracking/export', array ( 'controller' => 'tracking_reports', 'action' => 'export', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^tracking\\/tracking\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_reports_run', 'tracking/tracking/run', array ( 'controller' => 'tracking_reports', 'action' => 'run', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_reports_add', 'reports/tracking/add', array ( 'controller' => 'tracking_reports', 'action' => 'add', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/tracking$/', $path, $matches)) {
      return Router::doMatch($path, 'tracking_reports', 'reports/tracking', array ( 'controller' => 'tracking_reports', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^time$/', $path, $matches)) {
      return Router::doMatch($path, 'time', 'time', array ( 'controller' => 'time', 'action' => 'index', 'module' => 'tracking', ), array(), $matches, $query_string);
    } elseif(preg_match('/^paper$/', $path, $matches)) {
      return Router::doMatch($path, 'paper', 'paper', array ( 'controller' => 'scheduled_tasks', 'action' => 'paper', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/convert-to-a-template$/', $path, $matches)) {
      return Router::doMatch($path, 'project_convert_to_a_template', 'projects/:project_slug/convert-to-a-template', array ( 'controller' => 'project', 'action' => 'convert_to_a_template', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^project_action\\/project_changed$/', $path, $matches)) {
      return Router::doMatch($path, 'project_action_project_changed', 'project_action/project_changed', array ( 'controller' => 'project_action', 'action' => 'project_change', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/milestones$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_milestones', 'admin/indices/object-contexts/rebuild/milestones', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_milestones', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/projects$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_projects', 'admin/indices/object-contexts/rebuild/projects', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_projects', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild\\/people$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild_people', 'admin/indices/object-contexts/rebuild/people', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild_people', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/milestones$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_milestones', 'admin/indices/activity-logs/rebuild/milestones', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_milestones', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/projects$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_projects', 'admin/indices/activity-logs/rebuild/projects', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_projects', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild\\/people$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild_people', 'admin/indices/activity-logs/rebuild/people', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild_people', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/names\\/build\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'names_search_index_admin_build', 'admin/search/names/build/:action', array ( 'controller' => 'names_search_index_admin', 'search_index_name' => 'names', 'module' => 'system', ), array (
  0 => 'action',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/project-objects\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'project_objects_search_index_admin_build', 'admin/search/project-objects/build', array ( 'controller' => 'project_objects_search_index_admin', 'action' => 'build', 'search_index_name' => 'project_objects', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/projects\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_search_index_admin_build', 'admin/search/projects/build', array ( 'controller' => 'projects_search_index_admin', 'action' => 'build', 'search_index_name' => 'projects', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/projects-data-cleanup\\/([a-z0-9\\-\\._]+)\\/permanently_delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_projects_data_cleanup_permanently_delete_project', 'admin/projects-data-cleanup/:project_slug/permanently_delete', array ( 'controller' => 'projects_data_cleanup_admin', 'action' => 'permanently_delete_project', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/projects-data-cleanup$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_projects_data_cleanup', 'admin/projects-data-cleanup', array ( 'controller' => 'projects_data_cleanup_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-requests$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_requests', 'admin/project-requests', array ( 'controller' => 'project_requests_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/projects$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_projects', 'admin/projects', array ( 'controller' => 'projects_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/identity\\/revert$/', $path, $matches)) {
      return Router::doMatch($path, 'identity_admin_revert', 'admin/identity/revert', array ( 'controller' => 'identity_admin', 'action' => 'revert', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin_edit_page', 'admin/repsite/:page_id/edit', array ( 'controller' => 'repsite_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'page_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin_delete_page', 'admin/repsite/:page_id/delete', array ( 'controller' => 'repsite_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'page_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite\\/edit-domain$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin_edit_repsite_domain', 'admin/repsite/edit-domain', array ( 'controller' => 'repsite_admin', 'action' => 'edit_repsite_domain', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite\\/add-new-page$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin_add_new_page', 'admin/repsite/add-new-page', array ( 'controller' => 'repsite_admin', 'action' => 'add_new_page', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite\\/get_page$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin_get_page', 'admin/repsite/get_page', array ( 'controller' => 'repsite_admin', 'action' => 'get_page', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/repsite$/', $path, $matches)) {
      return Router::doMatch($path, 'repsite_admin', 'admin/repsite', array ( 'controller' => 'repsite_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/identity$/', $path, $matches)) {
      return Router::doMatch($path, 'identity_admin', 'admin/identity', array ( 'controller' => 'identity_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/settings\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_settings_categories', 'admin/settings/categories', array ( 'controller' => 'categories_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/settings\\/general$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_settings_general', 'admin/settings/general', array ( 'controller' => 'settings', 'action' => 'general', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_settings', 'admin', array ( 'controller' => 'admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_label_set_as_default', 'admin/project-labels/labels/:label_id/set-as-default', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_set_label_as_default', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_label_delete', 'admin/project-labels/labels/:label_id/delete', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_delete_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_label_edit', 'admin/project-labels/labels/:label_id/edit', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_edit_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_label', 'admin/project-labels/labels/:label_id', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_view_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_labels_add', 'admin/project-labels/labels/add', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_add_label', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/project-labels\\/labels$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_admin_labels', 'admin/project-labels/labels', array ( 'controller' => 'project_labels_admin', 'action' => 'projects_admin_labels', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_role_set_as_default', 'admin/roles/project/:role_id/set-as-default', array ( 'controller' => 'project_roles_admin', 'action' => 'set_as_default', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_role_delete', 'admin/roles/project/:role_id/delete', array ( 'controller' => 'project_roles_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_role_edit', 'admin/roles/project/:role_id/edit', array ( 'controller' => 'project_roles_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_role', 'admin/roles/project/:role_id', array ( 'controller' => 'project_roles_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_roles_add', 'admin/roles/project/add', array ( 'controller' => 'project_roles_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/project$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_project_roles', 'admin/roles/project', array ( 'controller' => 'project_roles_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/copy-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_copy_to_project', 'projects/:project_slug/milestones/:milestone_id/copy-to-project', array ( 'controller' => 'milestones', 'action' => 'project_milestone_copy_to_project', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/move-to-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_move_to_project', 'projects/:project_slug/milestones/:milestone_id/move-to-project', array ( 'controller' => 'milestones', 'action' => 'project_milestone_move_to_project', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reschedule$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reschedule', 'projects/:project_slug/milestones/:milestone_id/reschedule', array ( 'controller' => 'milestones', 'action' => 'project_milestone_reschedule', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/timeline$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_timeline', 'projects/timeline', array ( 'controller' => 'projects_timeline', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/reschedule$/', $path, $matches)) {
      return Router::doMatch($path, 'project_reschedule', 'projects/:project_slug/reschedule', array ( 'controller' => 'project', 'action' => 'reschedule', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^s\\/([a-z0-9\\-\\._]+)\\/(\\w+)$/', $path, $matches)) {
      return Router::doMatch($path, 'shared_object', 's/:sharing_context/:sharing_code', array ( 'controller' => 'frontend', 'action' => 'default_view_shared_object', 'module' => 'system', ), array (
  0 => 'sharing_context',
  1 => 'sharing_code',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_avatar_remove', 'projects/templates/:template_id/avatar/remove', array ( 'controller' => 'project_template', 'action' => 'project_template/avatar_remove', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_avatar_edit', 'projects/templates/:template_id/avatar/edit', array ( 'controller' => 'project_template', 'action' => 'project_template/avatar_edit', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_avatar_upload', 'projects/templates/:template_id/avatar/upload', array ( 'controller' => 'project_template', 'action' => 'project_template/avatar_upload', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_avatar_view', 'projects/templates/:template_id/avatar/view', array ( 'controller' => 'project_template', 'action' => 'project_template/avatar_view', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/min-data$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_min_data', 'projects/templates/:template_id/min-data', array ( 'controller' => 'project_templates', 'action' => 'min_data', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/positions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_positions', 'projects/templates/:template_id/positions', array ( 'controller' => 'project_templates', 'action' => 'positions', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/upload-compatibility$/', $path, $matches)) {
      return Router::doMatch($path, 'project_file_template_upload_compatibility', 'projects/templates/:template_id/:object_type/upload-compatibility', array ( 'controller' => 'project_object_templates', 'action' => 'upload_compatibility', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/shortcuts$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_shortcuts', 'projects/templates/:template_id/shortcuts', array ( 'controller' => 'project_object_templates', 'action' => 'shortcuts', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/mass_edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_mass_edit', 'projects/templates/:template_id/mass_edit', array ( 'controller' => 'project_object_templates', 'action' => 'mass_edit', 0 => array ( 'parent_id' => '\\d+', 'subtask_id' => '\\d+', ), 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/(\\d+)\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_reorder', 'projects/templates/:template_id/:object_type/:object_id/reorder', array ( 'controller' => 'project_object_templates', 'action' => 'reorder', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/(\\d+)\\/subobjects$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_subobjects', 'projects/templates/:template_id/:object_type/:object_id/subobjects', array ( 'controller' => 'project_object_templates', 'action' => 'subobjects', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_delete', 'projects/templates/:template_id/:object_type/:object_id/delete', array ( 'controller' => 'project_object_templates', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_edit', 'projects/templates/:template_id/:object_type/:object_id/edit', array ( 'controller' => 'project_object_templates', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/files\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_file_add', 'projects/templates/:template_id/files/add', array ( 'controller' => 'project_object_templates', 'action' => 'files_add', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template_add', 'projects/templates/:template_id/:object_type/add', array ( 'controller' => 'project_object_templates', 'action' => 'add', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_template', 'projects/templates/:template_id/:object_type/:object_id', array ( 'controller' => 'project_object_templates', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'template_id',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_templates_reorder', 'projects/templates/reorder', array ( 'controller' => 'project_templates', 'action' => 'reorder', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_delete', 'projects/templates/:template_id/delete', array ( 'controller' => 'project_templates', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_edit', 'projects/templates/:template_id/edit', array ( 'controller' => 'project_templates', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_templates_add', 'projects/templates/add', array ( 'controller' => 'project_templates', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template_build', 'projects/templates/:template_id/build', array ( 'controller' => 'project_templates', 'action' => 'build', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_template', 'projects/templates/:template_id', array ( 'controller' => 'project_templates', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'template_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/templates$/', $path, $matches)) {
      return Router::doMatch($path, 'project_templates', 'projects/templates', array ( 'controller' => 'project_templates', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/check\\/(\\w+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_check', 'project-requests/check/:project_request_public_id', array ( 'controller' => 'project_requests_public', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'project_request_public_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/submit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_submit', 'project-requests/submit', array ( 'controller' => 'project_requests_public', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_unsubscribe_all', 'project-requests/:project_request_id/unsubscribe_all', array ( 'controller' => 'project_requests', 'action' => 'project_request_unsubscribe_all', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_unsubscribe', 'project-requests/:project_request_id/unsubscribe', array ( 'controller' => 'project_requests', 'action' => 'project_request_unsubscribe', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_subscribe', 'project-requests/:project_request_id/subscribe', array ( 'controller' => 'project_requests', 'action' => 'project_request_subscribe', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_subscriptions', 'project-requests/:project_request_id/subscriptions', array ( 'controller' => 'project_requests', 'action' => 'project_request_manage_subscriptions', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_untrash', 'project-requests/:project_request_id/comments/:comment_id/untrash', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_trash', 'project-requests/:project_request_id/comments/:comment_id/trash', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_state_trash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_unarchive', 'project-requests/:project_request_id/comments/:comment_id/unarchive', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_archive', 'project-requests/:project_request_id/comments/:comment_id/archive', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_state_archive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_untrash', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_trash', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_state_trash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_unarchive', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_archive', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_state_archive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_preview', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_preview_content', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_download', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_download_content', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_delete', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachment_state_delete', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment_edit', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_edit_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachment', 'project-requests/:project_request_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_view_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachments_add', 'project-requests/:project_request_id/comments/:comment_id/attachments/add', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_add_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_attachments', 'project-requests/:project_request_id/comments/:comment_id/attachments', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_attachments', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_delete', 'project-requests/:project_request_id/comments/:comment_id/delete', array ( 'controller' => 'project_requests', 'action' => 'project_request_comment_state_delete', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment_edit', 'project-requests/:project_request_id/comments/:comment_id/edit', array ( 'controller' => 'project_requests', 'action' => 'project_request_edit_comment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comment', 'project-requests/:project_request_id/comments/:comment_id', array ( 'controller' => 'project_requests', 'action' => 'project_request_view_comment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comments_unlock', 'project-requests/:project_request_id/comments/unlock', array ( 'controller' => 'project_requests', 'action' => 'project_request_comments_unlock', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comments_lock', 'project-requests/:project_request_id/comments/lock', array ( 'controller' => 'project_requests', 'action' => 'project_request_comments_lock', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comments_add', 'project-requests/:project_request_id/comments/add', array ( 'controller' => 'project_requests', 'action' => 'project_request_add_comment', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_comments', 'project-requests/:project_request_id/comments', array ( 'controller' => 'project_requests', 'action' => 'project_request_comments', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_untrash', 'project-requests/:project_request_id/attachments/:attachment_id/untrash', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_trash', 'project-requests/:project_request_id/attachments/:attachment_id/trash', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_state_trash', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_unarchive', 'project-requests/:project_request_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_archive', 'project-requests/:project_request_id/attachments/:attachment_id/archive', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_state_archive', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_preview', 'project-requests/:project_request_id/attachments/:attachment_id/preview', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_preview_content', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_download', 'project-requests/:project_request_id/attachments/:attachment_id/download', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_download_content', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_delete', 'project-requests/:project_request_id/attachments/:attachment_id/delete', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachment_state_delete', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment_edit', 'project-requests/:project_request_id/attachments/:attachment_id/edit', array ( 'controller' => 'project_requests', 'action' => 'project_request_edit_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachment', 'project-requests/:project_request_id/attachments/:attachment_id', array ( 'controller' => 'project_requests', 'action' => 'project_request_view_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachments_add', 'project-requests/:project_request_id/attachments/add', array ( 'controller' => 'project_requests', 'action' => 'project_request_add_attachment', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^project-requests\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_attachments', 'project-requests/:project_request_id/attachments', array ( 'controller' => 'project_requests', 'action' => 'project_request_attachments', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/save-client$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_save_client', 'projects/requests/:project_request_id/save-client', array ( 'controller' => 'project_requests', 'action' => 'save_client', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_delete', 'projects/requests/:project_request_id/delete', array ( 'controller' => 'project_requests', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/create-quote$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_create_quote', 'projects/requests/:project_request_id/create-quote', array ( 'controller' => 'project_requests', 'action' => 'create_quote', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/create-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_create_project', 'projects/requests/:project_request_id/create-project', array ( 'controller' => 'project_requests', 'action' => 'create_project', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/take$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_take', 'projects/requests/:project_request_id/take', array ( 'controller' => 'project_requests', 'action' => 'take', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/close$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_close', 'projects/requests/:project_request_id/close', array ( 'controller' => 'project_requests', 'action' => 'close', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/open$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_open', 'projects/requests/:project_request_id/open', array ( 'controller' => 'project_requests', 'action' => 'open', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request_edit', 'projects/requests/:project_request_id/edit', array ( 'controller' => 'project_requests', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_request', 'projects/requests/:project_request_id', array ( 'controller' => 'project_requests', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'project_request_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_requests_add', 'projects/requests/add', array ( 'controller' => 'project_requests', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_requests_mass_edit', 'projects/requests/mass-edit', array ( 'controller' => 'project_requests', 'action' => 'mass_edit', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_requests_archive', 'projects/requests/archive', array ( 'controller' => 'project_requests', 'action' => 'archive', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/requests$/', $path, $matches)) {
      return Router::doMatch($path, 'project_requests', 'projects/requests', array ( 'controller' => 'project_requests', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_report_delete', 'reports/workload/:workload_report_id/delete', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'delete', ), array (
  0 => 'workload_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_report_edit', 'reports/workload/:workload_report_id/edit', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'edit', ), array (
  0 => 'workload_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_report', 'reports/workload/:workload_report_id', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'view', ), array (
  0 => 'workload_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_reports_export', 'reports/workload/export', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_reports_run', 'reports/workload/run', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_reports_add', 'reports/workload/add', array ( 'controller' => 'workload_reports', 'module' => 'system', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/workload$/', $path, $matches)) {
      return Router::doMatch($path, 'workload_reports', 'reports/workload', array ( 'controller' => 'workload_reports', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filter_delete', 'reports/assignments/:assignment_filter_id/delete', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'delete', ), array (
  0 => 'assignment_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filter_edit', 'reports/assignments/:assignment_filter_id/edit', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'edit', ), array (
  0 => 'assignment_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filter', 'reports/assignments/:assignment_filter_id', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'view', ), array (
  0 => 'assignment_filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filters_export', 'reports/assignments/export', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filters_run', 'reports/assignments/run', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filters_add', 'reports/assignments/add', array ( 'controller' => 'assignment_filters', 'module' => 'system', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/assignments$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_filters', 'reports/assignments', array ( 'controller' => 'assignment_filters', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/objects\\/(\\d+)\\/update-milestone$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_update_milestone', 'projects/:project_slug/objects/:object_id/update-milestone', array ( 'controller' => 'milestones', 'action' => 'update_milestone', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/objects\\/(\\d+)\\/copy$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_copy', 'projects/:project_slug/objects/:object_id/copy', array ( 'controller' => 'project_objects', 'action' => 'copy', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/objects\\/(\\d+)\\/move$/', $path, $matches)) {
      return Router::doMatch($path, 'project_object_move', 'projects/:project_slug/objects/:object_id/move', array ( 'controller' => 'project_objects', 'action' => 'move', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/objects$/', $path, $matches)) {
      return Router::doMatch($path, 'project_objects', 'projects/:project_slug/objects', array ( 'controller' => 'projects', 'action' => 'overview', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/outline\\/mass_edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_outline_mass_edit', 'projects/:project_slug/outline/mass_edit', array ( 'controller' => 'project_outline', 'action' => 'mass_edit', 0 => array ( 'parent_id' => '\\d+', 'subtask_id' => '\\d+', ), 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/outline\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_outline_reorder', 'projects/:project_slug/outline/:object_type/:object_id/reorder', array ( 'controller' => 'project_outline', 'action' => 'reorder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/outline\\/([a-z0-9\\-\\._]+)\\/([a-z0-9\\-\\._]+)\\/subobjects$/', $path, $matches)) {
      return Router::doMatch($path, 'project_outline_subobjects', 'projects/:project_slug/outline/:object_type/:object_id/subobjects', array ( 'controller' => 'project_outline', 'action' => 'subobjects', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'object_type',
  2 => 'object_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/outline\\/shortcuts$/', $path, $matches)) {
      return Router::doMatch($path, 'project_outline_shortcuts', 'projects/:project_slug/outline/shortcuts', array ( 'controller' => 'project_outline', 'action' => 'shortcuts', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/outline$/', $path, $matches)) {
      return Router::doMatch($path, 'project_outline', 'projects/:project_slug/outline', array ( 'controller' => 'project_outline', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_untrash', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/untrash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_trash', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/trash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_unarchive', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_archive', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/archive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_preview', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/preview', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_preview_content', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_download', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/download', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_download_content', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_delete', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/delete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachment_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment_edit', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id/edit', array ( 'controller' => 'milestones', 'action' => 'project_milestone_edit_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachment', 'projects/:project_slug/milestones/:milestone_id/attachments/:attachment_id', array ( 'controller' => 'milestones', 'action' => 'project_milestone_view_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachments_add', 'projects/:project_slug/milestones/:milestone_id/attachments/add', array ( 'controller' => 'milestones', 'action' => 'project_milestone_add_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_attachments', 'projects/:project_slug/milestones/:milestone_id/attachments', array ( 'controller' => 'milestones', 'action' => 'project_milestone_attachments', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/([a-z0-9\\-\\._]+)\\/assignees$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_assignees', 'projects/:project_slug/milestones/:milestone_id/assignees', array ( 'controller' => 'milestones', 'action' => 'project_milestone_assignees', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminder_delete', 'projects/:project_slug/milestones/:milestone_id/reminders/:reminder_id/delete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_delete_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminder_dismiss', 'projects/:project_slug/milestones/:milestone_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'milestones', 'action' => 'project_milestone_dismiss_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminder_send', 'projects/:project_slug/milestones/:milestone_id/reminders/:reminder_id/send', array ( 'controller' => 'milestones', 'action' => 'project_milestone_send_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminder_edit', 'projects/:project_slug/milestones/:milestone_id/reminders/:reminder_id/edit', array ( 'controller' => 'milestones', 'action' => 'project_milestone_edit_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminder', 'projects/:project_slug/milestones/:milestone_id/reminders/:reminder_id', array ( 'controller' => 'milestones', 'action' => 'project_milestone_view_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminders_nudge', 'projects/:project_slug/milestones/:milestone_id/reminders/nudge', array ( 'controller' => 'milestones', 'action' => 'project_milestone_nudge_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminders_add', 'projects/:project_slug/milestones/:milestone_id/reminders/add', array ( 'controller' => 'milestones', 'action' => 'project_milestone_add_reminder', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reminders', 'projects/:project_slug/milestones/:milestone_id/reminders', array ( 'controller' => 'milestones', 'action' => 'project_milestone_reminders', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/unsubscribe_all$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_unsubscribe_all', 'projects/:project_slug/milestones/:milestone_id/unsubscribe_all', array ( 'controller' => 'milestones', 'action' => 'project_milestone_unsubscribe_all', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_unsubscribe', 'projects/:project_slug/milestones/:milestone_id/unsubscribe', array ( 'controller' => 'milestones', 'action' => 'project_milestone_unsubscribe', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_subscribe', 'projects/:project_slug/milestones/:milestone_id/subscribe', array ( 'controller' => 'milestones', 'action' => 'project_milestone_subscribe', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_subscriptions', 'projects/:project_slug/milestones/:milestone_id/subscriptions', array ( 'controller' => 'milestones', 'action' => 'project_milestone_manage_subscriptions', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_untrash', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/untrash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_trash', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/trash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_unarchive', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/unarchive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_archive', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/archive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_untrash', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/untrash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_trash', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/trash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_unarchive', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_archive', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/archive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_preview', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/preview', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_preview_content', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_download', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/download', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_download_content', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_delete', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/delete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachment_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment_edit', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id/edit', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_edit_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachment', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/:attachment_id', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_view_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
  3 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachments_add', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments/add', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_add_attachment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_attachments', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/attachments', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_attachments', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_delete', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/delete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comment_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment_edit', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id/edit', array ( 'controller' => 'milestones', 'action' => 'project_milestone_edit_comment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/([a-z0-9\\-\\._]+)\\/comments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comment', 'projects/:project_slug/milestones/:milestone_id/comments/:comment_id', array ( 'controller' => 'milestones', 'action' => 'project_milestone_view_comment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
  2 => 'comment_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comments_unlock', 'projects/:project_slug/milestones/:milestone_id/comments/unlock', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comments_unlock', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comments_lock', 'projects/:project_slug/milestones/:milestone_id/comments/lock', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comments_lock', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comments_add', 'projects/:project_slug/milestones/:milestone_id/comments/add', array ( 'controller' => 'milestones', 'action' => 'project_milestone_add_comment', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_delete', 'projects/:project_slug/milestones/:milestone_id/delete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_untrash', 'projects/:project_slug/milestones/:milestone_id/untrash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_trash', 'projects/:project_slug/milestones/:milestone_id/trash', array ( 'controller' => 'milestones', 'action' => 'project_milestone_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_unarchive', 'projects/:project_slug/milestones/:milestone_id/unarchive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_archive', 'projects/:project_slug/milestones/:milestone_id/archive', array ( 'controller' => 'milestones', 'action' => 'project_milestone_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/update-priority$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_update_priority', 'projects/:project_slug/milestones/:milestone_id/update-priority', array ( 'controller' => 'milestones', 'action' => 'project_milestone_update_priority', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/reopen$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_reopen', 'projects/:project_slug/milestones/:milestone_id/reopen', array ( 'controller' => 'milestones', 'action' => 'project_milestone_reopen', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/complete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_complete', 'projects/:project_slug/milestones/:milestone_id/complete', array ( 'controller' => 'milestones', 'action' => 'project_milestone_complete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_comments', 'projects/:project_slug/milestones/:milestone_id/comments', array ( 'controller' => 'milestones', 'action' => 'project_milestone_comments', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_edit', 'projects/:project_slug/milestones/:milestone_id/edit', array ( 'controller' => 'milestones', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone', 'projects/:project_slug/milestones/:milestone_id', array ( 'controller' => 'milestones', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'milestone_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestones_export', 'projects/:project_slug/milestones/export', array ( 'controller' => 'milestones', 'action' => 'export', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestones_add', 'projects/:project_slug/milestones/add', array ( 'controller' => 'milestones', 'action' => 'add', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestones_reorder', 'projects/:project_slug/milestones/reorder', array ( 'controller' => 'milestones', 'action' => 'reorder', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestones_archive', 'projects/:project_slug/milestones/archive', array ( 'controller' => 'milestones', 'action' => 'archive', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/milestones$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestones', 'projects/:project_slug/milestones', array ( 'controller' => 'milestones', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/people\\/(\\d+)\\/change-permissions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_user_permissions', 'projects/:project_slug/people/:user_id/change-permissions', array ( 'controller' => 'project_people', 'action' => 'user_permissions', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/people\\/(\\d+)\\/remove-from-project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_remove_user', 'projects/:project_slug/people/:user_id/remove-from-project', array ( 'controller' => 'project_people', 'action' => 'remove_user', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/people\\/(\\d+)\\/replace$/', $path, $matches)) {
      return Router::doMatch($path, 'project_replace_user', 'projects/:project_slug/people/:user_id/replace', array ( 'controller' => 'project_people', 'action' => 'replace_user', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/people\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_people_add', 'projects/:project_slug/people/add', array ( 'controller' => 'project_people', 'action' => 'add_people', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/people$/', $path, $matches)) {
      return Router::doMatch($path, 'project_people', 'projects/:project_slug/people', array ( 'controller' => 'project_people', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'project_settings', 'projects/:project_slug/settings', array ( 'controller' => 'project', 'action' => 'settings', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/icon\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_delete_icon', 'projects/:project_slug/icon/delete', array ( 'controller' => 'project_icon', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/icon\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_edit_icon', 'projects/:project_slug/icon/edit', array ( 'controller' => 'project_icon', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/sync-unlock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_sync_unlock', 'projects/:project_slug/sync-unlock', array ( 'controller' => 'project', 'action' => 'sync_unlock', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/sync-lock$/', $path, $matches)) {
      return Router::doMatch($path, 'project_sync_lock', 'projects/:project_slug/sync-lock', array ( 'controller' => 'project', 'action' => 'sync_lock', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/export-as-file$/', $path, $matches)) {
      return Router::doMatch($path, 'project_export_as_file', 'projects/:project_slug/export-as-file', array ( 'controller' => 'project', 'action' => 'export_as_file', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'project_export', 'projects/:project_slug/export', array ( 'controller' => 'project', 'action' => 'export', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/unpin$/', $path, $matches)) {
      return Router::doMatch($path, 'project_unpin', 'projects/:project_slug/unpin', array ( 'controller' => 'project', 'action' => 'unpin', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/pin$/', $path, $matches)) {
      return Router::doMatch($path, 'project_pin', 'projects/:project_slug/pin', array ( 'controller' => 'project', 'action' => 'pin', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/activity-log\\/rss$/', $path, $matches)) {
      return Router::doMatch($path, 'project_activity_log_rss', 'projects/:project_slug/activity-log/rss', array ( 'controller' => 'project', 'action' => 'project_activity_log_rss', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/activity-log$/', $path, $matches)) {
      return Router::doMatch($path, 'project_activity_log', 'projects/:project_slug/activity-log', array ( 'controller' => 'project', 'action' => 'project_activity_log', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_edit', 'projects/:project_slug/edit', array ( 'controller' => 'project', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/ical-subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_ical_subscribe', 'projects/:project_slug/ical-subscribe', array ( 'controller' => 'project', 'action' => 'ical_subscribe', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/ical$/', $path, $matches)) {
      return Router::doMatch($path, 'project_ical', 'projects/:project_slug/ical', array ( 'controller' => 'project', 'action' => 'ical', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/subtasks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_subtasks', 'projects/:project_slug/subtasks', array ( 'controller' => 'project', 'action' => 'subtasks', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_attachments', 'projects/:project_slug/attachments', array ( 'controller' => 'project', 'action' => 'attachments', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/comments$/', $path, $matches)) {
      return Router::doMatch($path, 'project_comments', 'projects/:project_slug/comments', array ( 'controller' => 'project', 'action' => 'comments', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/user-subscriptions-mass-unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'project_user_subscriptions_mass_unsubscribe', 'projects/:project_slug/user-subscriptions-mass-unsubscribe', array ( 'controller' => 'project', 'action' => 'user_subscriptions_mass_unsubscribe', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/user-subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'project_user_subscriptions', 'projects/:project_slug/user-subscriptions', array ( 'controller' => 'project', 'action' => 'user_subscriptions', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/user-tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'project_user_tasks', 'projects/:project_slug/user-tasks', array ( 'controller' => 'project', 'action' => 'user_tasks', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^milestone\\/invoice\\/preview-items$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_invoicing_preview_items', 'milestone/invoice/preview-items', array ( 'controller' => 'milestones', 'action' => 'project_milestone_preview_items', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^milestone\\/invoice\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_milestone_invoicing', 'milestone/invoice/add', array ( 'controller' => 'milestones', 'action' => 'project_milestone_add_invoice', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^project\\/(([a-z0-9\\-\\._]+))\\/invoice\\/preview-items$/', $path, $matches)) {
      return Router::doMatch($path, 'project_invoicing_preview_items', 'project/:project_slug/invoice/preview-items', array ( 'controller' => 'project', 'action' => 'project_preview_items', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^project\\/(([a-z0-9\\-\\._]+))\\/invoice\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_invoicing', 'project/:project_slug/invoice/add', array ( 'controller' => 'project', 'action' => 'project_add_invoice', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_delete', 'projects/:project_slug/tracking/expenses/:expense_id/delete', array ( 'controller' => 'project', 'action' => 'project_tracking_expense_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_untrash', 'projects/:project_slug/tracking/expenses/:expense_id/untrash', array ( 'controller' => 'project', 'action' => 'project_tracking_expense_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_trash', 'projects/:project_slug/tracking/expenses/:expense_id/trash', array ( 'controller' => 'project', 'action' => 'project_tracking_expense_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_unarchive', 'projects/:project_slug/tracking/expenses/:expense_id/unarchive', array ( 'controller' => 'project', 'action' => 'project_tracking_expense_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_archive', 'projects/:project_slug/tracking/expenses/:expense_id/archive', array ( 'controller' => 'project', 'action' => 'project_tracking_expense_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense_edit', 'projects/:project_slug/tracking/expenses/:expense_id/edit', array ( 'controller' => 'project', 'action' => 'project_edit_expense', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expense', 'projects/:project_slug/tracking/expenses/:expense_id', array ( 'controller' => 'project', 'action' => 'project_view_expense', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'expense_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/expenses\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_expenses_add', 'projects/:project_slug/tracking/expenses/add', array ( 'controller' => 'project', 'action' => 'project_add_expense', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_delete', 'projects/:project_slug/tracking/time/:time_record_id/delete', array ( 'controller' => 'project', 'action' => 'project_tracking_time_record_state_delete', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_untrash', 'projects/:project_slug/tracking/time/:time_record_id/untrash', array ( 'controller' => 'project', 'action' => 'project_tracking_time_record_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_trash', 'projects/:project_slug/tracking/time/:time_record_id/trash', array ( 'controller' => 'project', 'action' => 'project_tracking_time_record_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_unarchive', 'projects/:project_slug/tracking/time/:time_record_id/unarchive', array ( 'controller' => 'project', 'action' => 'project_tracking_time_record_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_archive', 'projects/:project_slug/tracking/time/:time_record_id/archive', array ( 'controller' => 'project', 'action' => 'project_tracking_time_record_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record_edit', 'projects/:project_slug/tracking/time/:time_record_id/edit', array ( 'controller' => 'project', 'action' => 'project_edit_time_record', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_record', 'projects/:project_slug/tracking/time/:time_record_id', array ( 'controller' => 'project', 'action' => 'project_view_time_record', 'module' => 'system', ), array (
  0 => 'project_slug',
  1 => 'time_record_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/time\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_time_records_add', 'projects/:project_slug/tracking/time/add', array ( 'controller' => 'project', 'action' => 'project_add_time_record', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/estimates\\/set$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_estimate_set', 'projects/:project_slug/tracking/estimates/set', array ( 'controller' => 'project', 'action' => 'project_object_tracking_estimate_set', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking\\/estimates$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking_estimates', 'projects/:project_slug/tracking/estimates', array ( 'controller' => 'project', 'action' => 'project_object_tracking_estimates', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/tracking$/', $path, $matches)) {
      return Router::doMatch($path, 'project_tracking', 'projects/:project_slug/tracking', array ( 'controller' => 'project_tracking', 'action' => 'log', 'module' => 'tracking', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/update-label$/', $path, $matches)) {
      return Router::doMatch($path, 'project_update_label', 'projects/:project_slug/update-label', array ( 'controller' => 'project', 'action' => 'project_update_label', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_delete', 'projects/:project_slug/delete', array ( 'controller' => 'project', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_untrash', 'projects/:project_slug/untrash', array ( 'controller' => 'project', 'action' => 'project_state_untrash', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'project_trash', 'projects/:project_slug/trash', array ( 'controller' => 'project', 'action' => 'project_state_trash', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_unarchive', 'projects/:project_slug/unarchive', array ( 'controller' => 'project', 'action' => 'project_state_unarchive', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'project_archive', 'projects/:project_slug/archive', array ( 'controller' => 'project', 'action' => 'project_state_archive', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/update-category$/', $path, $matches)) {
      return Router::doMatch($path, 'project_update_category', 'projects/:project_slug/update-category', array ( 'controller' => 'project', 'action' => 'project_update_category', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/categories\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_category_delete', 'projects/categories/:category_id/delete', array ( 'controller' => 'projects', 'action' => 'project_delete_category', 'module' => 'system', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/categories\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_category_edit', 'projects/categories/:category_id/edit', array ( 'controller' => 'projects', 'action' => 'project_edit_category', 'module' => 'system', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/categories\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_category', 'projects/categories/:category_id', array ( 'controller' => 'projects', 'action' => 'project_view_category', 'module' => 'system', ), array (
  0 => 'category_id',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/categories\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'project_categories_add', 'projects/categories/add', array ( 'controller' => 'projects', 'action' => 'project_add_category', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/categories$/', $path, $matches)) {
      return Router::doMatch($path, 'project_categories', 'projects/categories', array ( 'controller' => 'projects', 'action' => 'project_categories', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/labels\\/project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_labels', 'info/labels/project', array ( 'controller' => 'projects', 'action' => 'labels', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/what-to-sync$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_what_to_sync', 'projects/what-to-sync', array ( 'controller' => 'projects', 'action' => 'what_to_sync', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_archive', 'projects/archive', array ( 'controller' => 'projects', 'action' => 'archive', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_add', 'projects/add', array ( 'controller' => 'project', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^project\\/([a-z0-9\\-\\._]+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'project_avatar_remove', 'project/:project_slug/avatar/remove', array ( 'controller' => 'project', 'action' => 'project/avatar_remove', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^project\\/([a-z0-9\\-\\._]+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'project_avatar_edit', 'project/:project_slug/avatar/edit', array ( 'controller' => 'project', 'action' => 'project/avatar_edit', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^project\\/([a-z0-9\\-\\._]+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'project_avatar_upload', 'project/:project_slug/avatar/upload', array ( 'controller' => 'project', 'action' => 'project/avatar_upload', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^project\\/([a-z0-9\\-\\._]+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'project_avatar_view', 'project/:project_slug/avatar/view', array ( 'controller' => 'project', 'action' => 'project/avatar_view', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/reopen$/', $path, $matches)) {
      return Router::doMatch($path, 'project_reopen', 'projects/:project_slug/reopen', array ( 'controller' => 'project', 'action' => 'project_reopen', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)\\/complete$/', $path, $matches)) {
      return Router::doMatch($path, 'project_complete', 'projects/:project_slug/complete', array ( 'controller' => 'project', 'action' => 'project_complete', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/(([a-z0-9\\-\\._]+))\\/m2p\\/learn_more$/', $path, $matches)) {
      return Router::doMatch($path, 'project_mail_to_project_learn_more', 'projects/:project_slug/m2p/learn_more', array ( 'controller' => 'project', 'action' => 'mail_to_project_learn_more', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project', 'projects/:project_slug', array ( 'controller' => 'project', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'project_slug',
), $matches, $query_string);
    } elseif(preg_match('/^projects\\/multiple\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'projects_mass_edit', 'projects/multiple/mass-edit', array ( 'controller' => 'projects', 'action' => 'mass_edit', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^projects$/', $path, $matches)) {
      return Router::doMatch($path, 'projects', 'projects', array ( 'controller' => 'projects', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/add-to-projects$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_add_to_projects', 'people/:company_id/users/:user_id/add-to-projects', array ( 'controller' => 'user_projects', 'action' => 'add_to_projects', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/projects\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_projects_archive', 'people/:company_id/users/:user_id/projects/archive', array ( 'controller' => 'user_projects', 'action' => 'archive', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/projects$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_projects', 'people/:company_id/users/:user_id/projects', array ( 'controller' => 'user_projects', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/activity-log\\/rss$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_activity_log_rss', 'people/:company_id/users/:user_id/activity-log/rss', array ( 'controller' => 'users', 'action' => 'people_company_user_activity_log_rss', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/activity-log$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_activity_log', 'people/:company_id/users/:user_id/activity-log', array ( 'controller' => 'users', 'action' => 'people_company_user_activity_log', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_reminders', 'people/:company_id/users/:user_id/reminders', array ( 'controller' => 'users', 'action' => 'people_company_user_user_reminders', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_widget_delete', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/delete', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_widget_delete', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
  3 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_widget_edit', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/edit', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_widget_edit', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
  3 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_widget', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_widget', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
  3 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_widgets_reorder', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/reorder', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_widgets_reorder', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_widgets_add', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/add', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_widgets_add', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_delete', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/delete', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_tab_delete', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab_edit', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id/edit', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_tab_edit', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tab', 'people/:company_id/users/:user_id/homescreen/tabs/:homescreen_tab_id', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_tab', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tabs_reorder', 'people/:company_id/users/:user_id/homescreen/tabs/reorder', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_tabs_reorder', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen\\/tabs\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen_tabs_add', 'people/:company_id/users/:user_id/homescreen/tabs/add', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen_tabs_add', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/homescreen$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_homescreen', 'people/:company_id/users/:user_id/homescreen', array ( 'controller' => 'users', 'action' => 'people_company_user_homescreen', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_avatar_remove', 'people/:company_id/users/:user_id/avatar/remove', array ( 'controller' => 'users', 'action' => 'people_company_user/avatar_remove', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_avatar_edit', 'people/:company_id/users/:user_id/avatar/edit', array ( 'controller' => 'users', 'action' => 'people_company_user/avatar_edit', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_avatar_upload', 'people/:company_id/users/:user_id/avatar/upload', array ( 'controller' => 'users', 'action' => 'people_company_user/avatar_upload', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_avatar_view', 'people/:company_id/users/:user_id/avatar/view', array ( 'controller' => 'users', 'action' => 'people_company_user/avatar_view', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_untrash', 'people/:company_id/users/:user_id/untrash', array ( 'controller' => 'users', 'action' => 'people_company_user_state_untrash', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_trash', 'people/:company_id/users/:user_id/trash', array ( 'controller' => 'users', 'action' => 'people_company_user_state_trash', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_unarchive', 'people/:company_id/users/:user_id/unarchive', array ( 'controller' => 'users', 'action' => 'people_company_user_state_unarchive', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_archive', 'people/:company_id/users/:user_id/archive', array ( 'controller' => 'users', 'action' => 'people_company_user_state_archive', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscription_delete', 'people/:company_id/users/:user_id/api-subscriptions/:api_client_subscription_id/delete', array ( 'controller' => 'users', 'action' => 'people_company_user_delete_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscription_edit', 'people/:company_id/users/:user_id/api-subscriptions/:api_client_subscription_id/edit', array ( 'controller' => 'users', 'action' => 'people_company_user_edit_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscription_disable', 'people/:company_id/users/:user_id/api-subscriptions/:api_client_subscription_id/disable', array ( 'controller' => 'users', 'action' => 'people_company_user_disable_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscription_enable', 'people/:company_id/users/:user_id/api-subscriptions/:api_client_subscription_id/enable', array ( 'controller' => 'users', 'action' => 'people_company_user_enable_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscription', 'people/:company_id/users/:user_id/api-subscriptions/:api_client_subscription_id', array ( 'controller' => 'users', 'action' => 'people_company_user_view_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
  2 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscriptions_add', 'people/:company_id/users/:user_id/api-subscriptions/add', array ( 'controller' => 'users', 'action' => 'people_company_user_add_api_client_subscription', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/api-subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_api_client_subscriptions', 'people/:company_id/users/:user_id/api-subscriptions', array ( 'controller' => 'users', 'action' => 'people_company_user_api_client_subscriptions', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/favorites\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_remove_from_favorites', 'people/:company_id/users/:user_id/favorites/remove', array ( 'controller' => 'favorites', 'action' => 'remove_from_favorites', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/favorites\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_add_to_favorites', 'people/:company_id/users/:user_id/favorites/add', array ( 'controller' => 'favorites', 'action' => 'add_to_favorites', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/([a-z0-9\\-\\._]+)\\/users\\/(\\d+)\\/favorites$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_favorites', 'people/:company_id/users/:user_id/favorites', array ( 'controller' => 'favorites', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/login-as$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_login_as', 'people/:company_id/users/:user_id/login-as', array ( 'controller' => 'users', 'action' => 'login_as', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/set-as-invited$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_set_as_invited', 'people/:company_id/users/:user_id/set-as-invited', array ( 'controller' => 'users', 'action' => 'set_as_invited', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/send-welcome-message$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_send_welcome_message', 'people/:company_id/users/:user_id/send-welcome-message', array ( 'controller' => 'users', 'action' => 'send_welcome_message', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/recent-activities$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_recent_activities', 'people/:company_id/users/:user_id/recent-activities', array ( 'controller' => 'users', 'action' => 'recent_activities', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/edit-password$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_edit_password', 'people/:company_id/users/:user_id/edit-password', array ( 'controller' => 'users', 'action' => 'edit_password', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_delete', 'people/:company_id/users/:user_id/delete', array ( 'controller' => 'users', 'action' => 'people_company_user_state_delete', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/export-vcard$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_export_vcard', 'people/:company_id/users/:user_id/export-vcard', array ( 'controller' => 'users', 'action' => 'export_vcard', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/edit-company-and-role$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_edit_company_and_role', 'people/:company_id/users/:user_id/edit-company-and-role', array ( 'controller' => 'users', 'action' => 'edit_company_and_role', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/edit-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_edit_settings', 'people/:company_id/users/:user_id/edit-settings', array ( 'controller' => 'users', 'action' => 'edit_settings', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/edit-profile$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_edit_profile', 'people/:company_id/users/:user_id/edit-profile', array ( 'controller' => 'users', 'action' => 'edit_profile', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_edit', 'people/:company_id/users/:user_id/edit', array ( 'controller' => 'users', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/add-user$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user_add', 'people/:company_id/add-user', array ( 'controller' => 'users', 'action' => 'add', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_user', 'people/:company_id/users/:user_id', array ( 'controller' => 'users', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'company_id',
  1 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/with-permissions$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_users_with_permissions', 'people/:company_id/users/with-permissions', array ( 'controller' => 'users', 'action' => 'users_with_permissions', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_users_archive', 'people/:company_id/users/archive', array ( 'controller' => 'users', 'action' => 'archive', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/users$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_users', 'people/:company_id/users', array ( 'controller' => 'users', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/project-requests$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_project_requests', 'people/:company_id/project-requests', array ( 'controller' => 'company_project_requests', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/projects\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_projects_archive', 'people/:company_id/projects/archive', array ( 'controller' => 'company_projects', 'action' => 'archive', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/projects$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_projects', 'people/:company_id/projects', array ( 'controller' => 'company_projects', 'action' => 'index', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_avatar_remove', 'people/:company_id/avatar/remove', array ( 'controller' => 'companies', 'action' => 'people_company/avatar_remove', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_avatar_edit', 'people/:company_id/avatar/edit', array ( 'controller' => 'companies', 'action' => 'people_company/avatar_edit', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_avatar_upload', 'people/:company_id/avatar/upload', array ( 'controller' => 'companies', 'action' => 'people_company/avatar_upload', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_avatar_view', 'people/:company_id/avatar/view', array ( 'controller' => 'companies', 'action' => 'people_company/avatar_view', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_untrash', 'people/:company_id/untrash', array ( 'controller' => 'companies', 'action' => 'people_company_state_untrash', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_trash', 'people/:company_id/trash', array ( 'controller' => 'companies', 'action' => 'people_company_state_trash', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_unarchive', 'people/:company_id/unarchive', array ( 'controller' => 'companies', 'action' => 'people_company_state_unarchive', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_archive', 'people/:company_id/archive', array ( 'controller' => 'companies', 'action' => 'people_company_state_archive', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/delete-logo$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_delete_logo', 'people/:company_id/delete-logo', array ( 'controller' => 'companies', 'action' => 'delete_logo', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/edit-logo$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_edit_logo', 'people/:company_id/edit-logo', array ( 'controller' => 'companies', 'action' => 'edit_logo', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_delete', 'people/:company_id/delete', array ( 'controller' => 'companies', 'action' => 'people_company_state_delete', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/export-vcard$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_export_vcard', 'people/:company_id/export-vcard', array ( 'controller' => 'companies', 'action' => 'export_vcard', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_edit', 'people/:company_id/edit', array ( 'controller' => 'companies', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/company_details$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company_details', 'people/company_details', array ( 'controller' => 'companies', 'action' => 'company_details', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'people_company', 'people/:company_id', array ( 'controller' => 'companies', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'company_id',
), $matches, $query_string);
    } elseif(preg_match('/^people\\/add-company$/', $path, $matches)) {
      return Router::doMatch($path, 'people_companies_add', 'people/add-company', array ( 'controller' => 'companies', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'people_mass_edit', 'people/mass-edit', array ( 'controller' => 'people', 'action' => 'mass_edit', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/archive\\/printable$/', $path, $matches)) {
      return Router::doMatch($path, 'people_archive_printable', 'people/archive/printable', array ( 'controller' => 'people', 'action' => 'archive_printable', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'people_archive', 'people/archive', array ( 'controller' => 'people', 'action' => 'archive', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/invite$/', $path, $matches)) {
      return Router::doMatch($path, 'people_invite', 'people/invite', array ( 'controller' => 'people', 'action' => 'invite', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/import-vcard$/', $path, $matches)) {
      return Router::doMatch($path, 'people_import_vcard', 'people/import-vcard', array ( 'controller' => 'people', 'action' => 'import_vcard', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people\\/printable$/', $path, $matches)) {
      return Router::doMatch($path, 'people_printable', 'people/printable', array ( 'controller' => 'people', 'action' => 'index_printable', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^people$/', $path, $matches)) {
      return Router::doMatch($path, 'people', 'people', array ( 'controller' => 'people', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^search\\/quick$/', $path, $matches)) {
      return Router::doMatch($path, 'quick_backend_search', 'search/quick', array ( 'controller' => 'backend_search', 'action' => 'quick_search', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^custom-tab\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'custom_tab', 'custom-tab/:homescreen_tab_id', array ( 'controller' => 'backend', 'action' => 'custom_tab', 'module' => 'system', ), array (
  0 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^whats-new$/', $path, $matches)) {
      return Router::doMatch($path, 'whats_new', 'whats-new', array ( 'controller' => 'backend', 'action' => 'whats_new', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^my-tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'my_tasks', 'my-tasks', array ( 'controller' => 'backend', 'action' => 'my_tasks', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/roles\\/project\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'project_role_info', 'info/roles/project/:role_id', array ( 'controller' => 'roles_info', 'action' => 'project_role', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^info\\/roles\\/project$/', $path, $matches)) {
      return Router::doMatch($path, 'project_roles_info', 'info/roles/project', array ( 'controller' => 'roles_info', 'action' => 'project_roles', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/save-license-details$/', $path, $matches)) {
      return Router::doMatch($path, 'save_license_details', 'admin/save-license-details', array ( 'controller' => 'update', 'action' => 'save_license_details', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/new-version$/', $path, $matches)) {
      return Router::doMatch($path, 'new_version_details', 'admin/new-version', array ( 'controller' => 'update', 'action' => 'check_for_new_version', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/install-new-modules$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update_install_new_modules', 'admin/update/install-new-modules', array ( 'controller' => 'update', 'action' => 'install_new_modules', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/get-upgrade-steps$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update_get_upgrade_steps', 'admin/update/get-upgrade-steps', array ( 'controller' => 'update', 'action' => 'get_upgrade_steps', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/unpack-update-package$/', $path, $matches)) {
      return Router::doMatch($path, 'application_unpack_download_package', 'admin/update/unpack-update-package', array ( 'controller' => 'update', 'action' => 'unpack_update_package', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/check-download-progress$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update_check_download_progress', 'admin/update/check-download-progress', array ( 'controller' => 'update', 'action' => 'check_download_progress', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/download-update-package$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update_download_package', 'admin/update/download-update-package', array ( 'controller' => 'update', 'action' => 'download_update_package', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update\\/check-password$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update_check_password', 'admin/update/check-password', array ( 'controller' => 'update', 'action' => 'check_password', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/update$/', $path, $matches)) {
      return Router::doMatch($path, 'application_update', 'admin/update', array ( 'controller' => 'update', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^data-sources\\/(\\d+)\\/validate$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_validate_before_import', 'data-sources/:data_source_id/validate', array ( 'controller' => 'data_sources', 'action' => 'validate_import', 'module' => 'data_sources', ), array (
  0 => 'data_source_id',
), $matches, $query_string);
    } elseif(preg_match('/^data-sources\\/(\\d+)\\/import$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_import', 'data-sources/:data_source_id/import', array ( 'controller' => 'data_sources', 'action' => 'import', 'module' => 'data_sources', ), array (
  0 => 'data_source_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources\\/test-connection$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_test_connection', 'admin/data-sources/test-connection', array ( 'controller' => 'data_sources_admin', 'action' => 'test_connection', 'module' => 'data_sources', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_delete', 'admin/data-sources/:data_source_id/delete', array ( 'controller' => 'data_sources_admin', 'action' => 'delete', 'module' => 'data_sources', ), array (
  0 => 'data_source_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources\\/(\\d+)\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source', 'admin/data-sources/:data_source_id/view', array ( 'controller' => 'data_sources_admin', 'action' => 'view', 'module' => 'data_sources', ), array (
  0 => 'data_source_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_edit', 'admin/data-sources/:data_source_id/edit', array ( 'controller' => 'data_sources_admin', 'action' => 'edit', 'module' => 'data_sources', ), array (
  0 => 'data_source_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'data_source_add', 'admin/data-sources/add', array ( 'controller' => 'data_sources_admin', 'action' => 'add', 'module' => 'data_sources', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/data-sources$/', $path, $matches)) {
      return Router::doMatch($path, 'data_sources', 'admin/data-sources', array ( 'controller' => 'data_sources_admin', 'module' => 'data_sources', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/events\\/([a-z0-9\\-\\._]+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_untrash', 'calendar/:calendar_id/events/:calendar_event_id/untrash', array ( 'controller' => 'calendar_events', 'action' => 'calendar_event_state_untrash', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/events\\/([a-z0-9\\-\\._]+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_trash', 'calendar/:calendar_id/events/:calendar_event_id/trash', array ( 'controller' => 'calendar_events', 'action' => 'calendar_event_state_trash', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/events\\/([a-z0-9\\-\\._]+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_unarchive', 'calendar/:calendar_id/events/:calendar_event_id/unarchive', array ( 'controller' => 'calendar_events', 'action' => 'calendar_event_state_unarchive', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/events\\/([a-z0-9\\-\\._]+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_archive', 'calendar/:calendar_id/events/:calendar_event_id/archive', array ( 'controller' => 'calendar_events', 'action' => 'calendar_event_state_archive', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_untrash', 'calendar/:calendar_id/untrash', array ( 'controller' => 'calendars', 'action' => 'calendar_state_untrash', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_trash', 'calendar/:calendar_id/trash', array ( 'controller' => 'calendars', 'action' => 'calendar_state_trash', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_unarchive', 'calendar/:calendar_id/unarchive', array ( 'controller' => 'calendars', 'action' => 'calendar_state_unarchive', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_archive', 'calendar/:calendar_id/archive', array ( 'controller' => 'calendars', 'action' => 'calendar_state_archive', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/events\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_delete', 'calendar/:calendar_id/events/:calendar_event_id/delete', array ( 'controller' => 'calendar_events', 'action' => 'calendar_event_state_delete', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/events\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event_edit', 'calendars/:calendar_id/events/:calendar_event_id/edit', array ( 'controller' => 'calendar_events', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/events\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_event', 'calendars/:calendar_id/events/:calendar_event_id', array ( 'controller' => 'calendar_events', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'calendar_id',
  1 => 'calendar_event_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/events\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_events_add', 'calendars/:calendar_id/events/add', array ( 'controller' => 'calendar_events', 'action' => 'add', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/events$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_events', 'calendars/:calendar_id/events', array ( 'controller' => 'calendar_events', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/events\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'events_add', 'calendars/events/add', array ( 'controller' => 'calendars', 'action' => 'add_event', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\w+)\\/(\\d+)\\/change-color$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_change_color_by_type', 'calendars/:type/:type_id/change-color', array ( 'controller' => 'calendars', 'action' => 'change_color', 'module' => 'system', ), array (
  0 => 'type',
  1 => 'type_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/change-color$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_change_color', 'calendars/:calendar_id/change-color', array ( 'controller' => 'calendars', 'action' => 'change_color', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/mass-change-visibility$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_mass_change_visibility', 'calendars/mass-change-visibility', array ( 'controller' => 'calendars', 'action' => 'mass_change_visibility', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\w+)\\/(\\d+)\\/change-visibility$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_change_visibility_by_type', 'calendars/:type/:type_id/change-visibility', array ( 'controller' => 'calendars', 'action' => 'change_visibility', 'module' => 'system', ), array (
  0 => 'type',
  1 => 'type_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/change-visibility$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_change_visibility', 'calendars/:calendar_id/change-visibility', array ( 'controller' => 'calendars', 'action' => 'change_visibility', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendar\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_delete', 'calendar/:calendar_id/delete', array ( 'controller' => 'calendars', 'action' => 'calendar_state_delete', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_edit', 'calendars/:calendar_id/edit', array ( 'controller' => 'calendars', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar', 'calendars/:calendar_id', array ( 'controller' => 'calendars', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/ical-subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_ical_subscribe', 'calendars/:calendar_id/ical-subscribe', array ( 'controller' => 'calendars', 'action' => 'ical_subscribe', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/(\\d+)\\/ical$/', $path, $matches)) {
      return Router::doMatch($path, 'calendar_ical', 'calendars/:calendar_id/ical', array ( 'controller' => 'calendars', 'action' => 'ical', 'module' => 'system', ), array (
  0 => 'calendar_id',
), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars_add', 'calendars/add', array ( 'controller' => 'calendars', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/sidebar$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars_sidebar_toggle', 'calendars/sidebar', array ( 'controller' => 'calendars', 'action' => 'sidebar', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/import\\/file$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars_import_file', 'calendars/import/file', array ( 'controller' => 'calendars', 'action' => 'import_file', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/import\\/feed$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars_import_feed', 'calendars/import/feed', array ( 'controller' => 'calendars', 'action' => 'import_feed', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^calendars\\/monthly\\/(\\w+)\\/(\\w+)$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars_monthly', 'calendars/monthly/:year/:month', array ( 'controller' => 'calendars', 'module' => 'system', ), array (
  0 => 'year',
  1 => 'month',
), $matches, $query_string);
    } elseif(preg_match('/^calendars$/', $path, $matches)) {
      return Router::doMatch($path, 'calendars', 'calendars', array ( 'controller' => 'calendars', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/search\\/([a-z0-9\\-\\._]+)\\/reinit$/', $path, $matches)) {
      return Router::doMatch($path, 'search_index_admin_reinit', 'admin/indices/search/:search_index_name/reinit', array ( 'controller' => 'search_index_admin', 'action' => 'reinit', 'module' => 'system', ), array (
  0 => 'search_index_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/search\\/([a-z0-9\\-\\._]+)\\/rebuild$/', $path, $matches)) {
      return Router::doMatch($path, 'search_index_admin_rebuild', 'admin/indices/search/:search_index_name/rebuild', array ( 'controller' => 'search_index_admin', 'action' => 'rebuild', 'module' => 'system', ), array (
  0 => 'search_index_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'search_settings', 'admin/search-settings', array ( 'controller' => 'search_settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^backend\\/search$/', $path, $matches)) {
      return Router::doMatch($path, 'backend_search', 'backend/search', array ( 'controller' => 'backend_search', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'reminders', 'reminders', array ( 'controller' => 'reminders', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^compare-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'compare_versions', '/compare-versions', array ( 'controller' => 'text_compare', 'action' => 'compare_versions', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^compare-text$/', $path, $matches)) {
      return Router::doMatch($path, 'compare_text', '/compare-text', array ( 'controller' => 'text_compare', 'action' => 'compare_text', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_report_delete', 'reports/payments/summary/:payments_summary_report_id/delete', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'delete', ), array (
  0 => 'payments_summary_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_report_edit', 'reports/payments/summary/:payments_summary_report_id/edit', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'edit', ), array (
  0 => 'payments_summary_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_report', 'reports/payments/summary/:payments_summary_report_id', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'view', ), array (
  0 => 'payments_summary_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_reports_export', 'reports/payments/summary/export', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_reports_run', 'reports/payments/summary/run', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_reports_add', 'reports/payments/summary/add', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/summary$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_summary_reports', 'reports/payments/summary', array ( 'controller' => 'payments_summary_reports', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_report_delete', 'reports/payments/report/:payments_report_id/delete', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'delete', ), array (
  0 => 'payments_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_report_edit', 'reports/payments/report/:payments_report_id/edit', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'edit', ), array (
  0 => 'payments_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_report', 'reports/payments/report/:payments_report_id', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'view', ), array (
  0 => 'payments_report_id',
), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_reports_export', 'reports/payments/report/export', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'export', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/run$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_reports_run', 'reports/payments/report/run', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'run', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_reports_add', 'reports/payments/report/add', array ( 'controller' => 'payments_reports', 'module' => 'system', 'action' => 'add', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports\\/payments\\/report$/', $path, $matches)) {
      return Router::doMatch($path, 'payments_reports', 'reports/payments/report', array ( 'controller' => 'payments_reports', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoices\\/payments\\/methods\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_methods_settings', 'admin/invoices/payments/methods/settings', array ( 'controller' => 'payment_gateways_admin', 'action' => 'methods', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_enable', 'payment/gateways/:payment_gateway_id/enable', array ( 'controller' => 'payment_gateways_admin', 'action' => 'enable', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_disable', 'payment/gateways/:payment_gateway_id/disable', array ( 'controller' => 'payment_gateways_admin', 'action' => 'disable', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_gateway_add', 'payment/gateways/add', array ( 'controller' => 'payment_gateways_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_gateway_delete', 'payment/gateways/:payment_gateway_id/delete', array ( 'controller' => 'payment_gateways_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/set_as_default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_set_as_default', 'payment/gateways/:payment_gateway_id/set_as_default', array ( 'controller' => 'payment_gateways_admin', 'action' => 'set_as_default', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_gateway_view', 'payment/gateways/:payment_gateway_id/view', array ( 'controller' => 'payment_gateways_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_payment_gateway_edit', 'payment/gateways/:payment_gateway_id/edit', array ( 'controller' => 'payment_gateways_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'payment_gateway_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoices\\/payments\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_gateways_settings', 'admin/invoices/payments/settings', array ( 'controller' => 'payment_gateways_admin', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/invoices\\/payments\\/enforce$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_gateways_enforce_settings', 'admin/invoices/payments/enforce', array ( 'controller' => 'payment_gateways_admin', 'action' => 'enforce_settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/allow\\/invoices\\/payments\\/change$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_gateways_allow_payments_for_invoice', 'admin/allow/invoices/payments/change', array ( 'controller' => 'payment_gateways_admin', 'action' => 'allow_payments_for_invoice', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/allow\\/payments\\/change$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_gateways_allow_payments', 'admin/allow/payments/change', array ( 'controller' => 'payment_gateways_admin', 'action' => 'allow_payments', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^payment\\/gateways\\/admin$/', $path, $matches)) {
      return Router::doMatch($path, 'payment_gateways_admin_section', 'payment/gateways/admin', array ( 'controller' => 'payment_gateways_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/express\\/checkout\\/cancel\\/url$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_paypal_express_checkout_cancel_url', 'admin/express/checkout/cancel/url', array ( 'controller' => 'make_returning_payment', 'action' => 'cancel_from_gateway', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/express\\/checkout\\/return\\/url$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_paypal_express_checkout_return_url', 'admin/express/checkout/return/url', array ( 'controller' => 'make_returning_payment', 'action' => 'paypal_express_checkout_return', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^code-snippets\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'code_snippet_preview', 'code-snippets/preview', array ( 'controller' => 'code_snippets', 'action' => 'preview', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^code-snippets\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'code_snippet_delete', 'code-snippets/:code_snippet_id/delete', array ( 'controller' => 'code_snippets', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'code_snippet_id',
), $matches, $query_string);
    } elseif(preg_match('/^code-snippets\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'code_snippet_edit', 'code-snippets/:code_snippet_id/edit', array ( 'controller' => 'code_snippets', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'code_snippet_id',
), $matches, $query_string);
    } elseif(preg_match('/^code-snippets\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'code_snippet', 'code-snippets/:code_snippet_id', array ( 'controller' => 'code_snippets', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'code_snippet_id',
), $matches, $query_string);
    } elseif(preg_match('/^code-snippets\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'code_snippets_add', 'code-snippets/add', array ( 'controller' => 'code_snippets', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_label_set_as_default', 'admin/assignments/labels/:label_id/set-as-default', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_set_label_as_default', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_label_delete', 'admin/assignments/labels/:label_id/delete', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_delete_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_label_edit', 'admin/assignments/labels/:label_id/edit', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_edit_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_label', 'admin/assignments/labels/:label_id', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_view_label', 'module' => 'system', ), array (
  0 => 'label_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_labels_add', 'admin/assignments/labels/add', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_add_label', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/assignments\\/labels$/', $path, $matches)) {
      return Router::doMatch($path, 'assignments_admin_labels', 'admin/assignments/labels', array ( 'controller' => 'assignment_labels_admin', 'action' => 'assignments_admin_labels', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/labels\\/assignment$/', $path, $matches)) {
      return Router::doMatch($path, 'assignment_labels', 'info/labels/assignment', array ( 'controller' => 'assignees_api', 'action' => 'labels', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/notifications$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_admin', 'admin/notifications', array ( 'controller' => 'notifications_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/popup\\/show-read-and-unread$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_popup_show_read_and_unread', 'notifications/popup/show-read-and-unread', array ( 'controller' => 'notifications', 'action' => 'show_read_and_unread', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/popup\\/show-only-unread$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_popup_show_only_unread', 'notifications/popup/show-only-unread', array ( 'controller' => 'notifications', 'action' => 'show_only_unread', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/popup$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_popup', 'notifications/popup', array ( 'controller' => 'notifications', 'action' => 'popup', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/(\\d+)\\/mark-unread$/', $path, $matches)) {
      return Router::doMatch($path, 'notification_mark_unread', 'notifications/:notification_id/mark-unread', array ( 'controller' => 'notifications', 'action' => 'mark_unread', 'module' => 'system', ), array (
  0 => 'notification_id',
), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/(\\d+)\\/mark-read$/', $path, $matches)) {
      return Router::doMatch($path, 'notification_mark_read', 'notifications/:notification_id/mark-read', array ( 'controller' => 'notifications', 'action' => 'mark_read', 'module' => 'system', ), array (
  0 => 'notification_id',
), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'notification_delete', 'notifications/:notification_id/delete', array ( 'controller' => 'notifications', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'notification_id',
), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'notification_edit', 'notifications/:notification_id/edit', array ( 'controller' => 'notifications', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'notification_id',
), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'notification', 'notifications/:notification_id', array ( 'controller' => 'notifications', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'notification_id',
), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/seen-all$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_seen_all', 'notifications/seen-all', array ( 'controller' => 'notifications', 'action' => 'seen_all', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/refresh$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_refresh', 'notifications/refresh', array ( 'controller' => 'notifications', 'action' => 'refresh', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_settings', 'notifications/settings', array ( 'controller' => 'notifications', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications\\/mass-edit$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications_mass_edit', 'notifications/mass-edit', array ( 'controller' => 'notifications', 'action' => 'mass_edit', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^notifications$/', $path, $matches)) {
      return Router::doMatch($path, 'notifications', 'notifications', array ( 'controller' => 'notifications', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^public\\/notifications\\/unsubscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'public_notifications_unsubscribe', 'public/notifications/unsubscribe', array ( 'controller' => 'public_notifications', 'action' => 'unsubscribe', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^public\\/notifications\\/subscribe$/', $path, $matches)) {
      return Router::doMatch($path, 'public_notifications_subscribe', 'public/notifications/subscribe', array ( 'controller' => 'public_notifications', 'action' => 'subscribe', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_untrash', '/attachments/:attachment_id/untrash', array ( 'controller' => 'temporary_attachments', 'action' => 'attachment_state_untrash', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_trash', '/attachments/:attachment_id/trash', array ( 'controller' => 'temporary_attachments', 'action' => 'attachment_state_trash', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_unarchive', '/attachments/:attachment_id/unarchive', array ( 'controller' => 'temporary_attachments', 'action' => 'attachment_state_unarchive', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_archive', '/attachments/:attachment_id/archive', array ( 'controller' => 'temporary_attachments', 'action' => 'attachment_state_archive', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-temporary-attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_temporary_attachments', '/admin/disk-space/tools/remove-temporary-attachments', array ( 'controller' => 'attachments_disk_space_admin', 'action' => 'remove_temporary_attachments', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_delete', '/attachments/:attachment_id/delete', array ( 'controller' => 'temporary_attachments', 'action' => 'attachment_state_delete', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_edit', '/attachments/:attachment_id/edit', array ( 'controller' => 'temporary_attachments', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment_download', '/attachments/:attachment_id/download', array ( 'controller' => 'temporary_attachments', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'attachment', '/attachments/:attachment_id', array ( 'controller' => 'temporary_attachments', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^attachments\\/temporary\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'temporary_attachment_add', 'attachments/temporary/add', array ( 'controller' => 'temporary_attachments', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^announcements\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'announcement_dismiss', 'announcements/:announcement_id/dismiss', array ( 'controller' => 'announcements', 'action' => 'dismiss', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcement_delete', 'admin/announcements/:announcement_id/delete', array ( 'controller' => 'announcements_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/(\\d+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcement_disable', 'admin/announcements/:announcement_id/disable', array ( 'controller' => 'announcements_admin', 'action' => 'disable', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/(\\d+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcement_enable', 'admin/announcements/:announcement_id/enable', array ( 'controller' => 'announcements_admin', 'action' => 'enable', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcement_edit', 'admin/announcements/:announcement_id/edit', array ( 'controller' => 'announcements_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcement', 'admin/announcements/:announcement_id', array ( 'controller' => 'announcements_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'announcement_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcements_reorder', 'admin/announcements/reorder', array ( 'controller' => 'announcements_admin', 'action' => 'reorder', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcements_add', 'admin/announcements/add', array ( 'controller' => 'announcements_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/announcements$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_announcements', 'admin/announcements', array ( 'controller' => 'announcements_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^homescreen\\/widgets\\/(\\d+)\\/render$/', $path, $matches)) {
      return Router::doMatch($path, 'homescreen_widget_render', 'homescreen/widgets/:widget_id/render', array ( 'controller' => 'homescreen_widgets', 'module' => 'system', 'action' => 'render', ), array (
  0 => 'widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/remove\\/selected$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_mail_remove_selected_conflicts', 'admin/mailing/incoming/remove/selected', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'remove_selected_conflicts', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/remove\\/all$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_mail_remove_all_conflicts', 'admin/mailing/incoming/remove/all', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'remove_all_conflicts', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mass-conflict-resolution$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_mail_mass_conflict_resolution', 'admin/mailing/incoming/mass-conflict-resolution', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'mass_conflict_resolution', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/(\\d+)\\/import$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_import_mail', 'admin/mailing/incoming/:incoming_mail_id/import', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'conflict', 'module' => 'system', ), array (
  0 => 'incoming_mail_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_delete_mail', 'admin/mailing/incoming/:incoming_mail_id/delete', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'incoming_mail_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mail\\/conflict$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_conflict', 'admin/mailing/incoming/mail/conflict', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mail$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_mail', 'admin/mailing/incoming/mail', array ( 'controller' => 'incoming_mail_conflict', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_filter_reorder', 'admin/mailing/incoming/filter/reorder', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'reorder_position', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_delete', 'admin/mailing/incoming/filter/:filter_id/delete', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_edit', 'admin/mailing/incoming/filter/:filter_id/edit', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/(\\d+)\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_view', 'admin/mailing/incoming/filter/:filter_id/view', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/([a-z0-9\\-\\._]+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_disable', 'admin/mailing/incoming/filter/:filter_id/disable', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'disable', 'module' => 'system', ), array (
  0 => 'filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/([a-z0-9\\-\\._]+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_enable', 'admin/mailing/incoming/filter/:filter_id/enable', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'enable', 'module' => 'system', ), array (
  0 => 'filter_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filter\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filter_add', 'admin/mailing/incoming/filter/add', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/filters$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_filters', 'admin/mailing/incoming/filters', array ( 'controller' => 'incoming_mail_filter_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)\\/show\\/more\\/results$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_show_more_results', 'admin/mailing/incoming/mailboxes/:mailbox_id/show/more/results', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'show_more_results', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_change_settings', 'admin/mailing/incoming/settings', array ( 'controller' => 'incoming_mail_admin', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)\\/delete_messages$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_delete_messages', 'admin/mailing/incoming/mailboxes/:mailbox_id/delete_messages', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'delete_message_from_server', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)\\/list$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_list_messages', 'admin/mailing/incoming/mailboxes/:mailbox_id/list', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'list_messages', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_delete', 'admin/mailing/incoming/mailboxes/:mailbox_id/delete', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_edit', 'admin/mailing/incoming/mailboxes/:mailbox_id/edit', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox', 'admin/mailing/incoming/mailboxes/:mailbox_id', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/([a-z0-9\\-\\._]+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_disable', 'admin/mailing/incoming/mailboxes/:mailbox_id/disable', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'disable', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/([a-z0-9\\-\\._]+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_enable', 'admin/mailing/incoming/mailboxes/:mailbox_id/enable', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'enable', 'module' => 'system', ), array (
  0 => 'mailbox_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_add', 'admin/mailing/incoming/mailboxes/add', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^adminmailing\\/incoming\\/test-mailbox-connection$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailbox_test_connection', 'adminmailing/incoming/test-mailbox-connection', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'test_mailbox_connection', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/incoming\\/mailboxes$/', $path, $matches)) {
      return Router::doMatch($path, 'incoming_email_admin_mailboxes', 'admin/mailing/incoming/mailboxes', array ( 'controller' => 'incoming_mailboxes_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_untrash', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/untrash', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_state_untrash', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_trash', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/trash', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_state_trash', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_unarchive', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/unarchive', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_state_unarchive', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_archive', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/archive', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_state_archive', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/preview$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_preview', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/preview', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_preview_content', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-thumbnails$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_thumbnails', 'admin/disk-space/tools/remove-thumbnails', array ( 'controller' => 'preview_disk_space_admin', 'action' => 'remove_thumbnails', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/download$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_download', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/download', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_download_content', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_delete', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/delete', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachment_state_delete', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment_edit', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id/edit', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_edit_attachment', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachment', 'admin/mailing/outgoing/messages/:message_id/attachments/:attachment_id', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_view_attachment', 'module' => 'email', ), array (
  0 => 'message_id',
  1 => 'attachment_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachments_add', 'admin/mailing/outgoing/messages/:message_id/attachments/add', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_add_attachment', 'module' => 'email', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/attachments$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_attachments', 'admin/mailing/outgoing/messages/:message_id/attachments', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'outgoing_messages_admin_message_attachments', 'module' => 'email', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_delete', 'admin/mailing/outgoing/messages/:message_id/delete', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_send', 'admin/mailing/outgoing/messages/:message_id/send', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'send', 'module' => 'system', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)\\/raw-body$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message_raw_body', 'admin/mailing/outgoing/messages/:message_id/raw-body', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'view_raw_body', 'module' => 'system', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin_message', 'admin/mailing/outgoing/messages/:message_id', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'message_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/messages$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_messages_admin', 'admin/mailing/outgoing/messages', array ( 'controller' => 'outgoing_messages_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/send-test-message$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_email_admin_send_test_message', 'admin/mailing/outgoing/send-test-message', array ( 'controller' => 'outgoing_email_admin', 'action' => 'send_test_message', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/test-smtp-connection$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_email_admin_test_smtp_connection', 'admin/mailing/outgoing/test-smtp-connection', array ( 'controller' => 'outgoing_email_admin', 'action' => 'test_smtp_connection', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/outgoing\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'outgoing_email_admin_settings', 'admin/mailing/outgoing/settings', array ( 'controller' => 'outgoing_email_admin', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/reply-to-comment\\/update-mailbox$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_reply_to_comment_update_mailbox', 'admin/mailing/reply-to-comment/update-mailbox', array ( 'controller' => 'email_to_comment', 'action' => 'update_mailbox', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/reply-to-comment\\/install-imap$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_reply_to_comment_install_imap', 'admin/mailing/reply-to-comment/install-imap', array ( 'controller' => 'email_to_comment', 'action' => 'install_imap', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/reply-to-comment\\/change-from-address$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_reply_to_comment_change_from_address', 'admin/mailing/reply-to-comment/change-from-address', array ( 'controller' => 'email_to_comment', 'action' => 'change_from_address', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/reply-to-comment$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_reply_to_comment', 'admin/mailing/reply-to-comment', array ( 'controller' => 'email_to_comment', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/log\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_log_entry_delete', 'admin/mailing/log/:log_entry_id/delete', array ( 'controller' => 'email_admin', 'action' => 'log_entry_delete', 'module' => 'system', ), array (
  0 => 'log_entry_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/log\\/([a-z0-9\\-\\._]+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_log_entry_edit', 'admin/mailing/log/:log_entry_id/edit', array ( 'controller' => 'email_admin', 'action' => 'log_entry_edit', 'module' => 'system', ), array (
  0 => 'log_entry_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/log\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_log_entry', 'admin/mailing/log/:log_entry_id', array ( 'controller' => 'email_admin', 'action' => 'log_entry', 'module' => 'system', ), array (
  0 => 'log_entry_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing\\/log$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin_logs', 'admin/mailing/log', array ( 'controller' => 'email_admin', 'action' => 'log', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/mailing$/', $path, $matches)) {
      return Router::doMatch($path, 'email_admin', 'admin/mailing', array ( 'controller' => 'email_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^object-history$/', $path, $matches)) {
      return Router::doMatch($path, 'object_history', 'object-history', array ( 'controller' => 'object_history', 'action' => 'index', 'module' => 'history', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reports$/', $path, $matches)) {
      return Router::doMatch($path, 'reports', 'reports', array ( 'controller' => 'reports', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^activity-log\\/rss$/', $path, $matches)) {
      return Router::doMatch($path, 'backend_activity_log_rss', '/activity-log/rss', array ( 'controller' => 'backend', 'action' => 'backend_activity_log_rss', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^activity-log$/', $path, $matches)) {
      return Router::doMatch($path, 'backend_activity_log', '/activity-log', array ( 'controller' => 'backend', 'action' => 'backend_activity_log', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/clean$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_clean', 'admin/indices/activity-logs/clean', array ( 'controller' => 'activity_logs_admin', 'action' => 'clean', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/activity-logs\\/rebuild$/', $path, $matches)) {
      return Router::doMatch($path, 'activity_logs_admin_rebuild', 'admin/indices/activity-logs/rebuild', array ( 'controller' => 'activity_logs_admin', 'action' => 'rebuild', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/users\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'users_search_index_admin_build', 'admin/search/users/build', array ( 'controller' => 'users_search_index_admin', 'action' => 'build', 'module' => 'system', 'search_index_name' => 'users', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles\\/(([a-z0-9\\-\\._]+))$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_role', 'admin/roles/:user_role_name', array ( 'controller' => 'roles_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'user_role_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/roles$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_roles', 'admin/roles', array ( 'controller' => 'roles_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/maintenance-mode$/', $path, $matches)) {
      return Router::doMatch($path, 'maintenance_mode_settings', 'admin/maintenance-mode', array ( 'controller' => 'maintenance_mode', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/roles\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'system_role_info', 'info/roles/:role_id', array ( 'controller' => 'roles_info', 'action' => 'role', 'module' => 'system', ), array (
  0 => 'role_id',
), $matches, $query_string);
    } elseif(preg_match('/^info\\/roles$/', $path, $matches)) {
      return Router::doMatch($path, 'system_roles_info', 'info/roles', array ( 'controller' => 'roles_info', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminder_delete', 'users/:user_id/reminders/:reminder_id/delete', array ( 'controller' => 'users', 'action' => 'user_delete_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/(\\d+)\\/dismiss$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminder_dismiss', 'users/:user_id/reminders/:reminder_id/dismiss', array ( 'controller' => 'users', 'action' => 'user_dismiss_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/(\\d+)\\/send$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminder_send', 'users/:user_id/reminders/:reminder_id/send', array ( 'controller' => 'users', 'action' => 'user_send_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminder_edit', 'users/:user_id/reminders/:reminder_id/edit', array ( 'controller' => 'users', 'action' => 'user_edit_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminder', 'users/:user_id/reminders/:reminder_id', array ( 'controller' => 'users', 'action' => 'user_view_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'reminder_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/nudge$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminders_nudge', 'users/:user_id/reminders/nudge', array ( 'controller' => 'users', 'action' => 'user_nudge_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminders_add', 'users/:user_id/reminders/add', array ( 'controller' => 'users', 'action' => 'user_add_reminder', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/reminders$/', $path, $matches)) {
      return Router::doMatch($path, 'user_reminders', 'users/:user_id/reminders', array ( 'controller' => 'users', 'action' => 'user_reminders', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_widget_delete', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/delete', array ( 'controller' => 'users', 'action' => 'user_homescreen_widget_delete', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
  2 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_widget_edit', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id/edit', array ( 'controller' => 'users', 'action' => 'user_homescreen_widget_edit', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
  2 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_widget', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/:homescreen_widget_id', array ( 'controller' => 'users', 'action' => 'user_homescreen_widget', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
  2 => 'homescreen_widget_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_widgets_reorder', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/reorder', array ( 'controller' => 'users', 'action' => 'user_homescreen_widgets_reorder', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/widgets\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_widgets_add', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/widgets/add', array ( 'controller' => 'users', 'action' => 'user_homescreen_widgets_add', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_delete', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/delete', array ( 'controller' => 'users', 'action' => 'user_homescreen_tab_delete', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab_edit', 'users/:user_id/homescreen/tabs/:homescreen_tab_id/edit', array ( 'controller' => 'users', 'action' => 'user_homescreen_tab_edit', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tab', 'users/:user_id/homescreen/tabs/:homescreen_tab_id', array ( 'controller' => 'users', 'action' => 'user_homescreen_tab', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'homescreen_tab_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/reorder$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tabs_reorder', 'users/:user_id/homescreen/tabs/reorder', array ( 'controller' => 'users', 'action' => 'user_homescreen_tabs_reorder', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen\\/tabs\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen_tabs_add', 'users/:user_id/homescreen/tabs/add', array ( 'controller' => 'users', 'action' => 'user_homescreen_tabs_add', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/homescreen$/', $path, $matches)) {
      return Router::doMatch($path, 'user_homescreen', 'users/:user_id/homescreen', array ( 'controller' => 'users', 'action' => 'user_homescreen', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/activity-log\\/rss$/', $path, $matches)) {
      return Router::doMatch($path, 'user_activity_log_rss', 'users/:user_id/activity-log/rss', array ( 'controller' => 'users', 'action' => 'user_activity_log_rss', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/activity-log$/', $path, $matches)) {
      return Router::doMatch($path, 'user_activity_log', 'users/:user_id/activity-log', array ( 'controller' => 'users', 'action' => 'user_activity_log', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/avatar\\/remove$/', $path, $matches)) {
      return Router::doMatch($path, 'user_avatar_remove', 'users/:user_id/avatar/remove', array ( 'controller' => 'users', 'action' => 'user/avatar_remove', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/avatar\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_avatar_edit', 'users/:user_id/avatar/edit', array ( 'controller' => 'users', 'action' => 'user/avatar_edit', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/avatar\\/upload$/', $path, $matches)) {
      return Router::doMatch($path, 'user_avatar_upload', 'users/:user_id/avatar/upload', array ( 'controller' => 'users', 'action' => 'user/avatar_upload', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/avatar\\/view$/', $path, $matches)) {
      return Router::doMatch($path, 'user_avatar_view', 'users/:user_id/avatar/view', array ( 'controller' => 'users', 'action' => 'user/avatar_view', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/untrash$/', $path, $matches)) {
      return Router::doMatch($path, 'user_untrash', 'users/:user_id/untrash', array ( 'controller' => 'users', 'action' => 'user_state_untrash', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/trash$/', $path, $matches)) {
      return Router::doMatch($path, 'user_trash', 'users/:user_id/trash', array ( 'controller' => 'users', 'action' => 'user_state_trash', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/unarchive$/', $path, $matches)) {
      return Router::doMatch($path, 'user_unarchive', 'users/:user_id/unarchive', array ( 'controller' => 'users', 'action' => 'user_state_unarchive', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/archive$/', $path, $matches)) {
      return Router::doMatch($path, 'user_archive', 'users/:user_id/archive', array ( 'controller' => 'users', 'action' => 'user_state_archive', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscription_delete', 'users/:user_id/api-subscriptions/:api_client_subscription_id/delete', array ( 'controller' => 'users', 'action' => 'user_delete_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscription_edit', 'users/:user_id/api-subscriptions/:api_client_subscription_id/edit', array ( 'controller' => 'users', 'action' => 'user_edit_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscription_disable', 'users/:user_id/api-subscriptions/:api_client_subscription_id/disable', array ( 'controller' => 'users', 'action' => 'user_disable_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/(\\d+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscription_enable', 'users/:user_id/api-subscriptions/:api_client_subscription_id/enable', array ( 'controller' => 'users', 'action' => 'user_enable_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscription', 'users/:user_id/api-subscriptions/:api_client_subscription_id', array ( 'controller' => 'users', 'action' => 'user_view_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
  1 => 'api_client_subscription_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscriptions_add', 'users/:user_id/api-subscriptions/add', array ( 'controller' => 'users', 'action' => 'user_add_api_client_subscription', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/api-subscriptions$/', $path, $matches)) {
      return Router::doMatch($path, 'user_api_client_subscriptions', 'users/:user_id/api-subscriptions', array ( 'controller' => 'users', 'action' => 'user_api_client_subscriptions', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/login-as$/', $path, $matches)) {
      return Router::doMatch($path, 'user_login_as', 'users/:user_id/login-as', array ( 'controller' => 'users', 'action' => 'login_as', 'module' => 'authentication', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/set-as-invited$/', $path, $matches)) {
      return Router::doMatch($path, 'user_set_as_invited', 'users/:user_id/set-as-invited', array ( 'controller' => 'users', 'action' => 'set_as_invited', 'module' => 'authentication', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/export-vcard$/', $path, $matches)) {
      return Router::doMatch($path, 'user_export_vcard', 'users/:user_id/export-vcard', array ( 'controller' => 'users', 'action' => 'export_vcard', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'user_delete', 'users/:user_id/delete', array ( 'controller' => 'users', 'action' => 'user_state_delete', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/edit-settings$/', $path, $matches)) {
      return Router::doMatch($path, 'user_edit_settings', 'users/:user_id/edit-settings', array ( 'controller' => 'users', 'action' => 'edit_settings', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/edit-profile$/', $path, $matches)) {
      return Router::doMatch($path, 'user_edit_profile', 'users/:user_id/edit-profile', array ( 'controller' => 'users', 'action' => 'edit_profile', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/edit-password$/', $path, $matches)) {
      return Router::doMatch($path, 'user_edit_password', 'users/:user_id/edit-password', array ( 'controller' => 'users', 'action' => 'edit_password', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'user_edit', 'users/:user_id/edit', array ( 'controller' => 'users', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'user', 'users/:user_id', array ( 'controller' => 'users', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'user_id',
), $matches, $query_string);
    } elseif(preg_match('/^users\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'users_add', 'users/add', array ( 'controller' => 'users', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^users$/', $path, $matches)) {
      return Router::doMatch($path, 'users', 'users', array ( 'controller' => 'users', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^reset-password$/', $path, $matches)) {
      return Router::doMatch($path, 'reset_password', 'reset-password', array ( 'controller' => 'authentication', 'action' => 'reset_password', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^lost-password$/', $path, $matches)) {
      return Router::doMatch($path, 'forgot_password', 'lost-password', array ( 'controller' => 'authentication', 'action' => 'forgot_password', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^logout$/', $path, $matches)) {
      return Router::doMatch($path, 'logout', 'logout', array ( 'controller' => 'authentication', 'action' => 'logout', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^login$/', $path, $matches)) {
      return Router::doMatch($path, 'login', 'login', array ( 'controller' => 'authentication', 'action' => 'login', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/days-off$/', $path, $matches)) {
      return Router::doMatch($path, 'days_off_info', 'info/days-off', array ( 'controller' => 'api', 'action' => 'days_off', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info\\/currencies$/', $path, $matches)) {
      return Router::doMatch($path, 'days_currencies', 'info/currencies', array ( 'controller' => 'api', 'action' => 'currencies', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/translate-letter$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_translate_letter', 'admin/languages/:language_id/translate-letter', array ( 'controller' => 'languages_admin', 'action' => 'translate_letter', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/save-translation$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_save_single_translation', 'admin/languages/:language_id/save-translation', array ( 'controller' => 'languages_admin', 'action' => 'save_single', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/edit-translation$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_edit_translation', 'admin/languages/:language_id/edit-translation', array ( 'controller' => 'languages_admin', 'action' => 'edit_translation', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_delete', 'admin/languages/:language_id/delete', array ( 'controller' => 'languages_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_edit', 'admin/languages/:language_id/edit', array ( 'controller' => 'languages_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/do-update$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_do_update', 'admin/languages/:language_id/do-update', array ( 'controller' => 'languages_admin', 'action' => 'do_update', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/update$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_update', 'admin/languages/update', array ( 'controller' => 'languages_admin', 'action' => 'update', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)\\/export$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_export', 'admin/languages/:language_id/export', array ( 'controller' => 'languages_admin', 'action' => 'export', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language', 'admin/languages/:language_id', array ( 'controller' => 'languages_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'language_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/execute-import-steps$/', $path, $matches)) {
      return Router::doMatch($path, 'execute_import_steps', 'admin/execute-import-steps', array ( 'controller' => 'languages_admin', 'action' => 'execute_import_steps', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/do-import$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_language_do_import', 'admin/languages/do-import', array ( 'controller' => 'languages_admin', 'action' => 'do_import', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/import$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_languages_import', 'admin/languages/import', array ( 'controller' => 'languages_admin', 'action' => 'import', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/set-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_languages_set_default', 'admin/languages/set-default', array ( 'controller' => 'languages_admin', 'action' => 'set_default', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_languages_add', 'admin/languages/add', array ( 'controller' => 'languages_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/languages$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_languages', 'admin/languages', array ( 'controller' => 'languages_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies\\/(\\d+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currency_delete', 'admin/currencies/:currency_id/delete', array ( 'controller' => 'currencies_admin', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'currency_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies\\/(\\d+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currency_set_as_default', 'admin/currencies/:currency_id/set-as-default', array ( 'controller' => 'currencies_admin', 'action' => 'set_as_default', 'module' => 'system', ), array (
  0 => 'currency_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies\\/(\\d+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currency_edit', 'admin/currencies/:currency_id/edit', array ( 'controller' => 'currencies_admin', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'currency_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies\\/(\\d+)$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currency', 'admin/currencies/:currency_id', array ( 'controller' => 'currencies_admin', 'action' => 'view', 'module' => 'system', ), array (
  0 => 'currency_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies\\/add$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currencies_add', 'admin/currencies/add', array ( 'controller' => 'currencies_admin', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/currencies$/', $path, $matches)) {
      return Router::doMatch($path, 'admin_currencies', 'admin/currencies', array ( 'controller' => 'currencies_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/workweek$/', $path, $matches)) {
      return Router::doMatch($path, 'workweek_settings', 'admin/workweek', array ( 'controller' => 'globalization_admin', 'action' => 'workweek', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/date-time$/', $path, $matches)) {
      return Router::doMatch($path, 'date_time_settings', 'admin/date-time', array ( 'controller' => 'globalization_admin', 'action' => 'date_time', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/search\\/help\\/build$/', $path, $matches)) {
      return Router::doMatch($path, 'help_search_index_admin_build', 'admin/search/help/build', array ( 'controller' => 'help_search_index_admin', 'action' => 'build', 'module' => 'system', 'search_index_name' => 'help', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help\\/videos\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'help_video', 'help/videos/:video_name', array ( 'controller' => 'help_videos', 'action' => 'video', 'module' => 'system', ), array (
  0 => 'video_name',
), $matches, $query_string);
    } elseif(preg_match('/^help\\/videos$/', $path, $matches)) {
      return Router::doMatch($path, 'help_videos', 'help/videos', array ( 'controller' => 'help_videos', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help\\/books\\/([a-z0-9\\-\\._]+)\\/pages\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'help_book_page', 'help/books/:book_name/pages/:page_name', array ( 'controller' => 'help_books', 'action' => 'page', 'module' => 'system', ), array (
  0 => 'book_name',
  1 => 'page_name',
), $matches, $query_string);
    } elseif(preg_match('/^help\\/books\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'help_book', 'help/books/:book_name', array ( 'controller' => 'help_books', 'action' => 'book', 'module' => 'system', ), array (
  0 => 'book_name',
), $matches, $query_string);
    } elseif(preg_match('/^help\\/books$/', $path, $matches)) {
      return Router::doMatch($path, 'help_books', 'help/books', array ( 'controller' => 'help_books', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help\\/whats-new\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'help_whats_new_article', 'help/whats-new/:article_name', array ( 'controller' => 'help_whats_new', 'action' => 'article', 'module' => 'system', ), array (
  0 => 'article_name',
), $matches, $query_string);
    } elseif(preg_match('/^help\\/whats-new$/', $path, $matches)) {
      return Router::doMatch($path, 'help_whats_new', 'help/whats-new', array ( 'controller' => 'help_whats_new', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help\\/search$/', $path, $matches)) {
      return Router::doMatch($path, 'help_search', 'help/search', array ( 'controller' => 'help', 'action' => 'search', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help\\/popup$/', $path, $matches)) {
      return Router::doMatch($path, 'help_popup', 'help/popup', array ( 'controller' => 'help', 'action' => 'popup', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^help$/', $path, $matches)) {
      return Router::doMatch($path, 'help', 'help', array ( 'controller' => 'help', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disable-custom-modules$/', $path, $matches)) {
      return Router::doMatch($path, 'disable_custom_modules', 'admin/disable-custom-modules', array ( 'controller' => 'modules_admin', 'action' => 'disable_custom_modules', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/execute-installation-step$/', $path, $matches)) {
      return Router::doMatch($path, 'execute_installation_steps', 'admin/execute-installation-step', array ( 'controller' => 'modules_admin', 'action' => 'execute_installation_steps', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules\\/([a-z0-9\\-\\._]+)\\/disable$/', $path, $matches)) {
      return Router::doMatch($path, 'module_admin_module_disable', 'admin/modules/:module_name/disable', array ( 'controller' => 'modules_admin', 'action' => 'disable', 'module' => 'system', ), array (
  0 => 'module_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules\\/([a-z0-9\\-\\._]+)\\/enable$/', $path, $matches)) {
      return Router::doMatch($path, 'module_admin_module_enable', 'admin/modules/:module_name/enable', array ( 'controller' => 'modules_admin', 'action' => 'enable', 'module' => 'system', ), array (
  0 => 'module_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules\\/([a-z0-9\\-\\._]+)\\/uninstall$/', $path, $matches)) {
      return Router::doMatch($path, 'module_admin_module_uninstall', 'admin/modules/:module_name/uninstall', array ( 'controller' => 'modules_admin', 'action' => 'uninstall', 'module' => 'system', ), array (
  0 => 'module_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules\\/([a-z0-9\\-\\._]+)\\/install$/', $path, $matches)) {
      return Router::doMatch($path, 'module_admin_module_install', 'admin/modules/:module_name/install', array ( 'controller' => 'modules_admin', 'action' => 'install', 'module' => 'system', ), array (
  0 => 'module_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'module_admin_module', 'admin/modules/:module_name', array ( 'controller' => 'modules_admin', 'action' => 'module', 'module' => 'system', ), array (
  0 => 'module_name',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/modules$/', $path, $matches)) {
      return Router::doMatch($path, 'modules_admin', 'admin/modules', array ( 'controller' => 'modules_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/firewall$/', $path, $matches)) {
      return Router::doMatch($path, 'firewall', 'admin/firewall', array ( 'controller' => 'firewall', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/control-tower$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_settings', 'admin/control-tower', array ( 'controller' => 'control_tower', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower\\/performance-checklist$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_performance_checklist', 'control-tower/performance-checklist', array ( 'controller' => 'control_tower', 'action' => 'performance_checklist', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower\\/rebuild-localization$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_rebuild_localization', 'control-tower/rebuild-localization', array ( 'controller' => 'control_tower', 'action' => 'rebuild_localization', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower\\/rebuild-images$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_rebuild_images', 'control-tower/rebuild-images', array ( 'controller' => 'control_tower', 'action' => 'rebuild_images', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower\\/delete-compiled-templates$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_delete_compiled_templates', 'control-tower/delete-compiled-templates', array ( 'controller' => 'control_tower', 'action' => 'delete_compiled_templates', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower\\/empty-cache$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower_empty_cache', 'control-tower/empty-cache', array ( 'controller' => 'control_tower', 'action' => 'empty_cache', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^control-tower$/', $path, $matches)) {
      return Router::doMatch($path, 'control_tower', 'control-tower', array ( 'controller' => 'control_tower', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^trash\\/delete-object$/', $path, $matches)) {
      return Router::doMatch($path, 'object_delete', 'trash/delete-object', array ( 'controller' => 'trash', 'action' => 'delete_object', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^trash\\/untrash-object$/', $path, $matches)) {
      return Router::doMatch($path, 'object_untrash', 'trash/untrash-object', array ( 'controller' => 'trash', 'action' => 'untrash_object', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^trash\\/empty$/', $path, $matches)) {
      return Router::doMatch($path, 'trash_empty', 'trash/empty', array ( 'controller' => 'trash', 'action' => 'empty_trash', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^trash\\/([a-z0-9\\-\\._]+)$/', $path, $matches)) {
      return Router::doMatch($path, 'trash_section', 'trash/:section_name', array ( 'controller' => 'trash', 'action' => 'section', 'module' => 'system', ), array (
  0 => 'section_name',
), $matches, $query_string);
    } elseif(preg_match('/^trash$/', $path, $matches)) {
      return Router::doMatch($path, 'trash', 'trash', array ( 'controller' => 'trash', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^daily$/', $path, $matches)) {
      return Router::doMatch($path, 'daily', 'daily', array ( 'controller' => 'scheduled_tasks', 'action' => 'daily', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^hourly$/', $path, $matches)) {
      return Router::doMatch($path, 'hourly', 'hourly', array ( 'controller' => 'scheduled_tasks', 'action' => 'hourly', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^frequently$/', $path, $matches)) {
      return Router::doMatch($path, 'frequently', 'frequently', array ( 'controller' => 'scheduled_tasks', 'action' => 'frequently', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/clean$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_clean', 'admin/indices/object-contexts/clean', array ( 'controller' => 'object_contexts_admin', 'action' => 'clean', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/object-contexts\\/rebuild$/', $path, $matches)) {
      return Router::doMatch($path, 'object_contexts_admin_rebuild', 'admin/indices/object-contexts/rebuild', array ( 'controller' => 'object_contexts_admin', 'action' => 'rebuild', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/rebuild\\/finish$/', $path, $matches)) {
      return Router::doMatch($path, 'indices_admin_rebuild_finish', 'admin/indices/rebuild/finish', array ( 'controller' => 'indices_admin', 'action' => 'rebuild_finish', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices\\/rebuild$/', $path, $matches)) {
      return Router::doMatch($path, 'indices_admin_rebuild', 'admin/indices/rebuild', array ( 'controller' => 'indices_admin', 'action' => 'rebuild', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/indices$/', $path, $matches)) {
      return Router::doMatch($path, 'indices_admin', 'admin/indices', array ( 'controller' => 'indices_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/network$/', $path, $matches)) {
      return Router::doMatch($path, 'network_settings', 'admin/network', array ( 'controller' => 'network_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/scheduled-tasks$/', $path, $matches)) {
      return Router::doMatch($path, 'scheduled_tasks_admin', 'admin/scheduled-tasks', array ( 'controller' => 'scheduled_tasks_admin', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance\\/([a-z0-9\\-\\._]+)\\/set-as-default$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin_set_as_default_scheme', 'admin/appearance/:scheme_id/set-as-default', array ( 'controller' => 'appearance', 'action' => 'set_as_default', 'module' => 'system', ), array (
  0 => 'scheme_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance\\/([a-z0-9\\-\\._]+)\\/delete$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin_delete_scheme', 'admin/appearance/:scheme_id/delete', array ( 'controller' => 'appearance', 'action' => 'delete', 'module' => 'system', ), array (
  0 => 'scheme_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance\\/([a-z0-9\\-\\._]+)\\/rename$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin_rename_scheme', 'admin/appearance/:scheme_id/rename', array ( 'controller' => 'appearance', 'action' => 'rename', 'module' => 'system', ), array (
  0 => 'scheme_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance\\/([a-z0-9\\-\\._]+)\\/edit$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin_edit_scheme', 'admin/appearance/:scheme_id/edit', array ( 'controller' => 'appearance', 'action' => 'edit', 'module' => 'system', ), array (
  0 => 'scheme_id',
), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance\\/add-scheme$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin_add_scheme', 'admin/appearance/add-scheme', array ( 'controller' => 'appearance', 'action' => 'add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/appearance$/', $path, $matches)) {
      return Router::doMatch($path, 'appearance_admin', 'admin/appearance', array ( 'controller' => 'appearance', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-orphan-files$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_orphan_files', 'admin/disk-space/tools/remove-orphan-files', array ( 'controller' => 'disk_space_admin', 'action' => 'remove_orphan_files', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-old-application-versions$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_old_application_versions', 'admin/disk-space/tools/remove-old-application-versions', array ( 'controller' => 'disk_space_admin', 'action' => 'remove_old_application_versions', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-logs$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_logs', 'admin/disk-space/tools/remove-logs', array ( 'controller' => 'disk_space_admin', 'action' => 'remove_logs', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/tools\\/remove-application-cache$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_remove_application_cache', 'admin/disk-space/tools/remove-application-cache', array ( 'controller' => 'disk_space_admin', 'action' => 'remove_application_cache', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/settings$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_admin_settings', 'admin/disk-space/settings', array ( 'controller' => 'disk_space_admin', 'action' => 'settings', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space\\/usage$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_usage', 'admin/disk-space/usage', array ( 'controller' => 'disk_space_admin', 'action' => 'usage', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin\\/disk-space$/', $path, $matches)) {
      return Router::doMatch($path, 'disk_space_admin', 'admin/disk-space', array ( 'controller' => 'disk_space_admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^info$/', $path, $matches)) {
      return Router::doMatch($path, 'info', 'info', array ( 'controller' => 'api', 'action' => 'info', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^quick-add$/', $path, $matches)) {
      return Router::doMatch($path, 'quick_add', 'quick-add', array ( 'controller' => 'backend', 'action' => 'quick_add', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^refresh-menu$/', $path, $matches)) {
      return Router::doMatch($path, 'menu_refresh_url', 'refresh-menu', array ( 'controller' => 'backend', 'action' => 'refresh_menu', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^wireframe-updates$/', $path, $matches)) {
      return Router::doMatch($path, 'wireframe_updates', 'wireframe-updates', array ( 'controller' => 'backend', 'action' => 'wireframe_updates', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^public$/', $path, $matches)) {
      return Router::doMatch($path, 'public', 'public', array ( 'controller' => 'public', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^admin$/', $path, $matches)) {
      return Router::doMatch($path, 'admin', 'admin', array ( 'controller' => 'admin', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    } elseif(preg_match('/^$/', $path, $matches)) {
      return Router::doMatch($path, 'homepage', '', array ( 'controller' => 'backend', 'action' => 'index', 'module' => 'system', ), array(), $matches, $query_string);
    }

    return array(null, null);
  };