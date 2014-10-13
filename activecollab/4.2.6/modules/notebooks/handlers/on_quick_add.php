<?php

  /**
   * Notebooks module on_quick_add event handler
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function notebooks_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
    $item_id = 'notebook';

    if(is_foreachable($projects)) {
      foreach($projects as $project) {
        if(Notebooks::canAdd($logged_user, $project)) {
          $map[$item_id][] = 'project_' . $project->getId();
        } // if
      } // foreach
      
      if(isset($map[$item_id])) {
		    $items->add($item_id, array(
		      'text'	=> lang('Notebook'),
		    	'title' => lang('Add Notebook to the Project'),
		    	'dialog_title' => lang('Add Notebook to the :name Project'),
		    	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/notebook.png', NOTEBOOKS_MODULE) : AngieApplication::getImageUrl('icons/96x96/notebook.png', NOTEBOOKS_MODULE, $interface),
		      'url'		=> Router::assemble('project_notebooks_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
		    	'event' => 'notebook_created',
		    ));
      } // if
    } // if

  } // notebooks_handle_on_project_tabs