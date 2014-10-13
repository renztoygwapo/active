<?php

  /**
   * Tracking exporter
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class TrackingExporter extends ProjectExporter {
  	
  	/**
  	 * active module
  	 * 
  	 * @var string
  	 */
  	protected $active_module = TRACKING_MODULE;
  	
    /**
     * Relative path where exported files will be stored
     * 
     * @var String
     */
    protected $relative_path = 'tracking';
        
    /**
     * Export the tracked data
     */
    public function export() {
      parent::export();
      
      if ($this->section == 'tracking') {
	      // render tracking index page
  	    $tracking_objects = TrackingObjects::findAllByProject($this->project, STATE_ARCHIVED, $this->getObjectsVisibility());
  	    $interface = AngieApplication::INTERFACE_DEFAULT;
  	    $timerecord_icon = $this->storeFile(ASSETS_URL . "/images/tracking/$interface/icons/16x16/time-record.png", ASSETS_URL . "/images/tracking/$interface/icons/16x16/time-record.png");
  	    $expense_icon = $this->storeFile(ASSETS_URL . "/images/tracking/$interface/icons/16x16/expense.png", ASSETS_URL . "/images/tracking/$interface/icons/16x16/expense.png");
	    
  	    $this->smarty->assignByRef('csv_file_path',$this->getDestinationPath('index.csv'));
  	    $this->smarty->assignByRef('timerecord_icon', $timerecord_icon);
  	    $this->smarty->assignByRef('expense_icon', $expense_icon);
  	    $this->smarty->assignByRef('tracking_objects', $tracking_objects);
  	    $this->renderTemplate('tracking_index', $this->getDestinationPath('index.html'));
  	    $this->smarty->clearAssign('tracking_objects');
      } // if
    
	    return true;
    } // export
    
  } // ProjectExporter