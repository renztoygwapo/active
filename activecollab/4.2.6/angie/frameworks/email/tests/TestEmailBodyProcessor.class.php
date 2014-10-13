<?php

  /**
   * Test body processor
   */
  class TestEmailBodyProcessor extends AngieModelTestCase {
  	
  	/**
  	 * Body processor
  	 * 
  	 * @var IncomingMailBodyProcessor
  	 */
  	protected $processor;
  	
  	/**
  	 * Loaded email

  	 * @var MailboxManagerEmail
  	 */
  	protected $email;
  	
  	/**
  	 * Email manager
  	 * 
  	 * @var MailboxManager
  	 */
  	protected $manager;
  	
  	/**
  	 * Expected reply
  	 * 
  	 * @var string
  	 */
  	protected $expected_reply;
    
    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();
      
      $this->expected_reply = 'Email Reply';
      
      require_once ANGIE_PATH . '/classes/mailboxmanager/MailboxManagerEmail.class.php';
      require_once EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_body_processors/IncomingMailBodyProcessor.class.php';
    } // setUp
    
    /**
     * Tear down test case
     */
    function tearDown() {
      parent::tearDown();
    } // tearDown
    
    /**
     * Load requested email resource and create body processor
     * 
     * @param string $resource_name
     * @return null
     */
    function loadEmailResource($resource_name) {
    	$resource_file = EMAIL_FRAMEWORK_PATH . '/tests/resources/' . $resource_name . '.eml';
    	
    	// initialize connection to mailbox (eml file)
    	$this->manager = new PHPImapMailboxManager($resource_file);
    	$this->manager->connect();

    	// get the message
    	$this->email = $this->manager->getMessage(1, WORK_PATH);
      $this->manager->disconnect();

    	// initialize processor
    	$this->processor = new IncomingMailBodyProcessor($this->email);
    } // getEmailResource

    /**
     * Test reply from outlook
     */
    function testReplies() {

      // test apple mail
      $this->loadEmailResource('apple_mail_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('apple_mail_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('apple_mail_case_01');
      $this->assertEqual($this->processor->extractReply(), 'Just an attachment');
      $this->loadEmailResource('apple_mail_case_02');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);


      // test thunderbird
      $this->loadEmailResource('thunderbird_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('thunderbird_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test gmail
      $this->loadEmailResource('gmail_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('gmail_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test hotmail
      $this->loadEmailResource('hotmail_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('hotmail_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('hotmail_case_02');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test hushmail
      $this->loadEmailResource('hushmail');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test yahoo
      $this->loadEmailResource('yahoo_rich');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('yahoo_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('yahoo_case_01');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('yahoo_case_02');
      $this->assertEqual($this->processor->extractReply(), 'Reply koji uzrokuje da se u komentar uvoze i podaci o mail-u (task otvoren: <a href="https://afiveone.activecollab.net/projects/activecollab/tasks/2592" rel="nofollow">https://afiveone.activecollab.net/proje &hellip; tasks/2592</a>)');
      $this->loadEmailResource('yahoo_conversation');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test ios devices
      $this->loadEmailResource('iphone');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('ipad');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('ipod');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('ipad-case-01');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test android mail
      $this->loadEmailResource('android_mail');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('android_gmail');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('android_mail_origin');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test outlook express mixed
      $this->loadEmailResource('outlook_express_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_express_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test windows mail
      $this->loadEmailResource('windows_mail_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('windows_mail_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test windows mail live
      $this->loadEmailResource('windows_live_mail_mixed');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('windows_live_mail_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('windows_live_case_01');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test outlook 2003
      $this->loadEmailResource('outlook_2003_rich');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2003_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test outlook 2007
      $this->loadEmailResource('outlook_2007_html');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2007_rich');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2007_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2007_german');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // test outlook
      $this->loadEmailResource('outlook_2010_html');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2010_rich');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
      $this->loadEmailResource('outlook_2010_plain');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      $this->loadEmailResource('outlook_2013');
      $this->assertEqual($this->processor->extractReply(), 'Test from my Outlook.');


      $this->loadEmailResource('windows_phone');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // unknown
      $this->loadEmailResource('unknown_case_01');
      $this->assertEqual($this->processor->extractReply(), 'Thanks Michael.  We will discuss and get back to you.');

      // create temp language so we can do the testing for localized splitter
      DB::execute("INSERT INTO `" . TABLE_PREFIX . "languages` (`id`, `name`, `locale`, `last_updated_on`) VALUES (2, 'Serbian Latin', 'sr_CS.UTF-8', '2012-09-12 01:42:42');");
      DB::execute("INSERT INTO `" . TABLE_PREFIX . "languages` (`id`, `name`, `locale`, `last_updated_on`) VALUES (3, 'German', 'de_DE@euro', '2012-09-12 01:42:42');");
      DB::execute("INSERT INTO `" . TABLE_PREFIX . "language_phrases` (`hash`, `phrase`, `module`, `is_serverside`, `is_clientside`) VALUES ('48126d2b107337c18ea5b578c08d14f7', '-- REPLY ABOVE THIS LINE --', 'email', true, NULL);");
      DB::execute("INSERT INTO `" . TABLE_PREFIX . "language_phrase_translations` (`language_id`, `phrase_hash`, `translation`) VALUES ('2', '48126d2b107337c18ea5b578c08d14f7', '-- ODGOVORI ODJE --');");
      DB::execute("INSERT INTO `" . TABLE_PREFIX . "language_phrase_translations` (`language_id`, `phrase_hash`, `translation`) VALUES ('3', '48126d2b107337c18ea5b578c08d14f7', '-- ANTWORT ÜBER DIESER LINIE --');");

      // test the localized splitter
      $this->loadEmailResource('localized_splitter');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // unwanted text in non-english languages
      $this->loadEmailResource('german_case_01');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // other test cases
      $this->loadEmailResource('gmail_case_01');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);

      // testing unicode
      $this->loadEmailResource('latin_unicode_01');
      $this->assertEqual($this->processor->extractReply(), 'Šćućurih se u čaši povrh džačića.');
      $this->loadEmailResource('cyrilic_unicode_01');
      $this->assertEqual($this->processor->extractReply(), 'Ово ће бити ћирилични тест');

      // other cases
      $this->loadEmailResource('case_01');
      $this->assertEqual($this->processor->extractReply(), 'Šta ćemo sa ekipom koja je koristila lokalizaovani reply above this line?');

      $this->loadEmailResource('case_02');
      $this->assertEqual($this->processor->extractReply(), $this->expected_reply);
    } // testReplyFromOutlook
    
  } // TestEmailBodyProcessor