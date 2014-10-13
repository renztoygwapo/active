<?php

  /**
   * Announcements framework model definition
   *
   * @package angie.frameworks.announcements
   * @subpackage resources
   */
  class AnnouncementsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct announcements framework model
     * 
     * @param AnnouncementsFramework $parent
     */
    function __construct(AnnouncementsFramework $parent) {
      parent::__construct($parent);

      // Announcements
      $this->addModel(DB::createTable('announcements')->addColumns(array(
        DBIdColumn::create(),
        DBStringColumn::create('subject', 255),
        DBTextColumn::create('body')->setSize(DBColumn::BIG),
        DBIntegerColumn::create('body_type', 3, 0)->setSize(DBColumn::TINY),
        DBEnumColumn::create('icon', array('announcement', 'bug', 'comment', 'event', 'idea', 'info', 'joke', 'news', 'question', 'star', 'warning', 'welcome'), 'announcement'),
        DBStringColumn::create('target_type', 50),
        DBStringColumn::create('expiration_type', 50),
        DBDateColumn::create('expires_on'),
        DBActionOnByColumn::create('created'),
        DBBoolColumn::create('is_enabled', false),
        DBIntegerColumn::create('position', 10, 0)->setUnsigned(true)
      ))->addIndices(array(
        DBIndex::create('subject')
      )));

      // Announcement target IDs
      $this->addTable(DB::createTable('announcement_target_ids')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('announcement_id', 10, '0')->setUnsigned(true),
        DBStringColumn::create('target_id', 50, 0)
      )));

      // Announcement dismissals
      $this->addTable(DB::createTable('announcement_dismissals')->addColumns(array(
        DBIdColumn::create(),
        DBIntegerColumn::create('announcement_id', 10, '0')->setUnsigned(true),
        DBIntegerColumn::create('user_id', 10, '0')->setUnsigned(true)
      )));
    } // __construct
    
  }