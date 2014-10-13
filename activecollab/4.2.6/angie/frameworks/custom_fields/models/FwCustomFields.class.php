<?php

  /**
   * Custom fields management class
   *
   * @package angie.frameworks.custom_fields
   * @subpackage models
   */
  abstract class FwCustomFields {

    /**
     * Cached custom fields by type list
     *
     * @var array
     */
    private static $custom_fields_by_type = array();

    /**
     * Return list of custom fields and their settings by type
     *
     * @param string $type
     * @return array
     */
    static function getCustomFieldsByType($type) {
      if(!isset(self::$custom_fields_by_type[$type])) {
        self::$custom_fields_by_type[$type] = array();

        $rows = DB::execute('SELECT field_name, label, is_enabled FROM ' . TABLE_PREFIX . 'custom_fields WHERE parent_type = ? ORDER BY field_name', $type);
        if($rows) {
          foreach($rows as $row) {
            self::$custom_fields_by_type[$type][$row['field_name']] = array(
              'label' => $row['label'],
              'is_enabled' => (boolean) $row['is_enabled'],
            );
          } // foreach
        } // if
      } // if

      return self::$custom_fields_by_type[$type];
    } // getCustomFieldsByType

    /**
     * Save custom fields settings
     *
     * @param string $type
     * @param array $settings
     */
    static function setCustomFieldsByType($type, $settings) {
      $existing_settings = self::getCustomFieldsByType($type);

      if(is_array($existing_settings) && count($existing_settings) > 0) {
        foreach($existing_settings as $field_name => $details) {
          if(isset($settings[$field_name]) && $settings[$field_name]['is_enabled']) {
            $label = isset($settings[$field_name]['label']) && trim($settings[$field_name]['label']) ? trim($settings[$field_name]['label']) : self::getSafeFieldLabel($field_name);

            DB::execute('UPDATE ' . TABLE_PREFIX . 'custom_fields SET label = ?, is_enabled = 1 WHERE field_name = ? AND parent_type = ?', $label, $field_name, $type);
          } else {
            DB::execute('UPDATE ' . TABLE_PREFIX . 'custom_fields SET label = NULL, is_enabled = 0 WHERE field_name = ? AND parent_type = ?', $field_name, $type);

            EventsManager::trigger('on_custom_field_disabled', array($type, $field_name));
          } // if
        } // if
      } // if

      unset(self::$custom_fields_by_type[$type]);
    } // setCustomFieldsByType

    /**
     * Return enabled custom fields by type
     *
     * @param string $type
     * @return array
     */
    static function getEnabledCustomFieldsByType($type) {
      $result = array();

      foreach(CustomFields::getCustomFieldsByType($type) as $field => $details) {
        if($details['is_enabled']) {
          $result[$field] = $details;
        } // if
      } // foreach

      return $result;
    } // getEnabledCustomFieldsByType

    /**
     * Return safe field label, in case we have an empty name
     *
     * @param string $field_name
     * @return string
     */
    static function getSafeFieldLabel($field_name) {
      return ucfirst(str_replace('_', ' ', $field_name));
    } // getSafeFieldLabel

    /**
     * Initialize custom fields for the given type
     *
     * @param string $type
     * @param int $num
     */
    static function initForType($type, $num = 3) {
      $batch = new DBBatchInsert(TABLE_PREFIX . 'custom_fields', array('field_name', 'parent_type'));

      for($i = 1; $i <= $num; $i++) {
        $batch->insert("custom_field_{$i}", $type);
      } // for

      $batch->done();
    } // initForType

    /**
     * Drop definitions for given type
     *
     * @param string $type
     */
    static function dropForType($type) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'custom_fields WHERE parent_type = ?', $type);
    } // dropForType

  }