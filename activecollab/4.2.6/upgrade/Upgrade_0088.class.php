<?php

  /**
   * Update activeCollab 4.0.11 to activeCollab 4.0.12
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0088 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '4.0.11';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '4.0.12';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateNamesSearchIndex' => 'Update names search index',
        'scheduleIndexesRebuild' => 'Schedule index rebuild',
      );
    } // getActions

    /**
     * Update names search index
     *
     * @return bool|string
     */
    function updateNamesSearchIndex() {
      try {
        DB::execute("DROP TABLE IF EXISTS " . TABLE_PREFIX . 'search_index_for_names');
        DB::execute("CREATE TABLE " . TABLE_PREFIX . "search_index_for_names (
          item_type varchar(50) NOT NULL default '',
          item_id int(10) unsigned NOT NULL default '0',
          item_context varchar(255) default NULL,
          name varchar(255) default NULL,
          short_name varchar(255) default NULL,
          visibility int(11) default NULL,
          PRIMARY KEY  (item_type,item_id),
          KEY item_context (item_context),
          KEY visibility (visibility),
          FULLTEXT KEY content (name,short_name)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateNamesSearchIndex

  }