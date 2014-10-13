<?php

  /**
   * Test disabled mailer adapter
   * 
   * @package angie.frameworks.email
   * @subpackage tests
   */
  class TestDisabledApplicationMailer extends AngieModelTestCase {
  	
  	/**
  	 * Set up test disabled mailer environement
  	 */
  	function setUp() {
  		parent::setUp();
  		
  		AngieApplication::mailer()->setAdapter(new DisabledMailerAdapter());
  		AngieApplication::mailer()->setDefaultSender(new AnonymousUser('Default From', 'default@from.com'));
  		AngieApplication::mailer()->setDecorator(new OutgoingMessageDecorator());
  		AngieApplication::mailer()->connect();
  	} // setUp
  	
  	/**
  	 * Tear down
  	 */
  	function tearDown() {
  		parent::tearDown();
  		
  		AngieApplication::mailer()->disconnect();
  	} // tearDown
  	
  	/**
  	 * Run the test by sending a single message
  	 */
  	function testSingle() {
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 0, 'No entries in the log before we send a message');
  		
  		$single = AngieApplication::mailer()->send(new AnonymousUser('Ilija', 'ilija.studen@activecollab.com'), 'Test single', 'Sending message to single user');
  		
  		$this->assertIsA($single, 'OutgoingMessage');
  		$this->assertFalse($single->isLoaded(), 'Message instance is not saved');
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 0, 'No entries in the log after we send a message');
  	} // testSingle
  	
  	/**
  	 * Send multiple messages combined as a single message
  	 */
  	function testDigest() {
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 0, 'Activity log is empty');
  		
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'First message', 'First message body', null, AngieMailerDelegate::SEND_HOURLY);
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'Second message', 'Second message body', null, AngieMailerDelegate::SEND_HOURLY);
  		AngieApplication::mailer()->send(new AnonymousUser('Recipient', 'recipient@example.com'), 'Third message', 'Third message body', null, AngieMailerDelegate::SEND_HOURLY);
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'outgoing_messages'), 3);
  		
  		$messages = OutgoingMessages::find();
  		
  		$this->assertIsA($messages, 'DBResult');
  		$this->assertEqual($messages->count(), 3);
  		
  		AngieApplication::mailer()->getAdapter()->sendDigest($messages);
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'outgoing_messages'), 0, 'No more messages in outgoing messages table');
  		
  		$this->assertEqual((integer) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'mailing_activity_logs'), 0, 'Look for outgoing mail activity log, it needs to be empty');
  	} // testDigest 
  	
  }