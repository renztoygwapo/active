<?php
  /**
   * Incoing mail body processor
   * 
   * @package angie.frameworks.email
   * @subpackage models
   */
  class IncomingMailBodyProcessor {
    
    /**
     * Incoming email
     * 
     * @var MailboxManagerEmail
     */
    private $email;
    
    /**
     * Email body
     * 
     * @var MailboxManagerEmail
     */
    private $body;
    
    /**
     * Body lines
     */
    private $body_lines;
    
    /**
     * Store content type with which body is processed
     * 
     * @var string
     */
    private $body_processed_as;
    
    /**
     * Construct object
     * 
     * @param $email
     */
    function __construct(&$email) {
      $this->email = $email;
    } //__construct

    /**
     * Extracts the reply from email
     *
     * @return string
     */
    function extractReply() {
      $mailer = $this->email->getMailer();

      if (in_array($mailer, array(MailboxManagerEmail::MAILER_YAHOO))) {
        $extracted_reply = $this->yahooExtractReply();
      } else if (in_array($mailer, array(MailboxManagerEmail::MAIlER_OUTLOOK_2003, MailboxManagerEmail::MAILER_OUTLOOK_2007, MailboxManagerEmail::MAILER_OUTLOOK_2010, MailboxManagerEmail::MAILER_WINDOWS_LIVE_MAIL))) {
        $extracted_reply = $this->outlookExtractReply();
      } else if (in_array($mailer, array(MailboxManagerEmail::MAILER_ANDROID_MAIL))) {
        $extracted_reply = $this->androidExtractReply();
      } else if (in_array($mailer, array(MailboxManagerEmail::MAILER_IPHONE, MailboxManagerEmail::MAILER_IPAD, MailboxManagerEmail::MAILER_IPOD))) {
        $extracted_reply = $this->iPhoneExtractReply();
      } else if ($mailer == MailboxManagerEmail::MAILER_HOTMAIL) {
        $extracted_reply =  $this->hotmailExtractReply();
      } else if ($mailer == MailboxManagerEmail::MAILER_HUSHMAIL) {
        $extracted_reply = $this->hushmailExtractReply();
      } else if ($mailer == MailboxManagerEmail::MAILER_APPLE_MAIL) {
        $extracted_reply = $this->appleMailExtractReply();
      } else {
        $extracted_reply = $this->defaultExtractReply();
      } // if

      if ($extracted_reply) {
        $this->makeClickableLinks($extracted_reply);
      } // if

      return $extracted_reply;
    } // extractReply

    /**
     * Make clickable links
     */
    function makeClickableLinks(&$text) {
      AngieApplication::useHelper('linkify', ENVIRONMENT_FRAMEWORK, 'modifier');

      $text = preg_replace('#([\s\(\)]|[,]|[<p.*>])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ieu', '\'$1\'.smarty_modifier_linkify(\'$2://$3\')', $text);
      $text = preg_replace('#([\s\(\)]|[,]|[<p.*>])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ieu', '\'$1\'.smarty_modifier_linkify(\'$2.$3\', \'$2.$3\')', $text);
      $text = preg_replace("#(^|[\n ]|[,]|[\s]|)([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#iu", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);

      // fix <<a href></a>>
      $text = preg_replace('/\<\<a[^}]*?\>(.*?)\<\/a\>\>/', '<a href="\\1">\\1</a>', $text);
    } // makeClickableLinks

    /**
     * Return array pair of content_type/content
     *
     * @param string $content_type
     * @return array
     */
    function getPreferredBody($preferred_content_type) {
      $preferred_content_type = strtolower($preferred_content_type);
      $secondary_type = $preferred_content_type == 'text/plain' ? 'text/html' : 'text/plain';

      $body = trim($this->email->getBody($preferred_content_type));
      $this->setBodyProcessedAs($preferred_content_type);
      if ($body) {
        return array($preferred_content_type, $body);
      } else {
        $body = $this->email->getBody($secondary_type);
        if ($body) {
          $this->setBodyProcessedAs($secondary_type);
          return array($secondary_type, $body);
        } else {
          return array($preferred_content_type, '');
        } // if
      } // if
    } // getPreferredBody
    
    
    /**
     * Get content type with which body is processed
     * 
     * @param $value
     */
    function getBodyProcessedAs() {
      return $this->body_processed_as;
    }//getBodyProcessedAs
    
    
    /**
     * Set content type with which body is processed
     * 
     * @param $value
     */
    function setBodyProcessedAs($value) {
      return $this->body_processed_as = $value;
    }//setBodyProcessedAs
    

    /**
     * Default method of extracting replies
     *
     * @param boolean $return_raw
     * @return string
     */
    function defaultExtractReply($return_raw = false) {
      list ($content_type, $body) = $this->getPreferredBody('text/plain');

      if ($content_type != 'text/plain') {
        $body = HTML::toPlainText($body, true);
      } else {
        self::fixNewlines($body);
      } // if
      
      $lines = explode("\n", $body);

      // strip the reply
      self::defaultStripRepliedEmail($lines);

      // strip unwanted text
      self::stripUnwantedText($lines);

      // try to strip signature
      self::stripSignature($lines);

      // convert plain text quotes to blockquotes
      self::convertPlainTextQuotesToBlockquotes($lines);

      if (!$return_raw) {
        return self::join($lines);
      } else {
        return $lines;
      } // if
    } // defaultExtractReply

    /**
     * Extract Reply from Apple MAil mail
     */
    function appleMailExtractReply($return_raw = false) {
      list ($content_type, $body) = $this->getPreferredBody('text/plain');

      if ($content_type != 'text/plain') {
        $body = HTML::toPlainText($body, true);
      } else {
        self::fixNewlines($body);
      } // if

      $lines = explode("\n", $body);

      // strip the reply
      self::defaultStripRepliedEmail($lines);

      // remove the line added by apple mail
      $body = implode("\n", $lines);
      if (preg_match('/(.*)(On)(.*) at (.*) wrote\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE)) {
        $match_index = $matches[2][1];
        $body = trim(substr_utf($body, 0, $match_index));
        $lines = explode("\n", $body);
      } // if

      // strip unwanted text
      self::stripUnwantedText($lines);

      // try to strip signature
      self::stripSignature($lines);

      // convert plain text quotes to blockquotes
      self::convertPlainTextQuotesToBlockquotes($lines);

      if (!$return_raw) {
        return self::join($lines);
      } else {
        return $lines;
      } // if
    } // appleMailExtractReply

    /**
     * Default method of extracting replies
     *
     * @param boolean $return_raw
     * @return string
     */
    function hushmailExtractReply($return_raw = false) {
      list ($content_type, $body) = $this->getPreferredBody('text/plain');

      if ($content_type != 'text/plain') {
        $body = HTML::toPlainText($body, true);
      } else {
        self::fixNewlines($body);
      } // if

      $lines = explode("\n", $body);

      // strip the reply
      self::defaultStripRepliedEmail($lines);

      // remove the css added by hushmail
      $body = implode("\n", $lines);
      if (preg_match('/(.*)(On)(.*) at (.*) wrote\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE)) {
        $match_index = $matches[2][1];
        $body = trim(substr_utf($body, 0, $match_index));
        $lines = explode("\n", $body);
      } // if

      // strip unwanted text
      self::stripUnwantedText($lines);

      // try to strip signature
      self::stripSignature($lines);

      // convert plain text quotes to blockquotes
      self::convertPlainTextQuotesToBlockquotes($lines);

      if (!$return_raw) {
        return self::join($lines);
      } else {
        return $lines;
      } // if
    } // hushmailExtractReply

    /**
     * Extract reply from message sent by live.com
     *
     * @return string
     */
    function hotmailExtractReply() {
      list ($content_type, $body) = $this->getPreferredBody('text/plain');

      if ($content_type == 'text/html') {
        $body = str_replace_utf8("--&nbsp;", "||--signature-string--||", $body);
        $body = HTML::toPlainText($body, true);
        $body = str_replace("||--signature-string--||", "-- \n", $body);
      } // if

      $lines = explode("\n", $body);

      // strip the reply
      self::defaultStripRepliedEmail($lines);

      // try to find extra text added by live messenger
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 4, true);
      if (count($unwanted_text) == 4) {
        if (preg_match('/Date\:(.*)\nSubject\:(.*)\nFrom\:(.*)\nTo\:(.*)/mis', implode("\n", $unwanted_text), $results)) {
          $lines = array_splice($lines, 0, $cut_line);
        } // if
      } // if

      $body = implode("\n", $lines);
      if (preg_match('/(.*)(To\:)(.*)\nSubject\:(.*)\nDate\:(.*)\nFrom\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE)) {
        $match_index = $matches[2][1];
        $body = trim(substr_utf($body, 0, $match_index));
        $lines = explode("\n", $body);
      } // if

      // tries to strip signature
      self::stripSignature($lines);

      // convert plain text quotes to blockquotes
      self::convertPlainTextQuotesToBlockquotes($lines);

      return self::join($lines);
    } // hotmailExtractReply

    /**
     * Extract reply from iphone sent email
     *
     * @return string
     */
    function iPhoneExtractReply() {
      $lines = $this->defaultExtractReply(true);

      // strip "sent from my ..."
      $count_lines = count($lines);
      for($x = 0; $x < $count_lines; $x++) {
        $line = trim($lines[$count_lines - $x - 1]);
        if ($line) {
          $mailer = $this->email->getMailer();

          $match_string = 'sent from my';
          if ($mailer == MailboxManagerEmail::MAILER_IPHONE) {
            $match_string = 'sent from my iphone';
          } else if ($mailer == MailboxManagerEmail::MAILER_IPAD) {
            $match_string = 'sent from my ipad';
          } else if ($mailer == MailboxManagerEmail::MAILER_IPOD) {
            $match_string = 'sent from my ipod touch';
          } // if

          if (strtolower($line) == $match_string) {
            $lines = array_splice($lines, 0, $count_lines - $x - 1);
          } else {
            break;
          } // if
        } // if
      } // for

      return self::join($lines);
    } // iPhoneExtractReply

    /**
     * Extract reply from andorid mail client
     */
    function androidExtractReply() {
      $lines = $this->defaultExtractReply(true);

      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 1);
      $unwanted_text = implode(null, $unwanted_text);

      // strip 'first name last name wrote:'
      if (preg_match('/(.*?)wrote:/is', $unwanted_text)) {
        $lines = array_splice($lines, 0, $cut_line);
      } // if

      // default signature
      $match_string = '^sent from(.*?)';

      // strip default signature
      if ($match_string) {
        list ($default_signature, $cut_line) = self::getLinesFromEnd($lines, 1);
        $default_signature = implode(null, $default_signature);
        if (preg_match('/' . $match_string . '/is', $default_signature)) {
          $lines = array_splice($lines, 0, $cut_line);
        } // if
      } // if

      // try to strip standarnized signature
      self::stripSignature($lines);

      return self::join($lines);
    } // androidExtractReply

    /**
     * Extract reply from outlook
     */
    function outlookExtractReply() {
      $lines = $this->defaultExtractReply(true);

      // try to find leftovers from image
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 1);
      $unwanted_text = implode(null, $unwanted_text);
      if ($unwanted_text && strpos($unwanted_text, '<http://') === 0) {
        $lines = array_splice($lines, 0, $cut_line);
      } // if

      // try to find extra text added by outlook and remove it
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 4, true);
      if (count($unwanted_text) == 4) {
        // english language
        if (preg_match('/From\:(.*)\nSent\:(.*)\nTo\:(.*)\nSubject\:(.*)/mis', implode("\n", $unwanted_text))) {
          $lines = array_splice($lines, 0, $cut_line);
        } // if

        // german language
        if (preg_match('/Von\:(.*)\nGesendet\:(.*)\nAn\:(.*)\nBetreff\:(.*)/mis', implode("\n", $unwanted_text))) {
          $lines = array_splice($lines, 0, $cut_line);
        } // if
      } // if

      // remove separator
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 1);
      $unwanted_text = implode(null, $unwanted_text);
      if (strpos_utf($unwanted_text, '_____') === 0) {
        $lines = array_splice($lines, 0, $cut_line);
      } // if

      // try to strip standarnized signature
      self::stripSignature($lines);

      // convert back to rich text
      return self::join($lines);
    } // outlookExtractReply

    /**
     * Extract Reply from yahoo mail
     */
    function yahooExtractReply() {
      list ($content_type, $body) = $this->getPreferredBody('text/plain');

      if ($content_type != 'text/plain') {
        $body = HTML::toPlainText($body, true);
      } else {
        self::fixNewlines($body);
      } // if

      $lines = explode("\n", $body);

      self::yahooStripRepliedEmail($lines);

      self::stripUnwantedText($lines);

      self::stripSignature($lines);

      self::convertPlainTextQuotesToBlockquotes($lines);

      $body = implode("\n", $lines);
      if (preg_match('/(.*)(From\:)(.*)\nTo\:(.*)\nSent\:(.*)\nSubject\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE)) {
        $match_index = $matches[2][1];
        $body = trim(substr_utf($body, 0, $match_index));
        $lines = explode("\n", $body);
      } // if

      // try to find extra text added by yahoo and remove it
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 4, true);
      if (count($unwanted_text) == 4) {
        if (preg_match('/From\:(.*)\nTo\:(.*)\nSent\:(.*)\nSubject\:(.*)/mis', implode("\n", $unwanted_text))) {
          $lines = array_splice($lines, 0, $cut_line);
        } // if
      } // if

      // remove separator
      list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 1);
      $unwanted_text = implode(null, $unwanted_text);
      if (strpos_utf($unwanted_text, '_____') === 0) {
        $lines = array_splice($lines, 0, $cut_line);
      } // if

      // try to strip standarnized signature
      self::stripSignature($lines);

      // fix that issue with character #160
      if ($this->email->contentTypeExists('text/html')) {
        for ($x = 0; $x < count($lines); $x++) {
          $line = trim($lines[$x]);
          if ((strlen($line) == 1 && ord($line) == 160) || (strlen($line) == 2 && ord($line[0]) == 194 && ord($line[1]) == 160)) {
            $lines[$x] = '';
          } // if
        } // for
      } // if

      // convert back to rich text
      return self::join($lines);
    } // yahooExtractReply

    /**
     * Join lines
     *
     * @static
     * @param array $lines
     * @return string
     */
    static function join($lines) {
      return nl2br(trim(implode("\n", $lines)));
    } // join

    /**
     * Get lines from end
     *
     * @static
     * @param array $lines
     * @param int $number_of_lines
     * @param bool $empty_breaks
     * @return array
     */
    static function getLinesFromEnd($lines, $number_of_lines = 1, $empty_breaks = false) {
      // extract last 4 valid rows
      $count_lines = count($lines);
      $lines_found = array();
      $target_line = 0;
      for($x = 0; $x < $count_lines; $x++) {
        $line = trim($lines[$count_lines - $x - 1]);
        if ($line) {
          $lines_found = array_merge((array) $line, $lines_found);
          if (count($lines_found) == $number_of_lines) {
            $target_line = $count_lines - $x - 1;
            break;
          } // if
        } else {
          if (count($lines_found) && $empty_breaks) {
            $target_line = $count_lines - $x - 1;
            break;
          } // if
        } // if
      } // for

      return array($lines_found, $target_line);
    } // getLinesFromEnd

     /**
     * Converts plaintext quotes to blockquotes
     *
     * @return null
     */
    function convertPlainTextQuotesToBlockquotes(&$input_lines) {
      $block_quote_opened = false;
      $lines = array();
      for ($x = 0; $x < count($input_lines); $x++) {
        $line = $input_lines[$x];
      	if ((substr_utf($line,0,1) == '>') || (substr_utf($line,0,4) == '&gt;')) {
      	  if (!$block_quote_opened) {
      	    $lines[] = "<blockquote>\n";
      	    $block_quote_opened = true; 
      	  } // if
      	} else {
      	  if ($block_quote_opened) {
      	    $lines[] = "</blockquote>\n";
            $block_quote_opened = false;
      	  } // if
      	} // if

        if (substr_utf($line,0,1) == '>') {
          $lines[] = substr_utf($line, 1);
        } else if (substr_utf($line,0,4) == '&gt;') {
          $lines[] = substr_utf($line, 4);
        } else {
          $lines[] = $line;
        } // if
      } // foreach    
  	  if ($block_quote_opened) {
  	    $lines[] = "</blockquote>";
  	  } // if

      $input_lines = $lines;
    } // convertPlainTextQuotesToBlockquotes
  
    /**
     * Strip reply from email
     *
     * @param array $lines
     * @return boolean
     */
    function defaultStripRepliedEmail(&$lines) {
      $splitters = self::getSplitters();
      self::doStripRepliedEmail($lines, $splitters);
      return true;
    } // defaultStripRepliedEmail

    /**
     * Strip reply from yahoo email
     *
     * @param array $lines
     * @return boolean
     */
    function yahooStripRepliedEmail(&$lines) {
      $splitters = self::getSplitters();
      self::doStripRepliedEmail($lines, $splitters);

      if (is_foreachable($splitters)) {
        $yahoo_splitters = array();
        foreach ($splitters as $splitter) {
          if (strpos($splitter, '-- ') === 0) {
            $yahoo_splitters[] = substr_utf($splitter, 3);
          } else if (strpos($splitter, '--') === 0) {
            $yahoo_splitters[] = substr_utf($splitter, 3);
          } // if
        } // foreach

        $body = implode("\n", $lines);
        if (preg_match('/On(.*?)wrote\:(.*?)/is', $body)) {
          $yahoo_splitters[] = 'wrote:'; //for new yahoo conversations which have "On @date, @name wrote:" part
        } // if

        if (is_foreachable($yahoo_splitters)) {
          self::doStripRepliedEmail($lines, $yahoo_splitters, 1);
        } // if
      } // if4

      return true;
    } // yahooStripRepliedEmail

    /**
     * Do real stripping
     *
     * @param array $lines
     * @param array $splitters
     * @param number $trim_previous_lines
     * @return boolean
     */
    function doStripRepliedEmail(&$lines, &$splitters, $trim_previous_lines = 0) {
      $stripped = array();
      if (is_foreachable($lines)) {
        foreach ($lines as $line) {
          foreach ($splitters as $splitter) {
            if (stripos_utf($line, $splitter) !== false) {
              if ($trim_previous_lines == 0) {
                $lines = $stripped;
              } else {
                $lines = array_slice($stripped, 0, count($stripped) - $trim_previous_lines);
              } // if

              $last_line = trim($lines[count($lines) - 1]);
              if (in_array($last_line, array(
                '>', '&gt;',
                '> **', '&gt; **',
              ))) {
                $lines = array_slice($lines, 0, count($lines) - 1); // sometimes > sign appears in front of reply so we need to strip it
              } // if

              return true;
            } // if
          } // foreach
          $stripped[] = $line;
        } // foreach
      } // if

      return true;
    } // doStripRepliedEmail

    /*
     * Get Splitters
     *
     * @return array
     */
    function getSplitters() {
      $splitters = array(
        EMAIL_SPLITTER,
        '-----Original Message-----',
        '-------- Original message --------'
      );

      // load splitters for other languages
      $languages = Languages::findAll();
      if (is_foreachable($languages)) {
        foreach ($languages as $language) {
          $splitter_translation = lang(EMAIL_SPLITTER, null, null, $language);
          if ($splitter_translation && !in_array($splitter_translation, $splitters)) {
            $splitters[] = $splitter_translation;
          } // if
        } // foreach
      } // if

      return $splitters;
    } // getSplitters

    /**
     * Strip unwanted text after stripping reply
     *
     * @param array $lines
     * @return boolean
     */
    function stripUnwantedText(&$lines) {
      if (!is_foreachable($lines)) {
        return false;
      } // if

      $unwanted_text_patterns = array(
        '/^On(.*?)wrote:(.*?)/is',
        '/^Am(.*?)schrieb(.*?)/is',
        '/^(.*?)\/(.*?)\/(.*?)\<(.*?)\>(.*?)/is',
        '/^(.*?)\/(.*?)\/(.*?)&lt;(.*?)&gt;(.*?)/is'
      );

      foreach ($unwanted_text_patterns as $unwanted_text_pattern) {
        list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 1, true);
        $unwanted_text = trim(implode(null, $unwanted_text));
        if (preg_match($unwanted_text_pattern, $unwanted_text)) {
          $lines = array_splice($lines, 0, $cut_line);
          return true;
        } // if

        list ($unwanted_text, $cut_line) = self::getLinesFromEnd($lines, 2, true);
        $unwanted_text = trim(implode(null, $unwanted_text));
        if (preg_match($unwanted_text_pattern, $unwanted_text)) {
          $lines = array_splice($lines, 0, $cut_line);
          return true;
        } // if
      } // foreach

      $body = implode("\n", $lines);
      if (preg_match('/(.*)(From\:)(.*)\nTo\:(.*)\nSent\:(.*)\nSubject\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE) || preg_match('/(.*)(From\:)(.*)\nSent\:(.*)\nTo\:(.*)\nSubject\:(.*)/mis', $body, $matches, PREG_OFFSET_CAPTURE)) {
        $match_index = $matches[2][1];
        $body = trim(substr_utf($body, 0, $match_index));
        $lines = explode("\n", $body);
      } // if

      return true;
    } // stripUnwantedText

    /**
     * Strip signature from email
     *
     * @param $lines
     * @return boolean
     */
    function stripSignature(&$lines) {
      if (is_foreachable($lines)) {
        $count = count($lines);

        for ($x = 0; $x < $count; $x++) {
          $line = trim($lines[(($count - $x) - 1)]);
          if ($line && trim($line)) {
            if ($line == "-- " || $line == "--") {
              $lines = array_splice($lines, 0, (($count - $x) - 1));
              return true;
            } // if

            // we think that signature should not be longer than 8 characters
            if ($x > 8) {
              return true;
            } // if
          } // if
        } // for
      } // if

      return false;
    } // stripSignature

    /**
     * Fixes newlines (converts \n\r into \n)
     *
     * @param $body
     */
    function fixNewlines(&$body) {
      $body = str_replace(array("\n\r", "\r\n", "\r"), array("\n", "\n", "\n"), $body);
    } // fixNewLines

    /**
     * Additional data
     *
     * @return mixed
     */
    function getAdditionalData() {
      return false;
    } // getAdditionalData
    
  } // IncomingMailBodyProcessor