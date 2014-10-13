{title}Overview{/title}
{add_bread_crumb}Overview{/add_bread_crumb}

<div id="project_home">
  <div class="project_home_left"><div class="project_home_left_inner">
    {if Milestones::canAccess($logged_user, $active_project)}
      {if is_foreachable($late_and_today)}
      <div id="late_today" class="project_overview_box">
        <div class="project_overview_box_title">
          <h2>{lang}Late / Today Milestones{/lang}</h2>
        </div>
        <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
					<table class="common" cellspacing="0">
            <thead>
              <tr>
	              <th class="milestone">{lang}Milestone{/lang}</th>
	              <th class="responsible">{lang}Responsible Person{/lang}</th>
	              <th class="due">{lang}Due On{/lang}</th>
              </tr>
            </thead>
						<tbody>
							{foreach from=$late_and_today item=object}
						  <tr class="{if $object->isLate()}late{elseif $object->isUpcoming()}upcoming{else}today{/if}">
                <td class="milestone"><a href="{$object->getViewUrl()}" class="quick_view_item">{$object->getName()}</a></td>
								<td class="responsible">
									{if $object->assignees()->hasAssignee()}
									 <span class="details block">{user_link user=$object->assignees()->getAssignee()}</span>
                  {else}
                    ---
									{/if}
								</td>
								<td class="due">{due_on object=$object}</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
        </div></div>
      </div>
      {/if}
      
      {if is_foreachable($upcoming_objects)}
        <div id="upcoming" class="project_overview_box">
          <div class="project_overview_box_title">
            <h2>{lang}Upcoming Milestones{/lang}</h2>
          </div>
          <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
            <table class="common" cellspacing="0">
	            <thead>
	              <tr>
		              <th class="milestone">{lang}Milestone{/lang}</th>
		              <th class="responsible">{lang}Responsible Person{/lang}</th>
		              <th class="due">{lang}Due On{/lang}</th>
	              </tr>
	            </thead>
              <tbody>
              {foreach from=$upcoming_objects item=object}
                <tr class="{if $object->isLate()}late{elseif $object->isUpcoming()}upcoming{else}today{/if}">
                  <td class="milestone"><a href="{$object->getViewUrl()}" class="quick_view_item">{$object->getName()}</a></td>
                  <td class="responsible">
	                  {if $object->assignees()->hasAssignee()}
	                    <span class="details block">{user_link user=$object->assignees()->getAssignee()}</span>
	                  {else}
	                    ---
	                  {/if}
                  </td>
                  <td class="due">{due_on object=$object}</td>
                </tr>
              {/foreach}
              </tbody>
            </table>
          </div></div>
        </div>
      {/if}
    {/if}
      
      <div class="project_overview_box" id="project_recent_activities">
        <div class="project_overview_box_title">
          <h2>{lang}Recent Activities{/lang}</h2>
        </div>
        <div class="project_overview_box_content">
          <div class="project_overview_box_content_inner">{activity_logs_in user=$logged_user in=$active_project}</div>
        </div>
      </div>
  </div></div>
  
  <div class="project_home_right" id="project_details">
    <div class="project_home_right_inner">
    
    {object object=$active_project user=$logged_user}
    	{if AngieApplication::isModuleLoaded($smarty.const.TASKS_MODULE)}
      	{if Tasks::canAccess($logged_user, $active_project)}	
       		<div id="project_progress">{project_progress project=$active_project}</div>
       	{/if}
      {/if}
     	 
    {/object}
    
    {if is_foreachable($home_sidebars)}
      {foreach from=$home_sidebars item=home_sidebar}
        {if $home_sidebar.body}
          <div class="project_overview_box {if !$home_sidebar.is_important}alt{/if}" id="{$home_sidebar.id}">
            <div class="project_overview_box_title">
              <h2>{$home_sidebar.label}</h2>
            </div>
            <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
              {$home_sidebar.body nofilter}
            </div></div>
          </div>
        {/if}
      {/foreach}
    {/if}
    
  </div></div>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('project_updated.{$request->getEventScope()}', function (event, project) {
    if (project['class'] == 'Project' && project.id == '{$active_project->getId()}') {
      var wrapper = $('#project_home');
      var logo_image = wrapper.find('#select_project_icon img:first');
      logo_image.attr('src', project.avatar.large);
      App.clean($('#wireframe_page_tabs').find('#page_tabs li.first a').html(App.excerpt(project['name'], 25)));
    } // if
  });

  App.Wireframe.Events.bind('project_completed.{$request->getEventScope()} project_opened.{$request->getEventScope()}', function (event, project) {
    var project_status = $('#project_details').find('.meta_status .meta_data');
    if(project.is_completed) {
      project_status.html(App.lang('Completed'));
    } else {
    	project_status.html(App.lang('Active'));
    } // if
  });

  App.Wireframe.Events.bind('create_invoice_from_project.{$request->getEventScope()}', function (event, invoice) {
    if (invoice['class'] == 'Invoice') {
    	App.Wireframe.Flash.success(App.lang('New invoice created.'));
    	App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
    } // if
  });

  App.Wireframe.Events.bind('project_settings_updated.{$request->getEventScope()}', function (event, project_settings) {
    // refresh the project tabs
    var project = project_settings.project;
    if (project) {

      // trigger project updated
      App.Wireframe.Events.trigger('project_updated', project);

      // if we are currently in right project, update it's tabs  
	    if (project.id == {$active_project->getId()}) {
        var settings = project_settings.settings;
        if (settings) {
          var tabs = settings.tabs;
          if (tabs) {
            App.Wireframe.PageTabs.batchSet(tabs);
            App.Wireframe.PageTabs.setAsCurrent('overview');
          } // if
        } // if	      
	    } // if    
    }
  });

  var wrapper = $('#project_at_a_glance');
  var project_id = {$active_project->getId()};
  var project_state = {$active_project->getState()};
  var projects_url = '{assemble route=projects}';

  App.Wireframe.Events.bind('project_deleted.{$request->getEventScope()}', function (event, project) {
    if (project['class'] == 'Project' && project['id'] == project_id) {
      if (!wrapper.parents('objects_list').length) {
        if (project.state == 1) {
          App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
        } else if (project.state == 0) {
          App.Wireframe.Content.setFromUrl(projects_url);
        } // if
      } // if
      project_state = project['state'];
    } // if
  });
  
</script>