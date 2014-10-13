<?php

  /**
   * Framework level thumbnails implementation
   *
   * @package angie.frameworks.preview
   * @subpackage models
   */
  abstract class FwThumbnails {
    
    /**
     * Return thumbnail URL
     *
     * @param string $source
     * @param integer $width
     * @param integer $height
     * @return string
     * @throws InvalidParamError
     * @throws FileDnxError
     */
    static function getUrl($source, $width, $height) {
      if(str_starts_with($source, UPLOAD_PATH)) {
        $width = (integer) $width;
        $height = (integer) $height;
        
        return AngieApplication::getProxyUrl('forward_thumbnail', ENVIRONMENT_FRAMEWORK_INJECT_INTO, array(
          'name' => basename($source), 
          'width' => $width < 1 ? 80 : $width, 
          'height' => $height < 1 ? 80 : $height, 
          'ver' => is_file($source) ? filesize($source) : null,
        ));
      } else {
        throw new InvalidParamError('source', $source, 'Thumbnails can be created only from uploaded files');
      } // if
    } // getUrl

    /**
     * Remove all cached previews
     *
     * @return boolean
     */
    static public function cacheClear() {
      return empty_dir(THUMBNAILS_PATH, true);
    } // clear

    /**
     * calculate cached previews size
     */
    static public function cacheSize() {
      return dir_size(THUMBNAILS_PATH, true);
    } // cacheSize
    
  }