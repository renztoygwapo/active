<?php

  // Include test objects classes
  require_once __DIR__ . "/test_objects/BaseTestObject.class.php";
  require_once __DIR__ . "/test_objects/BaseTestObjects.class.php";
  require_once __DIR__ . "/test_objects/TestObject.class.php";
  require_once __DIR__ . "/test_objects/TestObjects.class.php";

  /**
   * Test notifications
   *
   * @package angie.frameworks.notifications
   * @subpackage tests
   */
  class TestNotifications extends AngieModelTestCase {

    /**
     * Test objects table instance
     *
     * @var DBTable
     */
    protected $test_objects_table;

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();

      AngieApplication::mailer()->setAdapter(new SilentMailerAdapter());
      AngieApplication::mailer()->setDefaultSender(new AnonymousUser('Default From', 'default@from.com'));
      AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
      AngieApplication::mailer()->connect();

      $this->test_objects_table = DB::createTable('test_objects');

      if($this->test_objects_table->exists(TABLE_PREFIX)) {
        $this->test_objects_table->delete(TABLE_PREFIX);
      } // if

      $this->test_objects_table->addColumns(array(
        DBIdColumn::create(),
        DBNameColumn::create(),
        DBTextColumn::create('body'),
      ));

      $this->test_objects_table->save(TABLE_PREFIX);
    } // setUp

    /**
     * Tear down test case
     */
    function tearDown() {
      AngieApplication::mailer()->disconnect();

      if($this->test_objects_table->exists()) {
        $this->test_objects_table->delete();
      } // if

      parent::tearDown();
    } // tearDown

    /**
     * Test consutrion of notification classes based on event names
     */
    function testFactory() {
      $notification = AngieApplication::notifications()->notifyAbout('notifications/test');

      $this->assertIsA($notification, 'TestNotification');

      $reflection = new ReflectionClass($notification);

      $this->assertEqual($reflection->getFileName(), NOTIFICATIONS_FRAMEWORK_PATH . '/notifications/TestNotification.class.php');
    } // testFactory

    /**
     * Test get template by channel
     */
    function testGetTemplate() {
      $notification = AngieApplication::notifications()->notifyAbout('notifications/test');

      $this->assertIsA($notification, 'TestNotification');

      $template_path = $notification->getTemplatePath(new EmailNotificationChannel());

      $this->assertEqual($template_path, NOTIFICATIONS_FRAMEWORK_PATH . '/notifications/email/test.tpl');
    } // testGetTemplate

    /**
     * Test open / close notification channels
     */
    function testChannelsOpenClose() {
      $channels = AngieApplication::notifications()->getChannels();

      $this->assertTrue(is_array($channels));
      $this->assertEqual(count($channels), 2);
      $this->assertIsA($channels[0], 'WebInterfaceNotificationChannel');
      $this->assertIsA($channels[1], 'EmailNotificationChannel');

      $this->assertFalse(AngieApplication::notifications()->channelsAreOpen());

      AngieApplication::notifications()->openChannels();

      $this->assertTrue(AngieApplication::notifications()->channelsAreOpen());

      AngieApplication::notifications()->closeChannels();

      $this->assertFalse(AngieApplication::notifications()->channelsAreOpen());
    } // testChannelsOpenClose

    /**
     * Test notification send
     */
    function testSend() {
      $notification = AngieApplication::notifications()->notifyAbout('notifications/test')->sendToUsers(array(
        Users::findById(1),
        new AnonymousUser('Test User', 'test@user.com'),
      ));

      $this->assertIsA($notification, 'TestNotification');
      $this->assertTrue($notification->isLoaded());

      $recipients = $notification->getRecipients();

      $this->assertIsA($recipients, 'DBResult');
      $this->assertEqual($recipients->count(), 1);

      $this->assertIsA($recipients[0], 'User');
      $this->assertEqual($recipients[0]->getId(), 1);
    } // testSend

  }