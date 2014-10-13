{title lang=false}{$active_project->getName() nofilter}{/title}
{add_bread_crumb}At a Glance{/add_bread_crumb}

<div id="project_at_a_glance">

  <div id="project_at_a_glance_card" class="{if $active_project->getState() == $smarty.const.STATE_ARCHIVED || $active_project->getState() == $smarty.const.STATE_TRASHED}with_warning{/if}">
	  <div class="project_at_a_glance_header">
      {if $active_project->getState() == $smarty.const.STATE_ARCHIVED}
        <div class="project_brief_warning"><img src="{image_url name="icons/16x16/archive.png" module="environment"}" />{lang}This project is archived{/lang}</div>
        {elseif $active_project->getState() == $smarty.const.STATE_TRASHED}
        <div class="project_brief_warning"><img src="{image_url name="icons/16x16/empty-trash.png" module="environment"}" />{lang}This project is in trash{/lang}</div>
      {/if}

      <table>
	      <tr>
	        <td class="logo">
	          <a href="{$active_project->getViewUrl()}"><img src="{$active_project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt="{$active_project->getName()}" /></a>
	        </td>
	        <td class="main">
	          <h2><a href="{$active_project->getViewUrl()}">{$active_project->getName()}</a></h2>
	        </td>
	        {if AngieApplication::isModuleLoaded($smarty.const.TASKS_MODULE)}
  	        {if Tasks::canAccess($logged_user, $active_project)}
            	<td class="progress">{project_progress project=$active_project}</td>
            {/if}
          {/if}
	      </tr>
		  </table>
	  </div>

    {if $active_project->getOverview()}
    <div class="project_at_a_glance_body">
      <div class="formatted_content">{$active_project->getOverview()|rich_text nofilter}</div>
    </div>
    {/if}
    
    <table class="project_at_a_glance_other">
      <tr>
        <td>
			    <div class="project_at_a_glance_meta">
			      <dl>
			        <dt>{lang}Leader{/lang}:</dt>
			        <dd>{user_link user=$active_project->getLeader()}</dd>
			        
			        {if $active_project->getCompany() instanceof Company}
			        <dt>{lang}Client{/lang}:</dt>
			        <dd><a href="{$active_project->getCompany()->getViewUrl()}" class="quick_view_item">{$active_project->getCompany()->getName()}</a></dd>
			        {/if}
			        
			        {if $logged_user->canSeeProjectBudgets() && $active_project->getBudget()}
			        <dt>{lang}Budget{/lang}:</dt>
			        <dd>{project_budget project=$active_project user=$logged_user}</dd>
			        {/if}
			        
			        {if $active_project->category()->get() instanceof ProjectCategory}
			        <dt>{lang}Category{/lang}:</dt>
			        <dd>{$active_project->category()->get()->getName()}</dd>
			        {/if}
			        
			        {if $active_project->getBasedOn() instanceof ProjectRequest}
			        <dt>{lang}Based On{/lang}:</dt>
			        <dd><a href="{$active_project->getBasedOn()->getViewUrl()}" class="quick_view_item">{$active_project->getBasedOn()->getName()}</a></dd>
			        {/if}
			        
			        <dt>{lang}Status{/lang}:</dt>
			        <dd>{$active_project->getVerboseStatus()}</dd>
			        
			        
			        {if $active_project->label()->get() instanceof ProjectLabel}
				      <dt>{lang}Label{/lang}:</dt>
				      <dd>{object_label object=$active_project}</dd>
			        {/if}

            {foreach CustomFields::getEnabledCustomFieldsByType('Project') as $field_name => $details}
              {if $active_project->getFieldValue($field_name)}
                <dt>{$details.label}:</dt>
                <dd>{$active_project->getFieldValue($field_name)}</dd>
              {/if}
            {/foreach}
			      </dl>
			    </div>
        </td>
        <td>
			    {if is_foreachable($project_brief_stats)}    
			    <div class="project_at_a_glance_details">
			      <ul>
			        {foreach from=$project_brief_stats item=project_brief_stat}
			          <li>{$project_brief_stat nofilter}</li>
			        {/foreach}
			      </ul>
			    </div>
			    {/if}
        </td>
      </tr>
    </table>

    {if $active_project->getState() > $smarty.const.STATE_ARCHIVED}
      <div class="project_at_a_glance_people">
        {assign var=project_people value=$active_project->users()->get($logged_user, $smarty.const.STATE_VISIBLE)}
        {if is_foreachable($project_people)}
          <ul>
            {foreach from=$project_people item=project_person}
              <li><a href="{$project_person->getViewUrl()}" title="{$project_person->getDisplayName()}"><img src="{$project_person->avatar()->getUrl()}" /></a></li>
            {/foreach}
          </ul>
        {/if}

        {if $active_project->canManagePeople($logged_user)}
            <div class="project_at_a_glance_people_manage_people">
                <a href="{assemble route=project_people project_slug=$active_project->getSlug()}">{lang}Manage People on this Project{/lang}</a>
            </div>
        {/if}
      </div>
    {/if}

    <div class="section_button_wrapper brief_action_button">
      {if $active_project->getState() == $smarty.const.STATE_ARCHIVED}
        <a class="section_button" href="{$active_project->state()->getUnarchiveUrl()}" id="project_brief_unarchive_project"><span><img src="{image_url name='icons/12x12/restore-from-archive.png' module='environment'}">{lang}Restore From Archive{/lang}</span></a>
      {elseif $active_project->getState() == $smarty.const.STATE_TRASHED}
        <a class="section_button" href="{$active_project->state()->getUntrashUrl()}" id="project_brief_untrash_project"><span><img src="{image_url name='icons/12x12/restore-from-trash.png' module='environment'}">{lang}Restore From Trash{/lang}</span></a>
      {else}
        <a class="section_button" href="{$active_project->getViewUrl()}"><span><img src="{image_url name='icons/16x16/go-to-project.png' module='environment'}">{lang}Go to the Project{/lang}</span></a>
      {/if}
    </div>
  </div>

  {if $active_project->getState() != $smarty.const.STATE_TRASHED}
    {assign_var name=user_tasks_url}{assemble route=project_user_tasks project_slug=$active_project->getSlug()}{/assign_var}
    {assign_var name=user_subscriptions_url}{assemble route=project_user_subscriptions project_slug=$active_project->getSlug()}{/assign_var}
    <ul class="project_at_glance_links">
      <li id="show_me_assignments">{link href=$user_tasks_url}My Assignments{/link}</li>
      <li id="show_me_subscriptions">{link href=$user_subscriptions_url}My Subscriptions{/link}</li>
      {if $logged_user->isFeedUser()}
        {assign_var name=ical_subscribe_url}{assemble route=project_ical_subscribe project_slug=$active_project->getSlug()}{/assign_var}
      <li id="show_me_ical">{link href=$ical_subscribe_url}iCalendar Feed{/link}</li>
      {if $logged_user->isFeedUser()}
      <li id="show_me_rss">{link href=$active_project->getRssUrl($logged_user) target='_blank'}RSS Feed{/link}</li>
      {/if}
      {/if}
    </ul>
  {/if}
</div>

<script type="text/javascript">
(function() {
  var wrapper = $('#project_at_a_glance');
  var project_id = {$active_project->getId()|json nofilter};
  var project_state = {$active_project->getState()|json nofilter};
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

  App.Wireframe.Events.bind('project_updated.{$request->getEventScope()}', function (event, project) {
	  if (project['class'] == 'Project' && project['id'] == project_id) {
      // update logo
	    var logo_image = wrapper.find('#select_project_icon img:first');
	    logo_image.attr('src', project.avatar.large);

      // if project is untrashed
      if (project_state == 1 && project['state'] != 1) {
        App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
      } // if

      if (project_state == 2 && project['state'] > 2) {
        App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
      } // if
      project_state = project['state'];
	  } // if
  });

  $('#page_action_restore_from_trash a, #project_brief_untrash_project').asyncLink({
    'confirmation'    : App.lang('Are you sure that you want to restore this :object_type from trash?', {
      'object_type' : App.lang('project')
    }),
    'success_message' : App.lang(':object_type has been successfully restored from trash', {
      'object_type' : App.lang('project')
    }),
    'success_event'   : 'project_updated'
  });

  $('#project_brief_unarchive_project').asyncLink({
    'confirmation'    : App.lang('Are you sure that you want to restore this :object_type from archive?', {
      'object_type' : App.lang('project')
    }),
    'success_message' : App.lang(':object_type has been successfully restored from archive', {
      'object_type' : App.lang('project')
    }),
    'success_event'   : 'project_updated'
  })

})();
</script>