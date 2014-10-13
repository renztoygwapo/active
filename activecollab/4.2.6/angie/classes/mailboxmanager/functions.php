<?php

  /**
   * Functions for MailboxManager module
   * 
   * @package angie.classes.mailboxmanager
   */
  
  /**
   * Returns default port for server security type
   *
   * @param string $security
   * @return integer
   */
  function mm_get_default_port($security) {
    return $security == MM_SECURITY_SSL ? 993 : 110;
  } // mm_get_default_port
  
  /**
   * Alternative imap_utf8 function
   *
   * @param string $something_to_decode
   */
  function imap_utf8_alt($something_to_decode) {
    if (!trim($something_to_decode)) {
      return null;
    } // if
    
    // if function exists we will try to use it in order to decode subject, otherwise we use buggy imap_utf8
    if (function_exists('imap_mime_header_decode')) {
      $decoded = imap_mime_header_decode($something_to_decode);
      if (is_foreachable($decoded)) {
        $decoded_string = '';
        foreach ($decoded as $element) {
        	if (strtoupper($element->charset) == 'DEFAULT') {
        		$decoded_string.= convert_to_utf8($element->text, 'US-ASCII');
        	} else if (strtoupper($element->charset) != 'UTF-8') {
            $decoded_string.= convert_to_utf8($element->text, $element->charset);
          } else {
            $decoded_string.= $element->text;
          } // if
        } // foreach
        $decoded_string = trim($decoded_string);
        if ($decoded_string) {
          return $decoded_string;
        } // if
      } // if
    } // if
    
    $decoded_string = trim(imap_utf8($something_to_decode));
    if (strlen_utf($decoded_string) > 0) {
      return $decoded_string;
    } // if
    
    return $something_to_decode;
  } // if

  /**
   * we define our own savebody function
   *
   * @param resource $imap_stream
   * @param mixed $file
   * @param int $msg_number
   * @param string $part_number
   * @param int $options
   * @return boolean
   */
  function imap_failsafe_savebody($imap_stream, $file, $msg_number, $part_number=null, $options=null) {
    $fetch_data = imap_fetchbody($imap_stream, $msg_number, $part_number, $options);
    if ($fetch_data === false) {
      return false;
    } // if
    return file_put_contents($file, $fetch_data);
  } // imap_failsafe_savebody

  // determine if we'll use failsafe functions for saving email body parts
  if (FAIL_SAFE_IMAP_FUNCTIONS || (!function_exists('imap_savebody'))) {
    /**
     * we define our own savebody function
     *
     * @param resource $imap_stream
     * @param mixed $file
     * @param int $msg_number
     * @param string $part_number
     * @param int $options
     * @return boolean
     */
    function imap_savebody_alt($imap_stream, $file, $msg_number, $part_number=null, $options=null) {
      return imap_failsafe_savebody($imap_stream, $file, $msg_number, $part_number, $options);
    } // imap_savebody_alt
    
  } else {
    /**
     * define proxy function for imap_savebody
     *
     * @param resource $imap_stream
     * @param mixed $file
     * @param int $msg_number
     * @param string $part_number
     * @param int $options
     * @return boolean
     */
    function imap_savebody_alt($imap_stream, $file, $msg_number, $part_number=null, $options=null) {
      return imap_savebody($imap_stream, $file, $msg_number, $part_number, $options);
    } // imap_savebody_alt
  } // if