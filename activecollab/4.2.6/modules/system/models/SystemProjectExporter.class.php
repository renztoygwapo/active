<?php

  class SystemProjectExporter extends ProjectExporter {
    
    /**
     * active module
     * 
     * @var string
     */
    protected $active_module = SYSTEM_MODULE;

    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = '/';
    
    /**
     * Export the companies and people
     * 
     * @param void
     * @return null
     */
    public function export() {
      parent::export();
      
      if ($this->section == 'system') {
        $this->storeAvatar('project_logo.gif',$this->project->avatar()->getUrl(),false);
        $this->renderTemplate('project_overview',$this->getDestinationPath('index.html'));
      } // if
    } // export
    
  } // SystemProjectExporter