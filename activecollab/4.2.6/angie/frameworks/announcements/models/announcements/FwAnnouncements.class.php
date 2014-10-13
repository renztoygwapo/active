<?php

  /**
   * Framework level announcements management implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage models
   */
  class FwAnnouncements extends BaseAnnouncements {

    /**
     * Return active announcements for a given user
     *
     * @param IUser $user
     * @return Announcement[]
     */
    static function findActiveByUser(IUser $user) {
      $announcements_table = TABLE_PREFIX . 'announcements';
      $announcement_target_ids_table = TABLE_PREFIX . 'announcement_target_ids';
      $announcement_dismissals_table = TABLE_PREFIX . 'announcement_dismissals';

      $dismissed_announcement_ids = DB::executeFirstColumn("SELECT DISTINCT $announcement_dismissals_table.announcement_id FROM $announcements_table JOIN $announcement_dismissals_table ON $announcements_table.id = $announcement_dismissals_table.announcement_id WHERE $announcements_table.expiration_type = ? AND $announcement_dismissals_table.user_id = ?", FwAnnouncement::ANNOUNCE_EXPIRATION_TYPE_UNTIL_DISMISSED, $user->getId());

      $additional_conditions = '';
      if($dismissed_announcement_ids) {
        $additional_conditions .= ' AND ' . DB::prepare("$announcements_table.id NOT IN (?)", array($dismissed_announcement_ids));
      } // if

      $today = new DateValue(time() + get_user_gmt_offset());

      return FwAnnouncements::findBySQL("SELECT $announcements_table.* FROM $announcements_table, $announcement_target_ids_table WHERE $announcements_table.id = $announcement_target_ids_table.announcement_id AND $announcements_table.is_enabled = ? AND IFNULL($announcements_table.expires_on, ?) >= ? AND (
        ($announcements_table.target_type = 'role' AND $announcement_target_ids_table.target_id = ?) OR
        ($announcements_table.target_type = 'company' AND $announcement_target_ids_table.target_id = ?) OR
        ($announcements_table.target_type = 'user' AND $announcement_target_ids_table.target_id = ?)
      ) $additional_conditions ORDER BY position ASC, created_on DESC", ANNOUNCEMENT_ENABLED, $today, $today, get_class($user), $user->getCompanyId(), $user->getId(), $additional_conditions);
    } // findActiveByUser

    /**
     * Return email notfication recipients by target type and ID-s
     *
     * @param string $target_type
     * @param array $target_ids
     * @return User[]
     */
    function findRecipientsByTarget($target_type, $target_ids) {
      $recipients = null;

      if(is_foreachable($target_ids)) {
        switch($target_type) {
          case FwAnnouncement::ANNOUNCE_TARGET_TYPE_ROLE:
            $recipients = Users::findByType($target_ids);
            break;
          case FwAnnouncement::ANNOUNCE_TARGET_TYPE_COMPANY:
            $recipients = Users::findByCompanyIds($target_ids);
            break;
          case FwAnnouncement::ANNOUNCE_TARGET_TYPE_USER:
            $recipients = Users::findByIds($target_ids);
            break;
        } // switch
      } // if

      return $recipients;
    } // findRecipientsByTarget

    /**
     * Return announcements slice based on given criteria
     *
     * @param integer $num
     * @param array $exclude
     * @param integer $timestamp
     * @return DBResult
     */
    static function getSlice($num = 10, $exclude = null, $timestamp = null) {
      if($exclude) {
        return self::find(array(
          'conditions' => array("id NOT IN (?)", $exclude),
          'order' => 'position ASC, created_on ASC',
          'limit' => $num,
        ));
      } else {
        return self::find(array(
          'order' => 'position ASC, created_on ASC',
          'limit' => $num,
        ));
      } // if
    } // getSlice

  }