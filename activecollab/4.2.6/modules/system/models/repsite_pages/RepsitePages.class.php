<?php 

	/**
	   * RepsitePages manager class
	   * 
	   * @package activeCollab.modules.system
	   * @subpackage models
	   */
  	class RepsitePages extends BaseRepsitePages {
  		
  	
  		static function isPageNameInUse($pagename, $exclude_pagename = null) {
	      if($exclude_pagename) {
	        $exclude_pagename_id = $exclude_pagename instanceof User ? $exclude_pagename->getId() : $exclude_pagename;

	        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'repsite_pages WHERE name = ? AND id != ?', $pagename, $exclude_pagename_id);
	      } else {
	        //return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'repsite_pages WHERE page_name = ? AND state > ?', $pagename, STATE_DELETED);
	        return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'repsite_pages WHERE name = ?', $pagename);
	      } // if
	    } // isCompanyNameInUse

	    static function pageNameURLencode($pagename) {
	    	return urlencode($pagename);
	    }


	    static function getRepsitePagesList() {
	    	//$rows = DB::execute('SELECT * FROM '. TABLE_PREFIX . 'repsite_pages where state != ?', STATE_DELETED);
	    	$rows = DB::execute('SELECT * FROM '. TABLE_PREFIX . 'repsite_pages ORDER BY id DESC');

	    	if(is_foreachable($rows)) {
	    		$result = array();
	    		foreach ($rows as $row) {
	    			$result[] = $row;
	    		}
	    		return $result;
	    	}	
	    	return null;
	    }

	    function getSlice($num = 10) {
	    	return RepsitePages::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
	    }


	    static function findByName($name) {
                return RepsitePages::find(array(
                        'conditions' => array('name = ?', $name),
                        'one' => true
                ));
        } // findByName

  	}