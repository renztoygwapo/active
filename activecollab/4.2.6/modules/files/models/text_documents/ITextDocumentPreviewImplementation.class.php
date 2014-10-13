<?php 

  /**
   * Text document preview implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class ITextDocumentPreviewImplementation extends IPreviewImplementation {
    
    /**
     * Construct download preview implementation
     *
     * @param IPreview $object
     */
    function __construct(IPreview $object) {
      if($object instanceof TextDocument) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'TextDocument');
      } // if
    } // __construct
        
    /**
     * Does this object has preview
     * 
     * @param void
     * @return boolean
     */
    function has() {
      return true;
    } // has
    
    /**
     * Render small preview
     *
     * @return string
     */
    function renderSmall() {
      return $this->renderPreview(80, 80);
    } // renderSmall
    
    /**
     * Render large preview
     *
     * @return string
     */
    function renderLarge() {
      return $this->renderPreview(550, 300);
    } // renderLarge
    
    /**
     * Renders the small icon url
     * 
     * @param void
     * @return string
     */
    function getSmallIconUrl() {
      return AngieApplication::getImageUrl('icons/16x16/text-document.png', FILES_MODULE);
    } // getSmallIconUrl
    
    /**
     * Returns the large icon
     * 
     * @param void
     * @return string
     */
    function getLargeIconUrl() {
      return AngieApplication::getImageUrl('icons/32x32/text-document.png', FILES_MODULE);
    } // getLargeIconUrl
    
    /**
     * Do the render
     * 
     * @param integer $width
     * @param integer $height
     * @return string
     */
    function renderPreview($width, $height) {
    	return 'text_document';
    } // renderPreview
  }