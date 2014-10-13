{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_milestone user=$logged_user skip="due_on,start_on"}
	<div id="milestone{$active_milestone->getId()}">
  	<div class="milestone_date_range" id="milestone_date_range_{$active_milestone->getId()}"></div>
    <script type="text/javascript">
      App.widgets.MilestoneDateRange.set('milestone_date_range_{$active_milestone->getId()}', {
         'start_date' : {$active_milestone->getStartOn()|json nofilter},
         'end_date' : {$active_milestone->getDueOn()|json nofilter}
      });
    </script>
       	
  	<div class="wireframe_content_wrapper">
  	  {inline_tabs object=$active_milestone}
  	</div>
    
    <div class="wireframe_content_wrapper">
      {object_comments object=$active_milestone user=$logged_user show_first=yes}
    </div>    
  </div>
{/object}

<script type="text/javascript">
  var project_id = {$active_project->getId()|json nofilter};

	App.Wireframe.Events.bind('create_invoice_from_milestone.content', function (event, invoice) {
   	if (invoice['class'] == 'Invoice') {
   		App.Wireframe.Flash.success(App.lang('New invoice created.'));
   		App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
		} // if
	});

  App.Wireframe.Events.bind('milestone_created.content', function(event, milestone) {
    if (typeof(milestone) == 'object') {
      if (project_id !== milestone['project_id']) {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(milestone['urls']['view']);
        } // if
      } else {
        App.Wireframe.Content.setFromUrl(milestone['urls']['view']);
      } // if
    } // if
  });

	App.Wireframe.Events.bind('milestone_updated.content', function (event, milestone) {
   	if(typeof(milestone) == 'object' && milestone && milestone['project_id'] != {$active_project->getId()|json nofilter} && milestone['id'] == {$active_milestone->getId()}) {
     if (project_id == milestone['project_id']) {
       App.Wireframe.Content.setFromUrl(milestone['urls']['view']);
     } else {
       if ($.cookie('ac_redirect_to_target_project')) {
         App.Wireframe.Content.setFromUrl(milestone['urls']['view']);
       } else {
         App.Wireframe.Content.setFromUrl($('#page_tab_milestones').find('a').attr('href'));
       } // if
     } // if
    } // if
	});

  App.Wireframe.Events.bind('milestone_deleted.content', function (event, milestone) {
    if (milestone['id'] == {$active_milestone->getId()}) {
      if (milestone['state'] == 0) {
        App.Wireframe.Content.setFromUrl('{assemble route="project_milestones" project_slug=$active_project->getSlug()}');
      } // if
    } // if
  });
</script>	