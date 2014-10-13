{title}Project Requests{/title}
{add_bread_crumb}List{/add_bread_crumb}
{use_widget name="objects_list" module="environment"}

<div id="project_requests">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Project Requests{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/projects.png' module=$smarty.const.SYSTEM_MODULE}" alt=""/></div>
      <div class="objects_list_details_actions">
          <ul>
            <li><a href="{assemble route='project_requests_add'}" id="new_project_request">{lang}New Project Request{/lang}</a></li>
          </ul>
      </div>
      
      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a project request and load its details, please click on it in the list on the left{/lang}</li>
          <li>{lang}It is possible to select multiple project requests at the same time. Just hold Ctrl key on your keyboard and click on all the projects that you want to select{/lang}</li>
        </ul>
      </div>
  </div>
</div>

<script type="text/javascript">
  $('#project_requests').each(function() {
    var wrapper = $(this);

    $('#new_project_request').flyoutForm({
      'title' : App.lang('New Project Request'),
      'success_event' : 'project_request_created'
    });

    var items = {$project_requests|json nofilter};

    var mass_edit_url = '{assemble route=project_requests_mass_edit}';
    var print_url = '{assemble route=project_requests print=1 project_slug=$active_project->getSlug()}';

    wrapper.objectsList({
      'id'                : 'project_requests',
      'items'             : items,
      'required_fields'   : ['id', 'is_closed', 'name', 'permalink', 'status', 'taken_by', 'taken_by_id'],
      'objects_type'      : 'project_requests',
      'events'            : App.standardObjectsListEvents(),
      'multi_title'       : App.lang(':num Project Requests Selected'),
      'multi_url'         : mass_edit_url,
      'multi_actions' : {$mass_manager|json nofilter},
      'print_url'         : print_url,
      'prepare_item'      : function (item) {
        return {
          'id' : item['id'],
          'name' : item['name'],
          'is_closed' : item['is_closed'] ? 1 : 0,
          'permalink' : item['permalink'],
          'status' : item['status'] ? item['status'] : 0,
          'taken_by' : item['taken_by'] ? item['taken_by']['short_display_name'] : null,
          'taken_by_id' : item['taken_by'] ? item['taken_by']['id'] : null
        };
      },
      'render_item'       : function (item) {
        switch(item['status']) {
          case 0:
            var status = '<span class="pill important">' + App.lang('New') + '</span>';
            break;
          case 1:
            var status = '<span class="pill">' + App.lang('Replied') + '</span>';
            break;
          case 2:
            var status = '<span class="pill ok">' + App.lang('Closed') + '</span>';
            break;
          default:
            var status = '<span class="pill">' + App.lang('Unknown') + '</span>';
        } // switch

        if(typeof(item['taken_by']) != 'undefined' && item['taken_by']) {
          if(typeof(item['taken_by']) == 'object') {
            var taken_by = item['taken_by']['short_display_name'] + ' ';
          } else {
            var taken_by = item['taken_by'] + ' ';
          } // if
          taken_by = '<strong>' + taken_by + '</strong>';
        } else {
          var taken_by = '';
        } // if

        return '<td class="name">' + taken_by + App.clean(item['name']) + '</td><td class="client right">' + status + '</td>';
      },

      'filtering'         : [{
        'label' : App.lang('Status'), 'property' : 'is_closed', 'values'  : [{
          'label' : App.lang('Active Requests'),
          'value' : '0',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'),
          'default' : true
        }, {
          'label' : App.lang('Closed Requests'),
          'value' : '1',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete')
        }]
      }]
    });
    
    // project_request added
    App.Wireframe.Events.bind('project_request_created.content', function (event, project_request) {
      wrapper.objectsList('add_item', project_request);
    });

    // project_request updated
    App.Wireframe.Events.bind('project_request_updated.content', function (event, project_request) {
      wrapper.objectsList('update_item', project_request);
    });

    // project_request deleted
    App.Wireframe.Events.bind('project_request_deleted.content', function (event, project_request) {
      wrapper.objectsList('delete_item', project_request['id']);
    });

    // quote added
    App.Wireframe.Events.bind('quote_created.content', function (event, quote) {
      var selected_requests = wrapper.objectsList('get_selection');
      var quote_based_on_id = typeof(quote) == 'object' && typeof(quote['based_on']) == 'object' ? quote['based_on']['id'] : null;

      if(quote_based_on_id && selected_requests.length == 1 && selected_requests[0] == quote_based_on_id) {
        App.Wireframe.Content.setFromUrl(quote['urls']['view']);
      } // if
    });

    // redirect to project after it's created from quote
    App.Wireframe.Events.bind('project_created.content', function (event, project) {
        App.Wireframe.Navigation.redirect(project.permalink);
    });


      // Comment added
    App.Wireframe.Events.bind('comment_created.content', function (event, comment) {
      App.Wireframe.Events.trigger('project_request_updated.content', [ comment.parent ]);
    });

  {if $active_project_request->isLoaded()}
    wrapper.objectsList('load_item', {$active_project_request->getId()|json nofilter}, {$active_project_request->getViewUrl()|json nofilter});
  {/if}
  });
</script>