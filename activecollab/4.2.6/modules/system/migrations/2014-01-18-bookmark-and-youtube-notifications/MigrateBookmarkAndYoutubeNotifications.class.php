<?php

  /**
   * Migrate bookmark and YouTube video notifications
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateBookmarkAndYoutubeNotifications extends AngieModelMigration {

    /**
     * Delete old notifications
     */
    function up() {
      $notification_ids = $this->executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'notifications WHERE type IN (?) OR parent_type IN (?)', array('NewBookmarkNotification', 'NewYouTubeVideoNotification'), array('Bookmark', 'YouTubeVideo'));

      if($notification_ids) {
        $this->execute('DELETE FROM ' . TABLE_PREFIX . 'notification_recipients WHERE notification_id IN (?)', $notification_ids);
        $this->execute('DELETE FROM ' . TABLE_PREFIX . 'notifications WHERE id IN (?)', $notification_ids);
      } // if
    } // up

  }