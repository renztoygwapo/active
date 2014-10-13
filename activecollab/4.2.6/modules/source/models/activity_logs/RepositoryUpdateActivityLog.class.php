<?php

  /**
   * Repository update activity log handler
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class RepositoryUpdateActivityLog extends ProjectObjectActivityLog {
  	
    /**
     * Action name
     *
     * @var string
     */
    protected $action_name = 'Updated';
    
    /**
     * Return log icon URL
     *
     * @return string
     */
    function getIconUrl() {
      return AngieApplication::getImageUrl('activity-log/updated.png', SOURCE_MODULE, AngieApplication::getPreferedInterface());
    } // getIconUrl
    
    /**
     * Cached head content
     *
     * @var boolean
     */
    private $head = false;
    
    /**
     * Render log details
     *
     * @return string
     */
    function renderHead($context = null, $interface = null) {
      if($this->head === false) {
      	if(empty($interface)) {
		      $interface = AngieApplication::INTERFACE_DEFAULT;
		    } // if
      	
        $lang_params = array(
          'url' => $this->getParent()->getViewUrl(),
          'user_name' => $this->getParent()->getName(),
        );
        
        if($context instanceof Project) {
        	$this->head = $interface == AngieApplication::INTERFACE_PHONE ? 
          	lang('Repository has been updated', $lang_params) : 
          	lang('Repository has been updated', $lang_params);
        } else {
          $lang_params['project_name'] = $this->getParent()->getProject()->getName(); 
          $lang_params['project_view_url'] = $this->getParent()->getProject()->getViewUrl();
          
          $this->head = $interface == AngieApplication::INTERFACE_PHONE ? 
          	lang('Repository has been updated in ":project_name" project', $lang_params) : 
          	lang('Repository has been updated in <a href=":project_view_url">:project_name</a> project', $lang_params);
        } // if
      } // if
      
      return $this->head;
    } // render
    
  }