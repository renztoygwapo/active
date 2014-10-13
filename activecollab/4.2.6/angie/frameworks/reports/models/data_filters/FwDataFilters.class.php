<?php

  /**
   * Framework level data filters manager
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwDataFilters extends BaseDataFilters {

    /**
     * Returns true if $user can create a new filter
     *
     * @param string $type
     * @param User $user
     * @return boolean
     * @throws InvalidParamError
     */
    static function canAdd($type, User $user) {
      if(empty($type)) {
        throw new InvalidParamError('type', $type, '$type value is required');
      } // if

      return $user->canUseReports();
    } // canAdd

    /**
     * Returns true if $user can manage assignment filters
     *
     * @param string $type
     * @param User $user
     * @return bool
     * @throws InvalidParamError
     */
    static function canManage($type, User $user) {
      if(empty($type)) {
        throw new InvalidParamError('type', $type, '$type value is required');
      } // if

      return $user->canUseReports();
    } // canManage

    // ---------------------------------------------------
    //  Finters
    // ---------------------------------------------------

    /**
     * Return saved data filters by given type
     *
     * @param string $type
     * @return DataFilter[]
     */
    static function findByType($type) {
      return DataFilters::find(array(
        'conditions' => array('type = ?', $type),
        'order' => 'name',
      ));
    } // findByType

    /**
     * Return filters of given type that $user can see
     *
     * @param string $type
     * @param User $user
     * @return DataFilter[]
     */
    static function findByUser($type, User $user) {
      return DataFilters::find(array(
        'conditions' => array('type = ? AND (is_private = ? OR (created_by_id = ? AND is_private = ?))', $type, false, $user->getId(), true),
        'order' => 'name'
      ));
    } // findByUser

    /**
     * Return ID name map of filters that $user can see
     *
     * @param string $type
     * @param User $user
     * @return array
     */
    static function getIdNameMap($type, User $user) {
      $rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'data_filters WHERE type = ? AND (is_private = ? OR (created_by_id = ? AND is_private = ?)) ORDER BY name', $type, false, $user->getId(), true);

      if($rows) {
        $result = array();

        foreach($rows as $row) {
          $result[(integer) $row['id']] = $row['name'];
        } // foreach

        return $result;
      } else {
        return null;
      } // if
    } // getIdNameMap

  }