<?php

  /**
   * Repository created activity log class
   *
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class RepositoryCreatedActivityLog extends ProjectObjectActivityLog {
  	
    /**
     * Action name
     *
     * @var string
     */
    protected $action_name = 'Created';
    
    /**
     * Return log icon URL
     *
     * @param void
     * @return string
     */
    function getIconUrl() {
      return AngieApplication::getImageUrl('activity-log/added.png', SOURCE_MODULE, AngieApplication::getPreferedInterface());
    } // getIconUrl
    
    /**
     * Cached head content
     *
     * @var string
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
          'user_name' => $this->getParent()->getCreatedBy()->getDisplayName(true), 
          'user_url' => $this->getParent()->getCreatedBy()->getViewUrl(),
          'url' => $this->getParent()->getViewUrl(),
          'name' => $this->getParent()->getName(),
        );
        
        if($context instanceof Project) {
        	$this->head = $interface == AngieApplication::INTERFACE_PHONE ? 
          	lang('Added ":name" repository', $lang_params) : 
          	lang('Added <a href=":url">:name</a> repository', $lang_params);
        } else {
          $lang_params['project_name'] = $this->getParent()->getProject()->getName(); 
          $lang_params['project_view_url'] = $this->getParent()->getProject()->getViewUrl();
          
          $this->head = $interface == AngieApplication::INTERFACE_PHONE ? 
          	lang('Added ":name" repository in ":project_name" project', $lang_params) : 
          	lang('Added <a href=":url">:name</a> repository in <a href=":project_view_url">:project_name</a> project', $lang_params);
        } // if
      } // if
      
      return $this->head;
    } // render
    
  }