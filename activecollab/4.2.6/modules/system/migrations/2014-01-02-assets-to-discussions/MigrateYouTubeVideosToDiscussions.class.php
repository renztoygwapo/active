<?php

  /**
   * Migrate YouTube videos to discussions
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateYouTubeVideosToDiscussions extends AngieModelMigration {

    /**
     * Upgrade the data
     */
    function up() {
      list($project_objects, $modification_logs, $comments, $subscriptions, $reminders) = $this->useTables('project_objects', 'modification_logs', 'comments', 'subscriptions', 'reminders');

      $youtube_videos = $this->execute("SELECT id, body, varchar_field_1 FROM $project_objects WHERE type = 'YouTubeVideo'");
      if($youtube_videos) {
        foreach($youtube_videos as $youtube_video) {
          $video_placeholder = '<div class="placeholder" placeholder-type="video" placeholder-extra="youtube" placeholder-object-id="' . clean($this->getVideoIdFromUrl($youtube_video['varchar_field_1'])) . '"></div>';

          $this->execute("UPDATE $project_objects SET type = 'Discussion', category_id = NULL, body = ?, varchar_field_1 = NULL WHERE id = ?", $video_placeholder . $youtube_video['body'], $youtube_video['id']);
          $this->execute("UPDATE $modification_logs SET parent_type = 'Discussion' WHERE parent_type = 'YouTubeVideo'");
          $this->execute("UPDATE $comments SET parent_type = 'Discussion' WHERE parent_type = 'YouTubeVideo'");
          $this->execute("UPDATE $subscriptions SET parent_type = 'Discussion' WHERE parent_type = 'YouTubeVideo'");
          $this->execute("UPDATE $reminders SET parent_type = 'Discussion' WHERE parent_type = 'YouTubeVideo'");
        } // foreach
      } // if

      $this->doneUsingTables('project_objects', 'modification_logs', 'comments', 'subscriptions', 'reminders');
    } // up

    /**
     * Checks if provided url is valid youtube url (does not check if youtube video really exists)
     *
     * @param string $url
     * @return mixed
     */
    function getVideoIdFromUrl($url) {
      $parsed = parse_url($url);

      $basedomain = strtolower(trim($parsed['host']));
      if (!$basedomain) {
        return false;
      } // if

      // extract basedomain
      $basedomain_parts = explode('.', $basedomain);
      $basedomain_parts_num = count($basedomain_parts);
      if ($basedomain_parts_num < 2) {
        return false;
      } // if

      // hack for co.uk domain
      if ($basedomain_parts[$basedomain_parts_num - 2] == 'co' && $basedomain_parts[$basedomain_parts_num - 1] == 'uk') {
        if ($basedomain_parts_num < 3) {
          return false;
        } // if
        $basedomain = $basedomain_parts[$basedomain_parts_num - 3] . '.' . $basedomain_parts[$basedomain_parts_num - 2] . '.' . $basedomain_parts[$basedomain_parts_num - 1];
      } else {
        $basedomain = $basedomain_parts[$basedomain_parts_num - 2] . '.' . $basedomain_parts[$basedomain_parts_num - 1];
      } // if

      // domain is not in list of supported domains
      if (!in_array($basedomain, $this->getDomains())) {
        return false;
      } // if

      // shortened url
      if ($basedomain == 'youtu.be') {
        $path = trim(array_var($parsed, 'path'));

        if (!$path || strlen($path) < 2) {
          return false;
        } // if

        $video_id = trim(substr($path, 1));
        if (!$video_id) {
          return  false;
        } // if

        return $video_id;
      } else {
        $query = trim($parsed['query']);
        parse_str($query, $parsed_query);
        $video_id = array_var($parsed_query, 'v', null);
        if (!$video_id) {
          return false;
        } // if
        return $video_id;
      } // if
    } // getVideoIdFromUrl

    /**
     * Returns all supported youtube domains
     *
     * @return array
     */
    private function getDomains() {
      return array(
        'youtube.com',
        'youtube.co.uk',
        'youtube.br',
        'youtube.fr',
        'youtube.it',
        'youtube.jp',
        'youtube.nl',
        'youtube.pl',
        'youtube.es',
        'youtube.ie',
        'youtu.be'
      );
    } // getDomains

  }