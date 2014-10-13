<?php

/**
 * Update activeCollab 3.1.8 to activeCollab 3.1.9
 *
 * @package activeCollab.upgrade
 * @subpackage scripts
 */
class Upgrade_0040 extends AngieApplicationUpgradeScript {

  /**
   * Initial system version
   *
   * @var string
   */
  protected $from_version = '3.1.8';

  /**
   * Final system version
   *
   * @var string
   */
  protected $to_version = '3.1.9';

  /**
   * Return script actions
   *
   * @return array
   */
  function getActions() {
    return array(
      'forceEvolutionTheme' => 'Force default theme',
      'fixRequiredBodies' => 'Fixing body field for objects that require it for validation',
      'dropNotificationStyle' => 'Drop notification style settings',
    );
  } // getActions

  /**
   * Force evolution theme
   *
   * @return true
   */
  function forceEvolutionTheme() {
    try {
      DB::beginWork('Forcing default theme @ ' . __CLASS__);

      DB::execute('UPDATE ' . TABLE_PREFIX . 'config_options SET value = ? WHERE name = ?', serialize('evolution'), 'theme');
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_option_values WHERE name = ?', 'theme');

      DB::commit('Default theme forced @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to force default theme @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // forceEvolutionTheme

  /**
   * Tries to fix objects that require body field for validation
   *
   * @return bool|string
   */
  function fixRequiredBodies() {
    try {
      DB::beginWork('Fixing empty bodies');

      DB::execute("UPDATE " . TABLE_PREFIX .  "project_objects SET name = ? WHERE name = '' OR name IS NULL", '[Name not provided]'); // update project object names if not specified
      DB::execute("UPDATE " . TABLE_PREFIX .  "project_objects SET body = ? WHERE type IN (?) AND (body = '' OR body IS NULL)", '[Content not provided]', array('Discussion', 'TextDocument')); // update discussions and text documents
      DB::execute("UPDATE " . TABLE_PREFIX .  "project_objects SET body = ? WHERE type = ? AND (body = '' OR body IS NULL)", '[Description not provided]', 'ProjectSourceRepository'); // source repositories
      DB::execute("UPDATE " . TABLE_PREFIX .  "comments SET body = ? WHERE (body = '' OR body IS NULL)", '[Content not provided]'); // comments
      DB::execute("UPDATE " . TABLE_PREFIX .  "subtasks SET body = ? WHERE (body = '' OR body IS NULL)", '[Content not provided]'); // subtasks

      DB::commit('Fixed empty bodies');
    } catch (Exception $e) {
      DB::rollback('Failed to fix empty bodies');

      return $e->getMessage();
    } // try

    return true;
  } // updateConfigOptions

  /**
   * Drop notification style settings
   *
   * @return bool|string
   */
  function dropNotificationStyle() {
    try {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name IN (?)', array('notification_header_style', 'notification_content_style', 'notification_footer_style'));
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // dropNotificationStyle
}