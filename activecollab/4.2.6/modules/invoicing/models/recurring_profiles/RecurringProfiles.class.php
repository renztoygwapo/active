<?php

  /**
   * RecurringProfiles class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class RecurringProfiles extends InvoiceObjects {

    /**
     * Returns true if $user can create new recurring profiles
     *
     * @param IUser $user
     * @return boolean
     */
    static function canAdd(IUser $user) {
      return Invoices::canAdd($user);
    } // canAdd

    /**
     * Returns true if $user can manage recurring invoices
     *
     * @param IUser $user
     * @return boolean
     */
    static function canManage(IUser $user) {
      return Invoices::canAdd($user);
    } // canManage

    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
    /**
     * Find skipped recurring profiles
     *
     * @return DBResult
     */
    static function findSkipped() {
      return RecurringProfiles::find(array(
        'conditions' => array("date_field_2 < ? AND type = 'RecurringProfile' AND state = ?", DateValue::now(), STATE_VISIBLE)
      ));
    } //findSkipped

    /**
     * Find recurring profiles to use for creating invoices
     *
     * @return DBResult
     */
    static function findMatchingForDay() {
      $today = new DateValue();
      return RecurringProfiles::find(array(
        'conditions' => array("(date_field_3 = ? OR date_field_2 = ?) AND state = ? AND (date_field_1 != ? OR date_field_1 IS NULL) AND type = 'RecurringProfile' ", $today, $today, STATE_VISIBLE, $today)
      ));
    } //findMetchingForDay

    /**
     * Find profiles by ids
     *
     * @param array $ids
     * @return mixed
     */
    static function findByIds($ids) {
      return Recurringprofiles::find(array(
        'conditions' => array("id IN (?) AND type = 'RecurringProfile'", $ids)
      ));
    } // findByIds

    /**
     * Find recurring profiles for objects list
     *
     * @param integer $min_state
     * @return array
     */
    static function findForObjectsList($min_state = STATE_VISIBLE) {
      $recurring_profile_url = Router::assemble('recurring_profile', array('recurring_profile_id' => '--RECURRINGPROFILEID--'));
      $profiles = DB::execute('SELECT id, company_id, name, state, date_field_2 as next_trigger_on FROM ' . TABLE_PREFIX . 'invoice_objects WHERE state = ? AND type = ?', $min_state, 'RecurringProfile');

      $result = array();
      if (is_foreachable($profiles)) {
        foreach ($profiles as $profile) {
          $result[] = array(
            'id' => $profile['id'],
            'name' => $profile['name'],
            'client_id'	=> $profile['company_id'],
            'permalink' => str_replace('--RECURRINGPROFILEID--', $profile['id'], $recurring_profile_url),
            'is_archived' => $profile['state'] == STATE_ARCHIVED ? '1' : '0',
            'is_skipped' => (strtotime(new DateValue()) > strtotime(new DateValue($profile['next_trigger_on'])) && $profile['state'] == STATE_VISIBLE) ? true : false
          );
        } // foreach
      } // if

      return $result;
    } // findForObjectsList

    /**
     * Find recurring profiles for phone list view
     *
     * @param integer $min_state
     * @return array
     */
    function findForPhoneList($min_state = STATE_VISIBLE) {
      $recurring_profiles_table = TABLE_PREFIX . 'invoice_objects';
      $companies_table = TABLE_PREFIX . 'companies';
      $recurring_profiles = DB::execute("SELECT $recurring_profiles_table.id, $companies_table.name AS company_name, $recurring_profiles_table.name FROM $recurring_profiles_table, $companies_table WHERE $recurring_profiles_table.company_id = $companies_table.id AND $recurring_profiles_table.state >= $min_state AND $recurring_profiles_table.type = 'RecurringProfile' ORDER BY company_name, $recurring_profiles_table.id");

      $view_recurring_profile_url_template = Router::assemble('recurring_profile', array('recurring_profile_id' => '--RECURRINGPROFILEID--'));

      $result = array();

      if(is_foreachable($recurring_profiles)) {
        foreach ($recurring_profiles as $recurring_profile) {
          $result[$recurring_profile['company_name']][] = array(
            'name' => $recurring_profile['name'],
            'permalink' => str_replace('--RECURRINGPROFILEID--', $recurring_profile['id'], $view_recurring_profile_url_template)
          );
        } // foreach
      } // if

      return $result;
    } // findForPhoneList

    /**
     * Find recurring profiles for printing by grouping and filtering criteria
     *
     * @param string $group_by
     * @return DBResult
     */
    public function findForPrint($group_by = null, $state) {
      if($group_by != 'company_id' && $group_by != 'status') {
        $group_by = null;
      } // if

      $profiles = RecurringProfiles::find(array(
        'conditions' => array('type = ? AND state = ?', 'RecurringProfile', $state),
        'order' => $group_by ? $group_by : 'id DESC'
      ));

      return $profiles;
    } // findForPrint

    /**
     * Get trashed map
     *
     * @param User $user
     * @return array
     */
    static function getTrashedMap($user) {
      return array(
        'recurring_profile' => DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'invoice_objects WHERE state = ? AND type = ? ORDER BY name DESC', STATE_TRASHED, 'RecurringProfile')
      );
    } // getTrashedMap

    /**
     * Find trashed recurring profiles
     *
     * @param User $user
     * @param array $map
     * @return array
     */
    static function findTrashed(User $user, &$map) {
      $trashed_recurring_profiles = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'invoice_objects WHERE state = ? AND type = ? ORDER BY name DESC', STATE_TRASHED, 'RecurringProfile');
      if ($trashed_recurring_profiles) {
        $view_url = Router::assemble('recurring_profile', array('recurring_profile_id' => '--RECURRING-PROFILE_ID--'));

        $items = array();
        foreach ($trashed_recurring_profiles as $trashed_recurring_profile) {
          $items[] = array(
            'id' => $trashed_recurring_profile['id'],
            'name' => $trashed_recurring_profile['name'],
            'type' => 'RecurringProfile',
            'permalink' => str_replace('--RECURRING-PROFILE_ID--', $trashed_recurring_profile['id'], $view_url),
            'can_be_parent' => false,
          );
        } // foreach

        return $items;
      } else {
        return null;
      } // if
    } // findTrashed

    /**
     * Delete trashed recurring profiles
     */
    static function deleteTrashed() {
      $recurring_profiles = RecurringProfiles::find(array(
        'conditions' => array('type = ? AND state = ?', 'RecurringProfile', STATE_TRASHED)
      ));

      if ($recurring_profiles) {
        foreach ($recurring_profiles as $recurring_profile) {
          $recurring_profile->state()->delete();
        } // foreach
      } // if

      return true;
    } // deleteTrashed

  }