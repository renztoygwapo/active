<?php

  /**
   * Test notification seen and read flag manipulation
   *
   * @package angie.frameworks.notifications
   * @subpackage tests
   */
  class TestNotificationFlags extends AngieModelTestCase {

    /**
     * Logged in user
     *
     * @var User
     */
    private $logged_user;

    /**
     * Main test notification
     *
     * @var TestNotification
     */
    private $first_notification;

    /**
     * Set up test variables
     */
    function setUp() {
      parent::setUp();

      AngieApplication::mailer()->setAdapter(new SilentMailerAdapter());
      AngieApplication::mailer()->setDefaultSender(new AnonymousUser('Default From', 'default@from.com'));
      AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
      AngieApplication::mailer()->connect();

      $this->logged_user = Users::findById(1);
      $this->first_notification = AngieApplication::notifications()->notifyAbout('notifications/test')->sendToUsers(array(
        $this->logged_user,
      ));
    } // setUp

    function tearDown() {
      AngieApplication::mailer()->disconnect();

      $this->logged_user = null;
      $this->first_notification = null;
    } // tearDown

    /**
     * Make sure that all initial data is properly loaded
     */
    function testSetUp() {
      $this->assertTrue($this->logged_user->isLoaded(), 'We have user loaded');
      
      $this->assertIsA($this->first_notification, 'TestNotification');
      $this->assertTrue($this->first_notification->isLoaded());
    } // testSetUp

    /**
     * Test mark as read / unread model feature
     */
    function testMarkReadUnread() {
      $this->assertEqual(count($this->first_notification->getRecipients()), 1);
      $this->assertFalse($this->first_notification->isSeen($this->logged_user));
      $this->assertFalse($this->first_notification->isRead($this->logged_user));

      Notifications::markRead($this->first_notification, $this->logged_user);

      $this->assertTrue($this->first_notification->isSeen($this->logged_user));
      $this->assertTrue($this->first_notification->isSeen($this->logged_user, false));

      $this->assertTrue($this->first_notification->isRead($this->logged_user));
      $this->assertTrue($this->first_notification->isRead($this->logged_user, false));

      Notifications::markUnread($this->first_notification, $this->logged_user);

      $this->assertTrue($this->first_notification->isSeen($this->logged_user));
      $this->assertTrue($this->first_notification->isSeen($this->logged_user, false));

      $this->assertFalse($this->first_notification->isRead($this->logged_user));
      $this->assertFalse($this->first_notification->isRead($this->logged_user, false));
    } // testMarkReadUnread

    /**
     * Test mark all read
     */
    function testMarkAllRead() {
      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $second_notification = AngieApplication::notifications()->notifyAbout('notifications/test')->sendToUsers(array(
        $this->logged_user,
        $second_user,
      ));

      $this->assertIsA($second_notification, 'TestNotification');
      $this->assertTrue($second_notification->isLoaded());
      $this->assertEqual(count($second_notification->getRecipients()), 2);

      $this->assertEqual(Notifications::count(), 2);
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'notification_recipients WHERE read_on IS NULL'), 3);

      Notifications::updateReadStatusForRecipient($this->logged_user, true);

      $this->assertTrue($this->first_notification->isSeen($this->logged_user));
      $this->assertTrue($this->first_notification->isRead($this->logged_user));

      $this->assertTrue($this->first_notification->isSeen($this->logged_user, false));
      $this->assertTrue($this->first_notification->isRead($this->logged_user, false));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'notification_recipients WHERE read_on IS NULL'), 1);

      Notifications::updateReadStatusForRecipient($this->logged_user, false);

      $this->assertTrue($this->first_notification->isSeen($this->logged_user));
      $this->assertFalse($this->first_notification->isRead($this->logged_user));

      $this->assertTrue($this->first_notification->isSeen($this->logged_user, false));
      $this->assertFalse($this->first_notification->isRead($this->logged_user, false));

      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'notification_recipients WHERE read_on IS NULL'), 3);
    } // testMarkAllRead

    /**
     * Clear notifications for recipient
     */
    function testClearForRecipient() {
      $second_user = new Administrator();
      $second_user->setAttributes(array(
        'email' => 'second-user@test.com',
        'company_id' => 1,
        'password' => 'test',
      ));
      $second_user->setState(STATE_VISIBLE);
      $second_user->save();

      $this->assertTrue($second_user->isLoaded());

      $second_notification = AngieApplication::notifications()->notifyAbout('notifications/test')->sendToUsers(array(
        $this->logged_user,
        $second_user,
      ));

      $this->assertIsA($second_notification, 'TestNotification');
      $this->assertTrue($second_notification->isLoaded());
      $this->assertEqual(count($second_notification->getRecipients()), 2);

      $this->assertEqual(Notifications::count(), 2);
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'notification_recipients WHERE read_on IS NULL'), 3);

      Notifications::clearForRecipient($this->logged_user);

      $this->assertEqual(Notifications::count(), 2);
      $this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'notification_recipients WHERE read_on IS NULL'), 1);
    } // testClearForRecipient

  }