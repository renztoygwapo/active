<?php

  /**
   * Script which imports email into activecollab
   *
   * @package activeCollab
   * @subpackage tasks
   */

  require_once dirname(__FILE__) . '/init.php';

  // importing from specified file
  $filename = array_var($argv, 2);
  if ($filename) {
    echo 'Importing email from file starting on ' . strftime(FORMAT_DATETIME) . ".\n";
    if (!is_file($filename)) {
      die("Error: Eml file does not exists\n");
    } // if
  } // if

  // import from stdin
  if (!$filename) {
    echo 'Importing email from stdin starting on ' . strftime(FORMAT_DATETIME) . ".\n";
    $email_source = trim(get_stdin());
    if (!$email_source) {
      die("Error: Email not provided \n");
    } // if

    // determine work filename
    do {
      $filename = WORK_PATH . '/' . make_string(10) . '-' . make_string(10) . '-' . make_string(10) . '-' . make_string(10) . '.eml';
    } while(is_file($filename));

    if (strtolower(substr($email_source, 0, 5)) != 'from ') {
      $from_line = 'From MAILER-DAEMON ' . date('D M d H:i:s Y', time()) . "\n";
      $email_source = $from_line . $email_source;
    } // if

    file_put_contents($filename, $email_source);
    chmod($filename, 0777);
  } // if

  try {
    AngieApplication::incomingMail()->importEmailFromFile($filename);
  } catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
  } // if

  echo 'Importing email finished on ' . strftime(FORMAT_DATETIME) . ".\n";