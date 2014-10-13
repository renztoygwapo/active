<?php

  /**
   * Class that abstracts single email message
   *
   * @package angie.incoming_mail
   */
  class MailboxManagerEmail {

    // desktop
    const MAILER_APPLE_MAIL = 'AppleMail';
    const MAILER_THUNDERBIRD = 'Thunderbird';
    const MAILER_WINDOWS_MAIL = 'WindowsMail';
    const MAILER_WINDOWS_LIVE_MAIL = 'WindowsLiveMail';
    const MAILER_OUTLOOK_EXPRESS = 'OutlookExpress';
    const MAIlER_OUTLOOK_2003 = 'Outlook2003';
    const MAILER_OUTLOOK_2007 = 'Outlook2007';
    const MAILER_OUTLOOK_2010 = 'Outlook2010';

    // webmails
    const MAILER_HOTMAIL = 'Hotmail';
    const MAILER_GMAIL = 'Gmail';
    const MAILER_YAHOO = 'Yahoo';
    const MAILER_HUSHMAIL = 'Hushmail';

    // mobile devices
    const MAILER_IPHONE = 'iPhone';
    const MAILER_IPAD = 'iPad';
    const MAILER_IPOD = 'iPod';
    const MAILER_ANDROID_MAIL = 'AndroidMail';
    const MAILER_ANDROID_GMAIL = 'AndroidGmail';

    // other
    const MAILER_UNKNOWN = 'Unknown';
    
    /**
     * Message id
     * 
     * @var integer
     */
    var $id;
    
    /**
     * Message Subject
     *
     * @var string
     */
    var $subject;
    
    /**
     * Message Date
     *
     * @var string
     */
    var $date;
    
    /**
     * Message size
     *
     * @var string
     */    
    var $size;
    
    /**
     * Body parts
     * 
     * @var array
     */
    var $bodies;
    
    /**
     * Attachments
     *
     * @var array
     */
    var $attachments;
    
    /**
     * Addresses
     * 
     * @var array
     */
    var $addresses;
    
    /**
     * Email headers
     *
     * @var string
     */
    var $headers;

    /**
     * Priority
     * 
     * @var int
     */
    var $priority = 3;

    /**
     * Is email delivery failure or auto-respond
     * 
     * @var boolean
     */
    var $is_auto_respond = false;
    
    /**
     * Existing content types
     *
     * @var array
     */
    var $existing_content_types;

    /**
     * Constructor method
     *
     */
    function __construct() {
      
    } // __construct
    
    /**
     * Set email subject
     *
     * @param string $subject
     */
    function setSubject($subject) {
      $this->subject = trim($subject);
    } // setSubject
    
    /**
     * Get email subject
     *
     */
    function getSubject() {
      return $this->subject;
    } // getSubject
    
    /**
     * Set the email time stamp
     *
     */
    function setDate($date_timestamp) {
      $this->date = $date_timestamp;
    } // setDate
    
    /**
     * Retrieves date timestamp
     *
     */
    function getDate() {
      return $this->date;
    } // getDate
    
    /**
     * Sets the email size
     *
     * @param integer $email_size
     */
    function setSize($email_size) {
      $this->size = (integer) $email_size;
    } // setSize
    
    /**
     * Retrieve email size
     *
     */
    function getSize() {
      return $this->size;
    } // getSize
    
    /**
     * Set email id
     *
     * @param string $id
     */
    function setId($id) {
      $this->id = $id;
    } // setId
    
    /**
     * Get email id
     *
     * @return string
     */
    function getId() {
      return $this->id;
    } // getId

    /**
     * Set priority
     *
     * @param string $priority
     */
    function setPriority($value) {
      $this->priority = $value;
    } // setPriority
    
    /**
     * Get priority
     *
     * @return string
     */
    function getPriority() {
      return $this->priority;
    } // getPriority
    
    /**
     * Set email headers
     *
     * @param string $headers
     */
    function setHeaders($headers) {
      $this->headers = $headers;
    } // setHeaders
    
    /**
     * Return email headers
     *
     * @return string
     */
    function getHeaders() {
      return $this->headers;
    } // getHeaders
    
    
    /**
     * Return is_delivery_failure
     * 
     * @return boolean
     */
    function getIsAutoRespond() {
      return $this->is_auto_respond;
    }//getIsAutoRespond
    
    /**
     * Set is_delivery_failure
     * 
     * @param $value
     */
    function setIsAutoRespond($value) {
      return $this->is_auto_respond = $value;
    }//setIsAutoRespond
    
    /**
     * Add recipients
     *
     * @param string $email_address
     * @param string $name
     * @param string $group
     */
    function addAddress($email_address, $name = null, $group = 'to') {
      $this->addresses[$group][] = array(
  		  'name'  => $name,
  		  'email' => $email_address
  		);
    } // addRecipient
    
    /**
     * Retrieve recipient data
     *
     * @param string $group
     * @param integer $id
     * @return array
     */
    function getAddress($group = 'to', $id = 0) {
      return isset($this->addresses[$group]) && isset($this->addresses[$group][$id]) ? $this->addresses[$group][$id] : null;
    } // getAddress
    
    /**
     * Retrieve recipient data
     *
     * @param string $group
     * @return array
     */
    function getAddresses($group = 'to') {
      return isset($this->addresses[$group]) ? $this->addresses[$group] : null;
    } //getAddresses
    
    /**
     * Add Body Part
     *
     * @param string $part_id
     * @param string $content_type
     * @param string $content
     * @return boolean
     */
    function addBody($part_id, $content_type, $content = null) {
      if (!in_array($content_type, (array) $this->existing_content_types)) {
        $this->existing_content_types[] = $content_type;
      } // if

      if (!isset($this->bodies[$content_type])) {
        $this->bodies[$content_type] = array();
      } // if

      $this->bodies[$content_type][] = array(
        'part_id'       => $part_id,
        'content_type'  => $content_type,
        'content'       => trim($content)
      );
      return true;
    } // addBodyPart
    
    /**
     * Add attachment
     *
     * @param integer $part_id
     * @param string $content_type
     * @param string $filename
     * @param string $path
     * @param integer $size
     * @return boolean
     */
    function addAttachment($part_id, $content_type, $filename, $path, $size=null) {
      $this->attachments[] = array(
        'id'            => $part_id,
        'content_type'  => $content_type,
        'filename'      => $filename,
        'path'          => $path,
        'size'          => $size,
      );
      return true;
    } // addAttachment
    
    
    /**
     * Return email attachments
     *
     * @return array
     */
    function getAttachments() {
      return $this->attachments;  
    } // getAttachments();

    /**
     * Count number of attachments
     *
     * @return int
     */
    function countAttachments() {
      return count($this->attachments);
    } // countAttachments
    
    /**
     * Returns processed bodies
     * 
     * @param string $content_type
     * @return string
     */
    function getBody($content_type) {
      $bodies = $this->getBodies($content_type);

      if (!is_foreachable($bodies)) {
        return null;  
      } // if

      $result = '';
      foreach ($bodies as $body) {
        if($content_type == 'text/html') {
          $result .= '<br>' . $body['content'];
        } else {
          $result .= "\n" . clean($body['content']);
        } // if

//        if ($result) {
//          $result .= ($content_type == 'text/html' ? '<br />' : "\n");
//        } // if
//        $result.= $content_type == 'text/plain' ? clean($body['content']) : $body['content'];
      } // foreach

      return trim($result);
    } // getProcessedBody

    /**
     * Check if content type exists in email
     *
     * @param string $content_type
     */
    function contentTypeExists($content_type) {
      return in_array($content_type, $this->existing_content_types);
    } // contentTypeExists
    
    /**
     * Get bodies with prefered content
     *
     * @param string $content_type
     */
    function getBodies($content_type) {
      return array_var($this->bodies, $content_type, null);
    } // getBodies

    /**
     * Cached mailer value
     *
     * @var string
     */
    protected $mailer = false;
    
    /**
     * Returns email client name (if it's known)
     *
     * @return string
     */
    function getMailer() {
      if ($this->mailer === false) {
        $headers = $this->getHeaders();

        if (strpos($headers, 'X-Mailer: iPod Mail') !== false) {
          $this->mailer = self::MAILER_IPOD;

        } else if (strpos($headers, 'X-Mailer: iPad Mail') !== false) {
          $this->mailer = self::MAILER_IPAD;

        } else if (strpos($headers, 'X-Mailer: iPhone Mail') !== false) {
          $this->mailer = self::MAILER_IPHONE;

        } else if (strpos($headers, 'X-Mailer: Microsoft Outlook Express') !== false) {
          $this->mailer = self::MAILER_OUTLOOK_EXPRESS;

        } else if (strpos($headers, 'X-Mailer: Microsoft Windows Mail') !== false) {
          $this->mailer = self::MAILER_WINDOWS_MAIL;

        } else if (strpos($headers, 'X-Mailer: Microsoft Windows Live Mail') !== false) {
          $this->mailer = self::MAILER_WINDOWS_LIVE_MAIL;

        } else if (strpos($headers, 'X-Mailer: Microsoft Office Outlook, Build 11') !== false) {
          $this->mailer = self::MAIlER_OUTLOOK_2003;

        } else if (strpos($headers, 'X-Mailer: Microsoft Office Outlook 12.') !== false) {
          $this->mailer = self::MAILER_OUTLOOK_2007;

        } else if (strpos($headers, 'X-Mailer: Microsoft Outlook 14.') !== false) {
          $this->mailer = self::MAILER_OUTLOOK_2010;

        } else if (strpos($headers, 'X-Mailer: Apple Mail') !== false || strpos($headers, 'Apple Message framework') !== false) {
          $this->mailer = self::MAILER_APPLE_MAIL;

        } else if (preg_match('/User\-Agent\:(.*)Mozilla(.*)Thunderbird(.*)/is', $headers)) {
          $this->mailer = self::MAILER_THUNDERBIRD;

        } else if (preg_match('/Message\-ID\:\ \<(.*?)@email.android.com\>/is', $headers)) {
          $this->mailer = self::MAILER_ANDROID_MAIL;

        } else if (preg_match('/Message\-ID\:\ \<(.*?)@mail.gmail.com\>/is', $headers)) {
          $this->mailer = self::MAILER_GMAIL;

        } else if (preg_match('/Received\:(.*)hotmail.com. \[(.*).(.*).(.*).(.*)\]/is', $headers) || preg_match('/Received\:(.*)hotmail.com./is', $headers)) {
          $this->mailer = self::MAILER_HOTMAIL;

        } else if (strpos($headers, 'X-Mailer: YahooMail') !== false) {
          $this->mailer = self::MAILER_YAHOO;

        } else if (preg_match('/Message\-ID\:\ \<(.*?)@smtp.hushmail.com\>/is', $headers)) {
          $this->mailer = self::MAILER_HUSHMAIL;

        } else {
          $this->mailer = self::MAILER_UNKNOWN;
        } // if
      } // if

      return $this->mailer;
    } // getMailer;
  } // MailboxManagerEmail
