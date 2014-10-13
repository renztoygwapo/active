<?php

  /**
   * Migrate bookmarks to discussions
   *
   * @package activeCollab.modules.system
   * @migrations
   */
  class MigrateBookmarksToDiscussions extends AngieModelMigration {

    /**
     * Upgrade the data
     */
    function up() {
      list($project_objects, $modification_logs, $comments, $subscriptions, $reminders) = $this->useTables('project_objects', 'modification_logs', 'comments', 'subscriptions', 'reminders');

      $bookmarks = $this->execute("SELECT id, body, varchar_field_1 FROM $project_objects WHERE type = 'Bookmark'");
      if($bookmarks) {
        foreach($bookmarks as $bookmark) {
          $bookmark_url = clean($bookmark['varchar_field_1']);

          $bookmark_placeholder = '<p>URL: <a href="' . clean($bookmark_url) . '" target="_blank">' . clean($bookmark_url) . '</a></p>';

          $this->execute("UPDATE $project_objects SET type = 'Discussion', category_id = NULL, body = ?, varchar_field_1 = NULL WHERE id = ?", $bookmark_placeholder . $bookmark['body'], $bookmark['id']);
          $this->execute("UPDATE $modification_logs SET parent_type = 'Discussion' WHERE parent_type = 'Bookmark'");
          $this->execute("UPDATE $comments SET parent_type = 'Discussion' WHERE parent_type = 'Bookmark'");
          $this->execute("UPDATE $subscriptions SET parent_type = 'Discussion' WHERE parent_type = 'Bookmark'");
          $this->execute("UPDATE $reminders SET parent_type = 'Discussion' WHERE parent_type = 'Bookmark'");
        } // foreach
      } // if

      $this->doneUsingTables('project_objects', 'modification_logs', 'comments', 'subscriptions', 'reminders');
    } // up

  }