<?php

  /**
   * All web related functions - content forwarding, redirections, header 
   * manipulation etc
   *
   * @package angie.functions
   */

  /**
   * Forward specific file to the browser as a stream of data
   * 
   * Download can be forced (disposition: attachment) or passed inline
   *
   * @param string $path File path
   * @param string $type Serve file as this type
   * @param string $name If set use this name, else use filename (basename($path))
   * @param boolean $force_download Force download (add Disposition => attachement)
   * @param boolean $die
   * @return boolean
   */
  function download_file($path, $type = 'application/octet-stream', $name = null, $force_download = false, $die = true) {
    if(!defined('HTTP_LIB_PATH')) {
      require ANGIE_PATH . '/classes/http/init.php';
    } // if
    
    // Prepare variables
    if(empty($name)) {
      $name = basename($path);
    } // if
    
    if(isset($_SERVER) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
      $name = urlencode($name); // Fix problem with non-ASCII characters in IE
    } // if
    
    $disposition = $force_download ? HTTP_DOWNLOAD_ATTACHMENT : HTTP_DOWNLOAD_INLINE;

    // Make sure that system is usable while download is running
    if($die) {
      session_write_close();
    } // if
    
    // Prepare and send file
    $download = new HTTP_Download();
    $download->setFile($path, true);
    $download->setContentType($type);
    $download->setContentDisposition($disposition, $name);
    
    $download->send();
    
    if($die) {
      die();
    } // if
  } // download_file
  
  /**
   * Use content (from file, from database, other source...) and pass it to the 
   * browser as a file
   *
   * @param string $content
   * @param string $type MIME type
   * @param string $name File name
   * @param integer $size File size
   * @param boolean $force_download Send Content-Disposition: attachment to force 
   *   save dialog
   * @param boolean $die
   * @return boolean
   */
  function download_contents($content, $type, $name, $force_download = false, $die = true) {
    if(!defined('HTTP_LIB_PATH')) {
      require ANGIE_PATH . '/classes/http/init.php';
    } // if
    
    if(isset($_SERVER) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
      $name = urlencode($name); // Fix problem with non-ASCII characters in IE
    } // if
    
    $disposition = $force_download ? HTTP_DOWNLOAD_ATTACHMENT : HTTP_DOWNLOAD_INLINE;

    // Make sure that system is usable while download is running
    if($die) {
      session_write_close();
    } // if
    
    // Prepare and send file
    $download = new HTTP_Download();
    $download->setData($content);
    $download->setContentType($type);
    $download->setContentDisposition($disposition, $name);
    
    $download->send();
    
    if($die) {
      die();
    } // if
  } // download_contents

  /**
   * Download file from server
   *
   * @param string $url
   * @param string $destination_file
   * @param array $request_headers
   * @param closure $progress_callback
   * @return array
   * @throws Exception
   */
  function download_from_server($url, $destination_file, $request_headers = null, $progress_callback = null) {
    $proxy_settings = ConfigOptions::getValue(array(
      'network_proxy_enabled',
      'network_proxy_protocol',
      'network_proxy_address',
      'network_proxy_port'
    ));

    $response_headers = null;
    $destination_directory = false;
    $size_of_the_download = 0;

    if (is_dir($destination_file)) {
      $destination_directory = $destination_file;
      $destination_file = AngieApplication::getAvailableFileName($destination_file);
    } // if

    // open the write handle
    $write_handle = fopen($destination_file, 'w+b');
    if (!$write_handle) {
      throw new Error('Cannot write update package to temporary folder');
    } // if

    // download package using curl functions
    if (function_exists('curl_init')) {
      // initialize curl
      $curl = curl_init($url);
      if ($curl_error = curl_error($curl)) {
        throw new Error(lang('Operation failed with error: :error', array('error' => $curl_error)));
      } // if

      // set curl options
      curl_setopt($curl, CURLOPT_FILE, $write_handle); // curl output
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // follow redirects if needed
      curl_setopt($curl, CURLOPT_TIMEOUT, 3000); // set timeout to 50 minutes
      curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers); // send request headers

      if (VERIFY_APPLICATION_VENDOR_SSL) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // verify peer
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // verify host
        curl_setopt($curl, CURLOPT_CAINFO, CUSTOM_CA_FILE); // use certificate file
      } else {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // don't verify peer
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // don't verify host
      } // if

      if ($proxy_settings['network_proxy_enabled']) {
        curl_setopt($curl, CURLOPT_PROXY, $proxy_settings['network_proxy_address'] . ':' . $proxy_settings['network_proxy_port']);
      } // if

      // collect response headers
      curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $header_line) use (&$response_headers) {
        $response_headers .= $header_line;
        return strlen($header_line);
      });

      if ($progress_callback) {
        // saving download progress
        curl_setopt($curl, CURLOPT_NOPROGRESS, false); // log progress
        curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, function ($download_size, $downloaded_size, $upload_size, $uploaded_size) use ($progress_callback, &$size_of_the_download) {
          if ($download_size > $size_of_the_download && $download_size > 1024) {
            $size_of_the_download = $download_size;
          } else {
            $download_size = $size_of_the_download;
          } // if

          $progress_callback($download_size, $downloaded_size);
        });
      } // if

      // execute curl request
      curl_exec($curl);
      if ($curl_error = curl_error($curl)) {
        throw new Error(lang('Operation failed with error: :error', array('error' => $curl_error)));
      } // if

      $response_headers = parse_headers(trim($response_headers));

      // close handles
      fclose($write_handle);
      curl_close($curl);

    } else if (ini_get('allow_url_fopen') && extension_loaded('openssl')) { // download update package using file handling functions

      // default stream context create options
      $stream_context_create_options = array(
        'http'=> array(
          'method'      => "GET",
          'header'      => implode("\r\n", $request_headers) . "\r\n"
        )
      );

      // if proxy is enabled
      if ($proxy_settings['network_proxy_enabled']) {
        $prefix = strtolower($proxy_settings['network_proxy_protocol']) == 'http' ? 'tcp' : 'ssl';
        $stream_context_create_options['http']['proxy'] = $prefix . '://' . $proxy_settings['network_proxy_address'] . ':' . $proxy_settings['network_proxy_port'];
        $stream_context_create_options['http']['request_fulluri'] = true;
      } // if

      // if custom ca certificate is enabled, turn on additional ssl options
      if (VERIFY_APPLICATION_VENDOR_SSL) {
        $stream_context_create_options['ssl'] = array(
          'verify_peer' => true,
          'verify_host' => true,
          'cafile'      => CUSTOM_CA_FILE
        );
      } // if

      // request options, needed for sending headers
      $context = stream_context_create($stream_context_create_options);

      // track progress
      if ($progress_callback) {
        stream_context_set_params($context, array(
          'notification' => function ($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) use ($progress_callback, &$size_of_the_download) {
            if (in_array($notification_code, array(STREAM_NOTIFY_FILE_SIZE_IS, STREAM_NOTIFY_PROGRESS, STREAM_NOTIFY_COMPLETED))) {
              if ($bytes_max > $size_of_the_download && $bytes_max > 1024) {
                $size_of_the_download = $bytes_max;
              } else {
                $bytes_max = $size_of_the_download;
              } // if

              $progress_callback($bytes_max, $bytes_transferred);
            } // if
          }
        ));
      } // if

      // open download handle
      $download_handle = fopen($url, 'rb', false, $context);
      if (!$download_handle) {
        throw new Error('Cannot find the update package');
      } // if

      $response_headers = parse_headers(trim(implode("\n", $http_response_header)));

      // download the file in chunks of 4kb
      while (!feof($download_handle)) {
        fwrite($write_handle, fread($download_handle, 1048576));
        fflush($write_handle);
      } // while

      // close the handles
      fclose($write_handle);
      fclose($download_handle);
    } else {
      throw new Exception(lang('Your server is not able to download updates'));
    } // if

    // extract the filename
    $filename = false;
    $content_disposition = array_var($response_headers, 'content-disposition');
    if ($content_disposition) {
      $content_disposition_parts = explode(';', $content_disposition);
      foreach ($content_disposition_parts as $content_disposition_part) {
        $content_disposition_part = trim($content_disposition_part);
        if (strpos($content_disposition_part, 'filename=') === 0) {
          $filename = trim(substr($content_disposition_part, 9), " \t\n\r\0\x0B\"\'");
          break;
        } // if
      } // foreach
    } // if

    if ($content_disposition && $destination_directory && $filename) {
      $new_destination_file = $destination_directory . '/' . $filename;

      // check if work file already exists and can't be deleted
      if (is_file($new_destination_file) && !@unlink($new_destination_file)) {
        throw new Error('File :filename already exists and cannot be deleted', array('filename' => $new_destination_file));
      } // if

      // try to rename temporary file to it's right name
      if (!rename($destination_file, $new_destination_file)) {
        throw new Error(lang('Failed to rename file to :filename', array('filename' => $filename)));
      };

      $destination_file = $new_destination_file;
    } // if

    return array($filename, $destination_file, filesize($destination_file), $response_headers);
  } // download_from_server

  /**
   * Get response from remote server
   *
   * @param $url
   * @param null $request_headers
   */
  function response_from_server($url, $request_headers = null) {
    $proxy_settings = ConfigOptions::getValue(array(
      'network_proxy_enabled',
      'network_proxy_protocol',
      'network_proxy_address',
      'network_proxy_port'
    ));

    if (function_exists('curl_init')) {
      // initialise curl
      $curl = curl_init($url);

      // set curl options
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

      if ($request_headers) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers); // send request headers
      } // if

      if ($proxy_settings['network_proxy_enabled']) {
        curl_setopt($curl, CURLOPT_PROXY, $proxy_settings['network_proxy_address'] . ':' . $proxy_settings['network_proxy_port']);
      } // if

      // get response
      $response = curl_exec($curl);
      // close connection
      curl_close($curl);
      // return response
      return $response;
    } else if (ini_get('allow_url_fopen')) {
      $stream_context_http_params = array(
        'method' => 'GET',
      );

      // if there are request headers
      if ($request_headers) {
        $stream_context_http_params['header'] = implode("\r\n", $request_headers) . "\r\n";
      } // if

      // if proxy is enabled
      if ($proxy_settings['network_proxy_enabled']) {
        $prefix = strtolower($proxy_settings['network_proxy_protocol']) == 'http' ? 'tcp' : 'ssl';
        $stream_context_http_params['proxy'] = $prefix . '://' . $proxy_settings['network_proxy_address'] . ':' . $proxy_settings['network_proxy_port'];
        $stream_context_http_params['request_fulluri'] = true;
      } // if

      // default stream context create options
      $stream_context_create_options = array(
        'http' => $stream_context_http_params
      );

      // create stream context create
      $stream_context_create = stream_context_create($stream_context_create_options);

      // perform the request and get contents
      return file_get_contents($url, false, $stream_context_create);
    } // if
  } // response_from_server

  /**
   * Get application user agent
   *
   * @return string
   */
  function get_application_user_agent() {
    return strtolower(APPLICATION_NAME) . ' v' . AngieApplication::getVersion();
  } // get_activecollab_user_agent

  /**
   * Get headers needed for authentication
   *
   * @return array
   */
  function get_application_authentication_headers() {
    return array(
      'User-Agent: ' . get_application_user_agent(),
      'ac-root-url: ' . ROOT_URL,
      'ac-license-key: ' . LICENSE_KEY,
      'ac-user-id: ' . LICENSE_UID
    );
  } // get_application_authentication_headers

  /**
   * Parse http header, and return array with key => values
   *
   * @param string $header
   * @return array
   */
  function parse_headers($headers) {
    $headers = explode("\n", $headers);
    $output = array();

    if ('HTTP' === substr($headers[0], 0, 4)) {
      list(, $output['status'], $output['status_text']) = explode(' ', trim($headers[0]));
      unset($headers[0]);
    } // if

    foreach ($headers as $v) {
      $h = preg_split('/:\s*/', $v);
      $output[strtolower($h[0])] = trim($h[1]);
    } // foreach

    return $output;
  } // parse_headers

  /**
   * This function will walk recursively through array and strip slashes from
   * scalar values
   *
   * @param array $array
   * @return array
   */
  function array_stripslashes(&$array) {
    if(is_array($array)) {
      foreach($array as $k => $v) {
        if(is_array($array[$k])) {
          array_stripslashes($array[$k]);
        } else {
          $array[$k] = stripslashes($array[$k]);
        } // if
      } // foreach
    } // if

    return $array;
  } // array_stripslashes
  
  /**
   * Check and set a valid protocol for a given URL
   * 
   * This function will check if $url has a protocol part and if it does not have 
   * it will add it. If $ignore_empty is set to true and $url is an emapty string 
   * empty string will be returned back (good for optional URL fields).
   *
   * @param string $url
   * @param boolean $ignore_empty
   * @param string $protocol
   * @return string
   */
  function valid_url_protocol($url, $ignore_empty = false, $protocol = 'http') {
    $trimmed = trim($url);
    if(($trimmed == '') && $ignore_empty) {
      return '';
    } // if
    
    if(strpos($trimmed, '://') === false) {
      return "$protocol://$trimmed";
    } else {
      return $trimmed;
    } // if
  } // valid_url_protocol
  
  
  /**
   * Replace spaces in URLs with %20
   *
   * @param string $url
   * @return string
   */
  function replace_url_spaces($url) {
    return str_replace(' ', '%20', $url);
  } // replace_url_spaces
  
//  /**
//   * Known user agents
//   */
//  define('USER_AGENT_IPHONE', 'iphone');
//  define('USER_AGENT_IPOD_TOUCH', 'ipodtouch');
//  define('USER_AGENT_SAFARI', 'safari');
//  define('USER_AGENT_FIREFOX', 'firefox');
//  define('USER_AGENT_CAMINO', 'camino');
//  define('USER_AGENT_OPERA', 'opera');
//  define('USER_AGENT_IE', 'ie');
//  define('USER_AGENT_NETSCAPE', 'netscape');
//  define('USER_AGENT_KONQUEROR', 'konqueror');
//  define('USER_AGENT_SYMBIAN', 'symbian');
//  define('USER_AGENT_OPERA_MINI', 'opera_mini');
//  define('USER_AGENT_OPERA_MOBILE', 'opera_mobile');
//  define('USER_AGENT_ANDROID', 'android');
//  define('USER_AGENT_BLACKBERRY','blackberry');
//  define('USER_AGENT_MOBILE_IE', 'mobile_ie');
//  
//  define('USER_AGENT_DEFAULT', 'default'); 
//  define('USER_AGENT_DEFAULT_MOBILE', USER_AGENT_IPHONE);
//  
//  /**
//   * Determines user agent
//   * 
//   * This function will detemine user agent, and store it in USER_AGENT
//   * 
//   * @return void
//   */
//  function get_user_agent() {
//    $user_agent = array_var($_SERVER, 'HTTP_USER_AGENT');
//    
//    $known_user_agents = array(
//      array("pattern" => "/MSIE(.*)IEMobile/", "device_name" => USER_AGENT_MOBILE_IE),
//      array("pattern" => "/BlackBerry/", "device_name" => USER_AGENT_BLACKBERRY),
//      array("pattern" => "/Linux(.*)Android(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_ANDROID),
//      array("pattern" => "/iPhone(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_IPHONE),  
//      array("pattern" => "/iPod(.*)AppleWebKit(.*)KHTML(.*)Mobile/", "device_name" => USER_AGENT_IPOD_TOUCH),
//      array("pattern" => "/SymbianOS(.*)AppleWebKit(.*)KHTML(.*)Safari/", "device_name" => USER_AGENT_SYMBIAN),
//      array("pattern" => "/AppleWebKit(.*)KHTML(.*)Safari/", "device_name" => USER_AGENT_SAFARI),
//      array("pattern" => "/Gecko(.*)Firefox/", "device_name" => USER_AGENT_FIREFOX),
//      array("pattern" => "/Gecko(.*)Camino/", "device_name" => USER_AGENT_CAMINO),
//      array("pattern" => "/Gecko(.*)Netscape/", "device_name" => USER_AGENT_NETSCAPE),
//      array("pattern" => "/Opera(.*)Opera Mini/", "device_name" => USER_AGENT_OPERA_MINI),
//      array("pattern" => "/MSIE(.*)Windows NT(.*)SV1(.*)Opera/", "device_name" => USER_AGENT_OPERA_MOBILE),
//      array("pattern" => "/MSIE(.*)Windows CE(.*)Opera(.*)/", "device_name" => USER_AGENT_OPERA_MOBILE),
//      array("pattern" => "/MSIE(.*)Symbian OS(.*)Opera(.*)/", "device_name" => USER_AGENT_OPERA_MOBILE),
//      array("pattern" => "/Opera/", "device_name" => USER_AGENT_OPERA),
//      array("pattern" => "/compatible(.*)MSIE/", "device_name" => USER_AGENT_IE),
//      array("pattern" => "/compatible(.*)Konqueror/", "device_name" => USER_AGENT_KONQUEROR),
//    );
//    
//    foreach ($known_user_agents as $known_user_agent) {
//    	if (preg_match($known_user_agent["pattern"], $user_agent)) {
//    	  define('USER_AGENT', $known_user_agent['device_name']);
//    	  return null;
//    	} // if
//    } // foreach
//    
//    if (!defined('USER_AGENT')) {
//      define('USER_AGENT', USER_AGENT_DEFAULT);
//    } // if
//    
//  } // get_user_agent
//  
//  get_user_agent();
//  
//  /**
//   * Check is @user_agent is mobile device
//   *
//   * @param string $user_agent
//   * @return boolean
//   */
//  function is_mobile_device($user_agent) {
//    return in_array($user_agent,array(
//      USER_AGENT_IPHONE,
//      USER_AGENT_IPOD_TOUCH,
//      USER_AGENT_SYMBIAN,
//      USER_AGENT_OPERA_MINI,
//      USER_AGENT_ANDROID,
//      USER_AGENT_BLACKBERRY,
//      USER_AGENT_MOBILE_IE,
//      USER_AGENT_OPERA_MOBILE
//    ));
//  } // is_mobile_device
  
  // ---------------------------------------------------
  //  HTML generators
  // ---------------------------------------------------
  
  /**
   * Open HTML tag
   *
   * @param string $name Tag name
   * @param array $attributes Array of tag attributes
   * @param boolean $empty If tag is empty it will be automaticly closed
   * @return string
   */
  function open_html_tag($name, $attributes = null, $empty = false) {
    $attribute_string = '';
    if(is_array($attributes) && count($attributes)) {
      $prepared_attributes = array();
      foreach($attributes as $k => $v) {
        if(trim($k) <> '') {
          
          if(is_bool($v)) {
            if($v) $prepared_attributes[] = "$k=\"$k\"";
          } else {
            $prepared_attributes[] = $k . '="' . clean($v) . '"';
          } // if
          
        } // if
      } // foreach
      $attribute_string = implode(' ', $prepared_attributes);
    } // if
    
    $empty_string = $empty ? ' /' : ''; // Close?
    return "<$name $attribute_string$empty_string>"; // And done...
  } // html_tag
  
  /**
   * Render form label element. This helper makes it really simple to mark reqired elements
   * in a standard way
   *
   * @param string $text Label content
   * @param string $for ID of related elementet
   * @param boolean $is_required Mark as a required fiedl
   * @param array $attributes Additional attributes
   * @param string $after_label Label text sufix
   * @return string
   */
  function label_tag($text, $for = null, $is_required = false, $attributes = null, $after_label = ':') {
    if(trim($for)) {
      if(is_array($attributes)) {
        $attributes['for'] = trim($for);
      } else {
        $attributes = array('for' => trim($for));
      } // if
    } // if
    
    $render_text = trim($text) . $after_label;
    if($is_required) {
      $render_text .= ' <span class="label_required">*</span>';
    } // if
    
    return open_html_tag('label', $attributes) . $render_text . '</label>';
  } // label_tag
  
  /**
   * Render radio field
   *
   * @param string $name Field name
   * @param mixed $value
   * @param boolean $checked
   * @param array $attributes Additional attributes
   * @return string
   */
  function radio_field($name, $checked = false, $attributes = null) {
    if(is_array($attributes)) {
      $attributes['type'] = 'radio';
      if(!isset($attributes['class'])) {
        $attributes['class'] = 'inline';
      } // if
    } else {
      $attributes = array('type' => 'radio', 'class' => 'inline');
    } // if
    
    // Value
    $value = array_var($attributes, 'value', false);
    if($value === false) {
      $value = 'checked';
    } // if
    
    // Checked
    if($checked) {
      $attributes['checked'] = 'checked';
    } else {
      if(isset($attributes['checked'])) {
        unset($attributes['checked']);
      } // if
    } // if
    
    $attributes['name'] = $name;
    $attributes['value'] = $value;
    
    return open_html_tag('input', $attributes, true);
  } // radio_field
  
  /**
   * Render select list box
   * 
   * Options is array of already rendered option and optgroup tags
   *
   * @param array $options Array of already rendered option and optgroup tags
   * @param array $attributes Additional attributes
   * @return string
   */
  function select_box($options, $attributes = null) {    
    $output = open_html_tag('select', $attributes) . "\n";
    if(is_array($options)) {
      foreach($options as $option) {
        $output .= $option . "\n";
      } // foreach
    } // if
    
    $output.= '</select>' . "\n";
    return $output;
  } // select_box
  
  /**
   * Render option tag
   *
   * @param string $text Option text
   * @param mixed $value Option value
   * @param array $attributes
   * @return string
   */
  function option_tag($text, $value = null, $attributes = null) {
    if(!is_null($value)) {
      if(is_array($attributes)) {
        $attributes['value'] = $value;
      } else {
        $attributes = array('value' => $value);
      } // if
    } // if
    return open_html_tag('option', $attributes) . clean($text) . '</option>';
  } // option_tag
  
  /**
   * Render option group
   *
   * @param string $label Group label
   * @param array $options
   * @param array $attributes
   * @return string
   */
  function option_group_tag($label, $options, $attributes = null) {
    if(is_array($attributes)) {
      $attributes['label'] = $label;
    } else {
      $attributes = array('label' => $label);
    } // if
    
    $output = open_html_tag('optgroup', $attributes) . "\n";
    if(is_array($options)) {
      foreach($options as $option) {
        $output .= $option . "\n";
      } // foreach
    } // if
    return $output . '</optgroup>' . "\n";
  } // option_group_tag
  
  /**
   * Extend url with additional parameters
   *
   * @param string $url
   * @param array $extend_with
   * @return string
   */
  function extend_url($url, $extend_with) {
    if (!$url || !is_foreachable($extend_with)) {
      return $url;
    } // if
    
    $extended_url = $url;
    foreach ($extend_with as $extend_element_key => $extend_element_value) {
      if (strpos($extended_url,  '?') === false) {
        $extended_url .= '?';
      } else {
        $extended_url .= '&';
      } // if

      if(is_array($extend_element_value)) {
        foreach($extend_element_value as $k => $v) {
          $extended_url .= $extend_element_key . '[' . $k . ']=' . $v;
        } // foreach
      } else {
        $extended_url .= $extend_element_key . '=' . $extend_element_value;
      } // if
    } // foreach
    
    return $extended_url;
  } // extend_url

  /**
   * Checks if server is windows
   *
   * @return boolean
   */
  function is_windows_server() {
    return strtoupper(substr(PHP_OS, 0, 3) == 'WIN');
  }