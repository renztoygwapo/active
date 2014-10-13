<?php

  /**
   * Framework level announcement implementation
   *
   * @package angie.frameworks.announcements
   * @subpackage models
   */
  abstract class FwAnnouncement extends BaseAnnouncement {

    // Announce constants
    const ANNOUNCE_ANNOUNCEMENT = 'announcement';
    const ANNOUNCE_BUG = 'bug';
    const ANNOUNCE_COMMENT = 'comment';
    const ANNOUNCE_EVENT = 'event';
    const ANNOUNCE_IDEA = 'idea';
    const ANNOUNCE_INFO = 'info';
    const ANNOUNCE_JOKE = 'joke';
    const ANNOUNCE_NEWS = 'news';
    const ANNOUNCE_QUESTION = 'question';
    const ANNOUNCE_STAR = 'star';
    const ANNOUNCE_WARNING = 'warning';
    const ANNOUNCE_WELCOME = 'welcome';

    // Announce target type constants
    const ANNOUNCE_TARGET_TYPE_ROLE = 'role';
    const ANNOUNCE_TARGET_TYPE_COMPANY = 'company';
    const ANNOUNCE_TARGET_TYPE_USER = 'user';

    // Announce expiration type constants
    const ANNOUNCE_EXPIRATION_TYPE_NEVER = 'never';
    const ANNOUNCE_EXPIRATION_TYPE_UNTIL_DISMISSED = 'until_dismissed';
    const ANNOUNCE_EXPIRATION_TYPE_ON_DAY = 'on_day';

    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------

    /**
     * Count users who can see $this announcement
     *
     * @param void
     * @return integer
     */
    function getVisibleToCount() {
      $users_table = TABLE_PREFIX . 'users';

      $target_type = $this->getTargetType();
      switch($target_type) {
        case self::ANNOUNCE_TARGET_TYPE_ROLE:
          $target_field = 'type';
          break;
        case self::ANNOUNCE_TARGET_TYPE_COMPANY:
          $target_field = 'company_id';
          break;
        case self::ANNOUNCE_TARGET_TYPE_USER:
          $target_field = 'id';
          break;
        default:
          throw new InvalidParamError('target_type', $target_type, "Announcement target type '$target_type' is not defined");
      } // switch

      $target_ids = $this->getTargetIds();

      return $target_ids ? (integer) DB::executeFirstCell("SELECT COUNT(id) FROM $users_table WHERE $target_field IN (?) AND state >= ?", $target_ids, STATE_VISIBLE) : 0;
    } // getVisibleToCount

    /**
     * Count users who dismissed $this announcement
     *
     * @param void
     * @return integer
     */
    function getDismissedByCount() {
      return (integer) DB::executeFirstCell('SELECT COUNT(DISTINCT user_id) FROM ' . TABLE_PREFIX . 'announcement_dismissals WHERE announcement_id = ?', $this->getId());
    } // getDismissedByCount

    /**
     * Override a default getter due to different expiration types
     *
     * @return string
     */
    function getExpiresOn() {
      $expiration_type = $this->getExpirationType();
      switch($expiration_type) {
        case self::ANNOUNCE_EXPIRATION_TYPE_NEVER:
          $expires_on = 'Never';
          break;
        case self::ANNOUNCE_EXPIRATION_TYPE_UNTIL_DISMISSED:
          $expires_on = 'Until dismissed';
          break;
        case self::ANNOUNCE_EXPIRATION_TYPE_ON_DAY:
          AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
          $expires_on = smarty_modifier_date(parent::getExpiresOn());
          break;
        default:
          throw new InvalidParamError('expiration_type', $expiration_type, "Announcement expiration type '$expiration_type' is not defined");
      } // switch

      return $expires_on;
    } // getExpiresOn

    /**
     * Format data for show to selection helper
     *
     * @return array
     */
    function getShowTo() {
      $show_to = array();

      $target_type = $this->getTargetType();

      $show_to['target_type'] = $target_type;
      $show_to[$target_type] = $this->getTargetIds();

      return $show_to;
    } // getShowTo

    /**
     * Format data for expiration selection helper
     *
     * @return array
     */
    function getExpiration() {
      $expiration = array();

      $expiration_type = $this->getExpirationType();

      $expiration['type'] = $expiration_type;
      if($expiration_type == self::ANNOUNCE_EXPIRATION_TYPE_ON_DAY) { $expiration['date'] = $this->getExpiresOn(); } // if

      return $expiration;
    } // getExpiration

    /**
     * Get target IDs
     *
     * @return array
     */
    function getTargetIds() {
      return DB::executeFirstColumn('SELECT DISTINCT target_id FROM ' . TABLE_PREFIX . 'announcement_target_ids WHERE announcement_id = ?', $this->getId());
    } // getTargetIds

    /**
     * Set target IDs
     *
     * @param array $target_ids
     */
    function setTargetIds($target_ids) {
      if(is_foreachable($target_ids)) {
        try {
          DB::beginWork('Set target IDs @ ' . __CLASS__);

          $announcement_target_ids_table = TABLE_PREFIX . 'announcement_target_ids';

          $to_insert = array();
          foreach($target_ids as $target_id) {
            $to_insert[] = DB::prepare('(?, ?)', $this->getId(), $target_id);
          } // foreach

          if(count($to_insert)) {
            DB::execute("INSERT INTO $announcement_target_ids_table (announcement_id, target_id) VALUES " . implode(', ', $to_insert));
          } // if

          DB::commit('Target IDs set @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to set target IDs @ ' . __CLASS__);
          throw $e;
        } // try
      } // if
    } // setTargetIds

    /**
     * Returns true if $user is dismissed $this announcement
     *
     * @param IUser $user
     * @return boolean
     */
    function isDismissedByUser(IUser $user) {
      return (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'announcement_dismissals WHERE announcement_id = ? AND user_id = ?', $this->getId(), $user->getId());
    } // isDismissedByUser

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can update $this announcement
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit

    /**
     * Returns true if $user can change announcement status (enable or disable)
     *
     * @param User $user
     * @return boolean
     */
    function canChangeStatus(User $user) {
      return $user->isAdministrator();
    } // canChangeStatus

    /**
     * Returns true if $user can dismiss announcement
     *
     * @param User $user
     * @return boolean
     */
    function canDismiss(User $user) {
      return !$this->isDismissedByUser($user);
    } // canDismiss

    /**
     * Returns true if $user can delete $this announcement
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator();
    } // canDelete

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return edit announcement URL
     *
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('admin_announcement_edit', array(
        'announcement_id' => $this->getId()
      ));
    } // getEditUrl

    /**
     * Return enable announcement URL
     *
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('admin_announcement_enable', array(
        'announcement_id' => $this->getId()
      ));
    } // getEnableUrl

    /**
     * Return disable announcement URL
     *
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('admin_announcement_disable', array(
        'announcement_id' => $this->getId()
      ));
    } // getDisableUrl

    /**
     * Return delete announcement URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('admin_announcement_delete', array(
        'announcement_id' => $this->getId()
      ));
    } // getDeleteUrl

    /**
     * Return dismiss announcement URL
     *
     * @return string
     */
    function getDismissUrl() {
      return Router::assemble('announcement_dismiss', array(
        'announcement_id' => $this->getId()
      ));
    } // getDismissUrl

    /**
     * Return small announcement icon URL
     *
     * @return string
     */
    function getSmallIconUrl() {
      return AngieApplication::getImageUrl('icons/16x16/' . $this->getIcon() . '.png', ANNOUNCEMENTS_FRAMEWORK);
    } // getSmallIconUrl

    /**
     * Return large announcement icon URL
     *
     * @return string
     */
    function getLargeIconUrl() {
      return AngieApplication::getImageUrl('icons/48x48/' . $this->getIcon() . '.png', ANNOUNCEMENTS_FRAMEWORK);
    } // getLargeIconUrl

    // ---------------------------------------------------
    //  System
    // --------------------------------------------------

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      $result['subject'] = $this->getSubject();
      $result['body'] = $this->getBody();
      $result['icon_url'] = $this->getSmallIconUrl();
      $result['visible_to_count'] = $this->getVisibleToCount();
      $result['dismissed_by_count'] = $this->getDismissedByCount();
      $result['expires_on'] = $this->getExpiresOn();
      $result['created_by'] = $this->getCreatedBy()->describe($user, false, $for_interface);
      $result['is_enabled'] = $this->getIsEnabled();

      AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');
      $result['created_by']['created_ago'] = smarty_modifier_ago($this->getCreatedOn());

      $result['urls']['edit'] = $this->getEditUrl();
      $result['urls']['enable'] = $this->getEnableUrl();
      $result['urls']['disable'] = $this->getDisableUrl();
      $result['urls']['delete'] = $this->getDeleteUrl();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi

    /**
     * Dismiss announcement for a given user
     *
     * @param IUser $user
     */
    function dismiss(IUser $user) {
      try {
        DB::beginWork('Dismissing announcement @ ' . __CLASS__);

        DB::execute('INSERT INTO ' . TABLE_PREFIX . 'announcement_dismissals (announcement_id, user_id) VALUES (?, ?)', $this->getId(), $user->getId());

        DB::commit('Announcement dismissed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to dismiss announcement @ ' . __CLASS__);
        throw $e;
      } // try
    } // dismiss

    /**
     * Reset target IDs and dismissals upon announcement edit
     *
     * @param array $target_ids
     * @return boolean
     */
    function save($target_ids = null) {
      if(!$this->isNew() && !is_null($target_ids)) {
        if($this->isModifiedField('target_type') || $this->areTargetIdsModified($target_ids, $this->getTargetIds())) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'announcement_target_ids WHERE announcement_id = ?', $this->getId());
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'announcement_dismissals WHERE announcement_id = ?', $this->getId());

          $this->setTargetIds($target_ids);
        } // if
      } // if

      return parent::save();
    } // save

    /**
     * Check whether existing target IDs are changed
     *
     * @param array $new_ids
     * @param array $existing_ids
     * @return boolean
     */
    function areTargetIdsModified($new_ids, $existing_ids) {
      if(is_foreachable($new_ids) && is_foreachable($existing_ids)) {
        if(count($new_ids) != count($existing_ids)) {
          return true;
        } // if

        foreach($new_ids as $new_id) {
          if(in_array($new_id, $existing_ids)) {
            continue;
          } else {
            return true;
          } // if
        } // foreach
      } else {
        return true;
      } // if

      return false;
    } // areTargetIdsModified

    /**
     * Remove announcement from database
     *
     * @return boolean
     */
    function delete() {
      try {
        DB::beginWork('Removing announcement from database @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'announcement_target_ids WHERE announcement_id = ?', $this->getId());
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'announcement_dismissals WHERE announcement_id = ?', $this->getId());

        parent::delete();

        DB::commit('Announcement removed from database @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove announcement from database @ ' . __CLASS__);
        throw $e;
      } // try
    } // delete

  }