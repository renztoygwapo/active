<?php

  /**
   * Test additional user addresses
   *
   * @package angie.frameworks.authentication
   * @subpackage tests
   */
  class TestAdditionalUserAddresses extends AngieModelTestCase {

    /**
     * Test user account
     *
     * @var User
     */
    private $test_user;

    /**
     * Set up test environment
     */
    function setUp() {
      parent::setUp();

      $this->test_user = new Administrator();
      $this->test_user->setAttributes(array(
        'email' => 'test-user@activecollab.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $this->test_user->setState(STATE_VISIBLE);
      $this->test_user->save();
    } // setUp

    /**
     * Test if test is properly set up
     */
    function testInitialization() {
      $this->assertTrue($this->test_user->isLoaded());
    } // testInitialization

    /**
     * Test get and set of additional addresses
     */
    function testGetAndSet() {
      $this->assertNull($this->test_user->getAdditionalEmailAddresses());
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 0);

      $this->test_user->setAdditionalEmailAddresses(array(
        'bbb@activecollab.com',
        'aaa@activecollab.com',
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 2);

      $copy = DataObjectPool::get('User', $this->test_user->getId(), null, true);

      if($copy instanceof User) {
        $this->assertEqual($copy->getAdditionalEmailAddresses(), array(
          'aaa@activecollab.com',
          'bbb@activecollab.com',
        ));
      } else {
        $this->fail('Fail to reload user');
      } // if
    } // testGetAndSet

    /**
     * Make sure that user's primary address is not recorded twice
     */
    function testSkippingPrimaryAddress() {
      $this->test_user->setAdditionalEmailAddresses(array(
        'TEST-USER@ACTIVECOLLAB.COM', // This should be skipped
        'bbb@activecollab.com', // This one should be recorded
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 1);
      $this->assertEqual($this->test_user->getAdditionalEmailAddresses(), array('bbb@activecollab.com'));
    } // testSkippingPrimaryAddress

    /**
     * Make sure that finder works properly
     */
    function testFinder() {
      $this->test_user->setAdditionalEmailAddresses(array(
        'bbb@activecollab.com', // This one should be recorded
      ));

      $this->assertNull(Users::findByEmail('bbb@activecollab.com'));
      $this->assertIsA(Users::findByEmail('bbb@activecollab.com', true), 'User');
    } // testFinder

    /**
     * Test how addresses change when user object changes state
     */
    function testStateChanges() {
      $this->test_user->setAdditionalEmailAddresses(array(
        'bbb@activecollab.com',
        'aaa@activecollab.com',
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 2);

      // Move to trash should not delete any addresses
      $this->test_user->state()->trash();
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 2);

      // Permanently delete should remove addresses
      $this->test_user->state()->delete();
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 0);
    } // testStateChanges

    /**
     * Test if addresses are properly cleaned up when force delete is called
     */
    function testForceDeleteCleanup() {
      $this->test_user->setAdditionalEmailAddresses(array(
        'bbb@activecollab.com',
        'aaa@activecollab.com',
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 2);

      // Force delete should clean up records
      $this->test_user->forceDelete();
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 0);
    } // testForceDeleteCleanup

    /**
     * Permanently delete test user
     */
    function testFindByEmail() {
      $this->assertEqual(Users::findByEmail('test-user@activecollab.com', true)->getId(), $this->test_user->getId());

      $this->test_user->state()->delete();

      $this->assertNull(Users::findByEmail('test-user@activecollab.com'));

      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'test-user@activecollab.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $this->assertEqual(Users::findByEmail('test-user@activecollab.com', true)->getId(), $second_user->getId());
    } // testFindByEmail

    /**
     * Test address in use
     */
    function testAddressInUse() {
      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@activecollab.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $second_user->setAdditionalEmailAddresses(array(
        '123@activecollab.com',
        '456@activecollab.com',
      ));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 2);

      try {
        $this->test_user->setAdditionalEmailAddresses(array(
          '123@activecollab.com'
        ));

        $this->fail('Address in use exception expected');
      } catch(InvalidParamError $e) {
        $this->pass('Caught address in use exception');
      } // try

      try {
        $this->test_user->setEmail('123@activecollab.com');
        $this->test_user->save();

        $this->fail('Address in use exception expected');
      } catch(Exception $e) {
        $this->pass('Caught address in use exception');
      } // try

      // Reload test user...
      $this->test_user = DataObjectPool::get('User', $this->test_user->getId(), true);

      if($this->test_user instanceof User) {
        $second_user->forceDelete();

        $this->test_user->setAdditionalEmailAddresses(array(
          '123@activecollab.com'
        ));

        $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'user_addresses'), 1);
        $this->assertEqual($this->test_user->getAdditionalEmailAddresses(), array(
          '123@activecollab.com'
        ));
      } else {
        $this->fail('User not reloaded');
      } // if
    } // testAddressInUse

  }