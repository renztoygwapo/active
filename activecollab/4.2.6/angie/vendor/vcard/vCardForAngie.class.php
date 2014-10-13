<?php

  /**
   * vCard for Angie class
   * 
   * @package angie.vendor.vcard
   */
  final class vCardForAngie {
  	
  	/**
     * Zip vCards
     * 
     * @return void
     */
    static function compress_vcards() {
	  	require_once ANGIE_PATH . '/classes/PclZip.class.php';
	  	
	  	$compressed_vcards_file_path = VCARD_EXPORT_DIR_PATH . '.zip';
	  	
			// delete existing compressed files
			@unlink($compressed_vcards_file_path);
			
			$zip = new PclZip($compressed_vcards_file_path);
	    
	    // get all created vCards
			$vcards = get_files(VCARD_EXPORT_DIR_PATH, null, true);
	    
	    $result = $zip->add($vcards, PCLZIP_OPT_REMOVE_PATH, WORK_PATH);
		  if(!$result) {
		    throw new Exception(lang('Could not add vCards to archive file :archive', array('archive' => $compressed_vcards_file_path)));
		  } // if
	    
	    @chmod($compressed_vcards_file_path, 0777);
	    safe_delete_dir(VCARD_EXPORT_DIR_PATH, WORK_PATH);
	  } // compress_vcards
    
  }