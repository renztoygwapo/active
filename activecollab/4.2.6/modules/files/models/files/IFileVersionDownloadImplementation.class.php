<?php

  /**
   * File versions download helper implementation
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IFileVersionDownloadImplementation extends IDownloadImplementation {
    
    /**
     * Construct new file version
     *
     * @param FileVersion $object
     */
    function __construct(FileVersion $object) {
      if($object instanceof FileVersion) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'FileVersion');
      } // if
    } // __construct
    
  }

?>