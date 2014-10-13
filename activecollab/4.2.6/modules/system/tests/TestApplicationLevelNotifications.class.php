<?php

  /**
   * Application level notifications test
   *
   * @package activeCollab.modules.system
   * @subpackage tests
   */
  class TestApplicationLevelNotifications extends AngieModelTestCase {

    /**
     * Administrator instance
     *
     * @var Administrator
     */
    private $administrator;

    /**
     * Member instance
     *
     * @var Member
     */
    private $member;

    /**
     * Test project
     *
     * @var Project
     */
    private $project;

    /**
     * Test milestone
     *
     * @var Milestone
     */
    private $milestone;

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();

      AngieApplication::mailer()->setAdapter(new SilentMailerAdapter());
      AngieApplication::mailer()->setDefaultSender(new AnonymousUser('Default From', 'default@from.com'));
      AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
      AngieApplication::mailer()->connect();

      $this->administrator = Users::findById(1);

      $this->member = new Member();

      $this->member->setCompanyId(1);
      $this->member->setEmail('email@a51dev.com');
      $this->member->setPassword('123');
      $this->member->save();

      $this->project = new Project();
      $this->project->setAttributes(array(
        'name' => 'Test Project',
        'leader_id' => 1,
        'company_id' => 1,
      ));
      $this->project->setState(STATE_VISIBLE);
      $this->project->save();

      $this->project->users()->add($this->member, null, array(
        'milestone' => ProjectRole::PERMISSION_MANAGE,
      ));

      $this->milestone = new Milestone();
      $this->milestone->setName('Test Milestone');
      $this->milestone->setProject($this->project);
      $this->milestone->setCreatedBy($this->administrator);
      $this->milestone->setState(STATE_VISIBLE);
      $this->milestone->save();

      $this->milestone->subscriptions()->subscribe($this->administrator);
      $this->milestone->subscriptions()->subscribe($this->member);
    } // setUp

    /**
     * Tear down test case
     */
    function tearDown() {
      AngieApplication::mailer()->disconnect();

      parent::tearDown();
    } // tearDown

    /**
     * Check test initialization
     */
    function testInitialization() {
      $this->assertTrue($this->administrator->isLoaded());
      $this->assertTrue($this->member->isLoaded());
      $this->assertTrue($this->project->isLoaded());
      $this->assertTrue($this->project->users()->isMember($this->member, false));
      $this->assertTrue(Milestones::canManage($this->member, $this->project));
      $this->assertTrue($this->milestone->isLoaded());
      $this->assertTrue($this->milestone->subscriptions()->isSubscribed($this->administrator, false));
      $this->assertTrue($this->milestone->subscriptions()->isSubscribed($this->member, false));
    } // testInitialization

    /**
     * Test sending a new comment
     */
    function testNewCommentWithDefaultSettings() {
      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      $comment = $this->milestone->comments()->submit('This is a comment', $this->member);

      $this->assertIsA($comment, 'Comment');

      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(id) FROM $notifications_table WHERE type = 'NewCommentNotification'"), 1);

      $notification = Notifications::findOneBySql("SELECT * FROM $notifications_table WHERE type = 'NewCommentNotification'");

      $this->assertIsA($notification, 'NewCommentNotification');
      $this->assertEqual($notification->getParentType(), 'Milestone');
      $this->assertEqual($notification->getParentId(), $this->milestone->getId());
      $this->assertEqual($notification->getComment()->getId(), $comment->getId());

      $recipients = DB::execute("SELECT * FROM $recipients_table WHERE notification_id = ?", $notification->getId());

      $this->assertIsA($recipients, 'DBResult');
      $recipients = $recipients->toArray();

      $this->assertEqual(count($recipients), 1);
      $this->assertEqual($recipients[0]['recipient_id'], $this->administrator->getId());
      $this->assertEqual($recipients[0]['recipient_email'], $this->administrator->getEmail());

      $email_notifications = DB::execute("SELECT * FROM " . TABLE_PREFIX . "mailing_activity_logs");

      $this->assertIsA($email_notifications, 'DBResult');
      $email_notifications = $email_notifications->toArray();

      $this->assertEqual(count($email_notifications), 1);
      $this->assertEqual($email_notifications[0]['to_id'], $this->administrator->getId());
      $this->assertEqual($email_notifications[0]['to_email'], $this->administrator->getEmail());
    } // testNewCommentWithDefaultSettings

    /**
     * Test sending a new comment when email is disabled by default
     */
    function testNewCommentWithEmailOffByDefault() {
      $email_channel = new EmailNotificationChannel();

      $email_channel->setEnabledByDefault(false);

      $email_channel = new EmailNotificationChannel();

      $this->assertFalse($email_channel->isEnabledByDefault());

      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      $comment = $this->milestone->comments()->submit('This is a comment', $this->member);

      $this->assertIsA($comment, 'Comment');

      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(id) FROM $notifications_table WHERE type = 'NewCommentNotification'"), 1);

      $notification = Notifications::findOneBySql("SELECT * FROM $notifications_table WHERE type = 'NewCommentNotification'");

      $this->assertIsA($notification, 'NewCommentNotification');
      $this->assertEqual($notification->getParentType(), 'Milestone');
      $this->assertEqual($notification->getParentId(), $this->milestone->getId());
      $this->assertEqual($notification->getComment()->getId(), $comment->getId());

      $recipients = DB::execute("SELECT * FROM $recipients_table WHERE notification_id = ?", $notification->getId());

      $this->assertIsA($recipients, 'DBResult');
      $recipients = $recipients->toArray();

      $this->assertEqual(count($recipients), 1);
      $this->assertEqual($recipients[0]['recipient_id'], $this->administrator->getId());
      $this->assertEqual($recipients[0]['recipient_email'], $this->administrator->getEmail());

      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "mailing_activity_logs"), 0);
    } // testNewCommentWithEmailOffByDefault

    /**
     * Test sending a new comment when email is disabled by default
     */
    function testNewCommentWithEmailOfForUser() {
      $email_channel = new EmailNotificationChannel();

      $email_channel->setEnabledFor($this->administrator, false);

      $email_channel = new EmailNotificationChannel();

      $this->assertTrue($email_channel->isEnabledByDefault());
      $this->assertFalse($email_channel->isEnabledFor($this->administrator));

      $notifications_table = TABLE_PREFIX . 'notifications';
      $recipients_table = TABLE_PREFIX . 'notification_recipients';

      $comment = $this->milestone->comments()->submit('This is a comment', $this->member);

      $this->assertIsA($comment, 'Comment');

      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(id) FROM $notifications_table WHERE type = 'NewCommentNotification'"), 1);

      $notification = Notifications::findOneBySql("SELECT * FROM $notifications_table WHERE type = 'NewCommentNotification'");

      $this->assertIsA($notification, 'NewCommentNotification');
      $this->assertEqual($notification->getParentType(), 'Milestone');
      $this->assertEqual($notification->getParentId(), $this->milestone->getId());
      $this->assertEqual($notification->getComment()->getId(), $comment->getId());

      $recipients = DB::execute("SELECT * FROM $recipients_table WHERE notification_id = ?", $notification->getId());

      $this->assertIsA($recipients, 'DBResult');
      $recipients = $recipients->toArray();

      $this->assertEqual(count($recipients), 1);
      $this->assertEqual($recipients[0]['recipient_id'], $this->administrator->getId());
      $this->assertEqual($recipients[0]['recipient_email'], $this->administrator->getEmail());

      $this->assertEqual((integer) DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "mailing_activity_logs"), 0);
    } // testNewCommentWithEmailOfForUser

  }