<?php

  /**
   * Framework level access log implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwAccessLogs extends BaseAccessLogs {
    
    /**
     * Returns true if $parent object was accessed by a given user since given 
     * date and time value
     * 
     * $parent can be IAccessLogs instance, or array where first element is 
     * parent class and the second element is parent ID
     * 
     * @param mixed $parent
     * @param IUser $by
     * @param DateTimeValue $since
     * @return bool
     * @throws InvalidInstanceError
     */
    static function isAccessedSince($parent, IUser $by, DateTimeValue $since) {
      if (is_array($parent) && isset($parent[0]) && isset($parent[1])) {
        list($parent_type, $parent_id) = $parent;
      } else if ($parent instanceof IAccessLog) {
        $parent_type = get_class($parent);
        $parent_id = $parent->getId();
      } else {
        throw new InvalidInstanceError('parent', $parent, array('array', 'IAccessLog'));
      } // if
      
      if($by instanceof IUser) {
        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'access_logs WHERE parent_type = ? AND parent_id = ? AND accessed_by_id = ? AND accessed_on >= ?', $parent_type, $parent_id, $by->getId(), $since);
      } else if($by instanceof AnonymousUser) {
        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'access_logs WHERE parent_type = ? AND parent_id = ? AND accessed_by_id = ? AND accessed_by_email = ? AND accessed_on >= ?', $parent_type, $parent_id, 0, $by->getEmail(), $since);
      } else {
        throw new InvalidInstanceError('by', $by, 'IUser');
      } // if
    } // isAccessedSince
    
    /**
     * Log access
     * 
     * @param IAccessLog $parent
     * @param IUser $by
     */
    static function log(IAccessLog $parent, IUser $by) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'access_logs (parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', get_class($parent), $parent->getId(), $by->getId(), $by->getDisplayName(), $by->getEmail(), AngieApplication::getVisitorIp());
    } // log

    /**
     * Log anonymous access
     *
     * @param IAccessLog $parent
     */
    static function logAnonymous(IAccessLog $parent) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'access_logs (parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)', get_class($parent), $parent->getId(), null, null, null, AngieApplication::getVisitorIp());
    } // logAnonymous


    /**
     * Log download
     * 
     * @param IAccessLog $parent
     * @param IUser $by
     */
    static function logDownload(IAccessLog $parent, IUser $by) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'access_logs (parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address, is_download) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', get_class($parent), $parent->getId(), $by->getId(), $by->getDisplayName(), $by->getEmail(), AngieApplication::getVisitorIp(), true);
    } // logDownload

    /**
     * Log anonymous download
     *
     * @param IAccessLog $parent
     */
    static function logAnonymousDownload(IAccessLog $parent) {
      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'access_logs (parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address, is_download) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?, ?)', get_class($parent), $parent->getId(), null, null, null, AngieApplication::getVisitorIp(), true);
    } // logDownload
    
    /**
     * Move old entries to archive
     */
    static function archive() {
      $reference = DateTimeValue::makeFromString('-30 days')->beginningOfDay();
      $access_logs_table = TABLE_PREFIX . 'access_logs';
      $access_logs_archive_table = TABLE_PREFIX . 'access_logs_archive';
      
      try {
        DB::beginWork('Moving access logs to archive @ ' . __CLASS__);
        
        DB::execute("INSERT INTO $access_logs_archive_table (parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address, is_download) SELECT parent_type, parent_id, accessed_by_id, accessed_by_name, accessed_by_email, accessed_on, ip_address, is_download FROM $access_logs_table WHERE accessed_on <= ?", $reference);
        DB::execute("DELETE FROM $access_logs_table WHERE accessed_on < ?", $reference);
        
        DB::commit('Access log entries moved to archive @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move access log entries to archive @ ' . __CLASS__);
        throw $e;
      } // try
    } // archive

	  /**
	   * Get all logs for given object
	   *
	   * @param IAccessLog $parent
	   * @return array
	   */
	  static function getAll(IAccessLog $parent) {
		  $result = array();

		  $parent_type = get_class($parent);
		  $parent_id = $parent->getId();

		  if ($parent instanceof Project) {
			  $logs = DB::execute("SELECT accessed_by_id, accessed_by_name, ip_address, accessed_on, is_download FROM " . TABLE_PREFIX . "access_logs WHERE parent_id = ? AND parent_type = ? GROUP BY accessed_by_id ORDER BY accessed_on DESC", $parent_id, $parent_type);
		  } else {
			  $logs = DB::execute("SELECT accessed_by_id, accessed_by_name, ip_address, accessed_on, is_download FROM " . TABLE_PREFIX . "access_logs WHERE parent_id = ? AND parent_type = ? ORDER BY accessed_on DESC", $parent_id, $parent_type);
		  } // if

		  if (is_foreachable($logs)) {
			  foreach($logs as $log) {
				  $accessed_on = new DateTimeValue(strtotime(array_var($log, 'accessed_on')));
				  $ip_address = array_var($log, 'ip_address', lang('unknown'));
				  $is_download = (boolean) array_var($log, 'is_download', false);
				  $text = (!$is_download) ? 'Accessed by <b>:name</b> from <b>:ip_address</b>' : 'Downloaded by <b>:name</b> from <b>:ip_address</b>';
				  $result[$accessed_on->format('Y-m-d')][] = array(
					  'text'          => lang($text, array(
						  'name'          => array_var($log, 'accessed_by_name'),
						  'ip_address'    => $ip_address == '127.0.0.1' || $ip_address == '::1' ? lang('localhost') : $ip_address
					  )),
					  'time'          => $accessed_on->formatTimeForUser()
				  );
			  } // foreach
		  } // if

		  return $result;
	  }
  
  }