<?php

  /**
   * Subscriptions framework model definition
   *
   * @package angie.frameworks.subscriptions
   * @subpackage resources
   */
  class SubscriptionsFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Construct subscriptions framework model definition
     *
     * @param SubscriptionsFramework $parent
     */
    function __construct(SubscriptionsFramework $parent) {
      parent::__construct($parent);
      
      $this->addModel(DB::createTable('subscriptions')->addColumns(array(
        DBIdColumn::create(), 
        DBParentColumn::create(), 
        DBUserColumn::create('user'), 
        DBDateTimeColumn::create('subscribed_on'), 
        DBStringColumn::create('code', 10), 
      ))->addIndices(array(
        DBIndex::create('user_subscribed', DBIndex::UNIQUE, array('user_email', 'parent_type', 'parent_id')), 
        DBIndex::create('subscribed_on', DBIndex::KEY, 'subscribed_on'), 
      )));
    } // __construct
    
  }