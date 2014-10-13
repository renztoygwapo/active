<?php

  /**
   * Update activeCollab 3.2.12 to activeCollab 3.2.13
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0061 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.12';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.13';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateCommentTypes' => 'Change ProjectObjectComment to their respective comment types',
      );
    } // getActions

    /**
     * Change ProjectObjectComment to their respective comment types
     *
     * @return bool|string
     */
    function updateCommentTypes() {
      $comments_table = TABLE_PREFIX . 'comments';

      try {
        if (DB::executeFirstCell("SELECT COUNT(*) FROM $comments_table WHERE type = ?", 'ProjectObjectComment')) {
          $comment_types = array(
            'DiscussionComment'   => 'Discussion',
            'AssetComment'        => array('File', 'Bookmark', 'TextDocument', 'YouTubeVideo'),
            'NotebookPageComment' => array('NotebookPage'),
            'MilestoneComment'    => array('Milestone'),
            'TaskComment'         => array('Task')
          );
          foreach ($comment_types as $comment_type => $parent_types) {
            DB::execute("UPDATE $comments_table SET type = ? WHERE type = ? AND parent_type IN (?)", $comment_type, 'ProjectObjectComment', $parent_types);
          } //foreach
        } //if
      } catch (Exception $e) {
        return $e->getMessage();
      } //try
      return true;
    } // updateCommentTypes

  }