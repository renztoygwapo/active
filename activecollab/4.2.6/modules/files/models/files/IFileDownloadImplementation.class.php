<?php

  /**
   * File download implementation
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IFileDownloadImplementation extends IDownloadImplementation {

	  /**
	   * Parent object
	   *
	   * @var IDownload
	   */
	  protected $object;

	  /**
	   * Construct download helper
	   *
	   * @param File $object
	   */
	  function __construct(File $object) {
		  $this->object = $object;
	  } // __construct

	  /**
	   * Count downloads of file
	   *
	   * @return mixed
	   */
	  function count() {
		  return DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "access_logs WHERE parent_id = ? AND parent_type = ? AND is_download = ?", $this->object->getId(), $this->object->getType(), '1');
	  } // count

  }