<?php

  // Include test objects classes
  require_once __DIR__ . "/test_objects/BaseTestObject.class.php";
  require_once __DIR__ . "/test_objects/BaseTestObjects.class.php";
  require_once __DIR__ . "/test_objects/TestObject.class.php";
  require_once __DIR__ . "/test_objects/TestObjects.class.php";

  /**
   * Test notification channel settings
   *
   * @package angie.frameworks.notifications
   * @subpackage tests
   */
  class TestNotificationChannelSettings extends AngieModelTestCase {

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
     * Test default settings
     */
    function testDefaultSettings() {
      $administrator = Users::findById(1);

      $member = new Member();

      $member->setCompanyId(1);
      $member->setEmail('email@a51dev.com');
      $member->setPassword('123');
      $member->save();

      $client = new Client();

      $client->setCompanyId(1);
      $client->setEmail('client@a51dev.com');
      $client->setPassword('123');
      $client->save();

      $email_channel = new EmailNotificationChannel();

      $this->assertTrue($email_channel->isEnabledByDefault());
      $this->assertTrue($email_channel->isEnabledFor($administrator));
      $this->assertTrue($email_channel->isEnabledFor($member));
      $this->assertTrue($email_channel->isEnabledFor($client));

      $this->assertEqual($email_channel->whoCanOverrideDefaultStatus(), array('Member', 'Manager'));

      $this->assertTrue($email_channel->canOverrideDefaultStatus($administrator));
      $this->assertTrue($email_channel->canOverrideDefaultStatus($member));
      $this->assertFalse($email_channel->canOverrideDefaultStatus($client));
    } // testDefaultSettings

    /**
     * Test if particular types of notifications are visible in particular channels
     */
    function testChannelVisibility() {
      $administrator = Users::findById(1);

      $web_interface_channel = new WebInterfaceNotificationChannel();
      $email_channel = new EmailNotificationChannel();

      $new_comment_notification = new NewCommentNotification();

      $this->assertTrue($new_comment_notification->isThisNotificationVisibleInChannel($web_interface_channel, $administrator));
      $this->assertTrue($new_comment_notification->isThisNotificationVisibleInChannel($email_channel, $administrator));

      $forgot_password_notification = new ForgotPasswordNotification();

      $this->assertFalse($forgot_password_notification->isThisNotificationVisibleInChannel($web_interface_channel, $administrator));
      $this->assertTrue($forgot_password_notification->isThisNotificationVisibleInChannel($email_channel, $administrator));
    } // testChannelVisibility

    /**
     * Test notification override
     */
    function testOverride() {
      $email_channel = new EmailNotificationChannel();
      $web_interface_channel = new WebInterfaceNotificationChannel();

      $new_comment_notification = new NewCommentNotification();
      $forgot_password_notification = new ForgotPasswordNotification();

      $member = new Member();

      $member->setCompanyId(1);
      $member->setEmail('email@a51dev.com');
      $member->setPassword('123');
      $member->save();

      $this->assertTrue($email_channel->isEnabledFor($member));
      $this->assertTrue($email_channel->canOverrideDefaultStatus($member));

      $email_channel->setEnabledFor($member, false);

      $this->assertFalse($email_channel->isEnabledFor($member));

      // Show new comment in web interface, but skip email because user turned off email channel
      $this->assertTrue($new_comment_notification->isThisNotificationVisibleInChannel($web_interface_channel, $member));
      $this->assertFalse($new_comment_notification->isThisNotificationVisibleInChannel($email_channel, $member));

      // Default, forced behavior - never show in web interface, but always send email, regardless of user settings
      $this->assertFalse($forgot_password_notification->isThisNotificationVisibleInChannel($web_interface_channel, $member));
      $this->assertTrue($forgot_password_notification->isThisNotificationVisibleInChannel($email_channel, $member));
    } // testOverride

    /**
     * Test notification override
     */
    function testEmailMentions() {
      $email_channel = new EmailNotificationChannel();
      $web_interface_channel = new WebInterfaceNotificationChannel();

      require_once NOTIFICATIONS_FRAMEWORK_PATH . '/notifications/TestNotification.class.php';

      $test_notification = new TestNotification();

      $member = new Member();

      $member->setCompanyId(1);
      $member->setEmail('email@a51dev.com');
      $member->setPassword('123');
      $member->save();

      $this->assertTrue($email_channel->isEnabledFor($member));
      $this->assertTrue($email_channel->canOverrideDefaultStatus($member));

      $email_channel->setEnabledFor($member, false);

      $this->assertFalse($email_channel->isEnabledFor($member));

      // Show new comment in web interface, but skip email because user turned off email channel
      $this->assertTrue($test_notification->isThisNotificationVisibleInChannel($web_interface_channel, $member));
      $this->assertTrue($test_notification->isThisNotificationVisibleInChannel($email_channel, $member));
    } // testEmailMentions

  }