<?php

  /**
   * activeCollab upgrade adapter
   * 
   * @package activeCollab.resources
   */
  class ActiveCollabUpgraderAdapter extends AngieApplicationUpgraderAdapter {

    /**
     * Validate login parameters and other conditions required for upgrade to work properly
     *
     * @param array $params
     * @return boolean
     */
    function validateBeforeUpgrade($params) {
      parent::validateBeforeUpgrade($params);

      // Check whether /public/template_covers is writable
      $template_covers_dir = PUBLIC_PATH . '/template_covers';

      if(!is_dir($template_covers_dir) && DIRECTORY_SEPARATOR != '\\') {
        @mkdir($template_covers_dir, 0777);
      } // if

      if(is_dir($template_covers_dir)) {
        if(folder_is_writable($template_covers_dir)) {
          $this->validationLogOk('/public/template_covers folder exists and it is writable');
        } else {
          $this->validationLogError('/public/template_covers folder is not writable. Make it writable to continue');
        } // if
      } else {
        $this->validationLogError('/public/template_covers folder does not exists and it is not writable. Create it and make it writable to continue');
      } // if

      return $this->everythingValid();
    } // validateBeforeUpgrade

  }