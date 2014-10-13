<?php

  /**
   * Test application mailer
   */
  class TestApplicationMailer extends AngieModelTestCase {
  	
  	/**
  	 * Set up test case
  	 */
  	function setUp() {
  		parent::setUp();
  		
  		AngieApplication::mailer()->setAdapter(new SilentMailerAdapter());
  		AngieApplication::mailer()->setDefaultSender(new AnonymousUser('Default From', 'default@from.com'));
  		AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
  		AngieApplication::mailer()->connect();
  	} // setUp
  	
  	/**
  	 * Tear down test case
  	 */
  	function tearDown() {
  		parent::tearDown();
  		
  		AngieApplication::mailer()->disconnect();
  	} // tearDown
  	
  	/**
  	 * Test mailing method
  	 */
  	function testMailingMethod() {
  		AngieApplication::mailer()->setDefaultMailingMethod(AngieMailerDelegate::SEND_HOURLY);
  		
  	  $anon = new AnonymousUser('Anon', 'user@site.com');
  	  $user = Users::findById(1);
  	  
  	  $this->assertEqual($anon->getMailingMethod(), AngieMailerDelegate::SEND_HOURLY, 'Anonymous users always use default mailing method');
  	  $this->assertEqual($user->getMailingMethod(), AngieMailerDelegate::SEND_HOURLY, 'User that do not have default value overriden use default mailing method');

  	  $user = Users::findById(1);
  	  $user->setMailingMethod(AngieMailerDelegate::SEND_IN_BACKGROUD);
  	  
  	  $this->assertEqual($user->getMailingMethod(), AngieMailerDelegate::SEND_IN_BACKGROUD, 'Test mailing method set');
  	  
  	  $user = Users::findById(1);
  	  $this->assertEqual($user->getMailingMethod(), AngieMailerDelegate::SEND_IN_BACKGROUD, 'Making sure that mailing method is permanently saved for this user');
  	} // testMailingMethod
  	
  	/**
  	 * Send a single message to the mailer
  	 */
  	function testSingle() {
	    $single = AngieApplication::mailer()->send(new AnonymousUser('Ilija', 'ilija.studen@activecollab.com'), 'Test single', 'Sending message to single user', array(
        'context' => 'TEST/APPMAILER', 
  		));
  		
  		$this->assertIsA($single, 'OutgoingMessage');
  		$this->assertEqual($single->getContextId(), 'TEST/APPMAILER');
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 1);
  		$log_entry = MailingActivityLogs::find(array(
  		  'one' => true,
  		));
  		
  		$this->assertIsA($log_entry, 'MessageSentActivityLog');
  		
  		$this->assertEqual($log_entry->getFromName(), 'Default From');
  		$this->assertEqual($log_entry->getFromEmail(), 'default@from.com');
  		
  		$this->assertEqual($log_entry->getToName(), 'Ilija');
  		$this->assertEqual($log_entry->getToEmail(), 'ilija.studen@activecollab.com');
  		
  		$this->assertEqual($log_entry->getAdditionalProperty('subject'), 'Test single {TEST/APPMAILER}');
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'Sending message to single user');
  	} // testSimple
  	
  	/**
  	 * Test custom sender
  	 */
  	function testCustomSender() {
  	  $single = AngieApplication::mailer()->send(new AnonymousUser('Ilija', 'ilija.studen@activecollab.com'), 'Test single', 'Sending message to single user', array(
  	    'sender' => new AnonymousUser('Custom Sender', 'custom@sender.com'), 
  	  ));
  		
  		$this->assertIsA($single, 'OutgoingMessage');
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 1);
  		$log_entry = MailingActivityLogs::find(array(
  		  'one' => true,
  		));
  		
  		$this->assertIsA($log_entry, 'MessageSentActivityLog');
  		
  		$this->assertEqual($log_entry->getFromName(), 'Custom Sender');
  		$this->assertEqual($log_entry->getFromEmail(), 'custom@sender.com');
  		
  		$this->assertEqual($log_entry->getToName(), 'Ilija');
  		$this->assertEqual($log_entry->getToEmail(), 'ilija.studen@activecollab.com');
  	} // testCustomSender
  	
  	/**
  	 * Send a message to the mailer, but set it to be delivered on an event
  	 */
  	function testDelayed() {
  		$message = AngieApplication::mailer()->send(new AnonymousUser('Ilija', 'ilija.studen@activecollab.com'), 'Test simple', 'Testing quick send', array(
  		  'sender' => new AnonymousUser('Ilija Studen', 'ilija.studen@gmail.com'), 
  		), AngieMailerDelegate::SEND_DAILY);
  		
  		$this->assertIsA($message, 'OutgoingMessage');
  		
  		$this->assertTrue($message->isLoaded());
  		$this->assertEqual($message->getSenderName(), 'Ilija Studen');
  		$this->assertEqual($message->getSenderEmail(), 'ilija.studen@gmail.com');
  		$this->assertEqual($message->getSubject(), 'Test simple');
  		$this->assertEqual($message->getBody(), 'Testing quick send');
  		$this->assertEqual($message->getMailingMethod(), AngieMailerDelegate::SEND_DAILY);
  	} // testDelayed
  	
  	/**
  	 * Send multiple messages combined as a single message
  	 */
  	function testDigest() {
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'First message', 'First message body', null, AngieMailerDelegate::SEND_HOURLY);
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'Second message', 'Second message body', null, AngieMailerDelegate::SEND_HOURLY);
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'Third message', 'Third message body', null, AngieMailerDelegate::SEND_HOURLY);
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'outgoing_messages'), 3);
  		
  		$messages = OutgoingMessages::find();
  		
  		$this->assertIsA($messages, 'DBResult');
  		$this->assertEqual($messages->count(), 3);
  		
  		AngieApplication::mailer()->getAdapter()->sendDigest($messages);
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'outgoing_messages'), 0, 'No more messages in outgoing messages table');
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 1, 'Look for outgoing mail activity log');
  		$log_entry = MailingActivityLogs::find(array(
  		  'one' => true,
  		));
  		
  		$this->assertIsA($log_entry, 'MessageSentActivityLog');
  		
  		$this->assertEqual($log_entry->getFromName(), 'Default From');
  		$this->assertEqual($log_entry->getFromEmail(), 'default@from.com');
  		
  		$this->assertEqual($log_entry->getToName(), 'Recipient');
  		$this->assertEqual($log_entry->getToEmail(), 'recipient@example.com');

      $message = first($messages);
  		$this->assertEqual($log_entry->getAdditionalProperty('subject'), $message->getDecorator()->getDigestSubject($messages));
  		
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'First message');
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'First message body');
  		
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'Second message');
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'Second message body');
  		
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'Third message');
  		$this->assertContains($log_entry->getAdditionalProperty('body'), 'Third message body');
  	} // testDigest
  	
  }