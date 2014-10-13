{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#new_project').flyoutForm({
    'title' : App.lang('New Project'),
    'success_event' : 'project_created'
  });

  $('#projects').each(function() {
    var wrapper = $(this);

    var items = {$projects|json nofilter};
    var owner_company_id = {$owner_company->getId()|json nofilter};
    var categories_map = {$categories|map nofilter};
    var companies_map = {$companies|map nofilter};

    var labels = {$labels|json nofilter};
    var labels_map = new App.Map();

    if(typeof(labels) == 'object' && labels) {
      App.each(labels, function(k, label) {
        labels_map.set(label['id'], label['name']);
      });
    } // if

    var print_url = {$print_url|json nofilter};

    var grouping = [{
      'label' : App.lang("Don't group"),
      'property' : '',
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
    }, {
      'label' : App.lang('By Category'),
      'property' : 'category_id',
      'map' : categories_map,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories'),
      'default' : true
    }, {
      'label' : App.lang('By Client'),
      'property' : 'company_id',
      'map' : companies_map ,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
      'uncategorized_label' : App.lang('Internal')
    }, {
      'label' : App.lang('By Label'),
      'property' : 'label_id',
      'map' : labels_map,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-label.png', 'labels'),
      'uncategorized_label' : App.lang('No Label')
    }];

    {custom_fields_prepare_objects_list grouping_variable=grouping type='Project' sample=$active_project}

    var init_options = {
      'id' : 'projects',
      'items' : items,
      'required_fields' : ['id', 'name', 'is_completed', 'category_id', 'label_id', 'company_id', 'icon', 'permalink'],
      'requirements' : {},
      'objects_type' : 'projects',
      'events' : App.standardObjectsListEvents(),
      'show_goto_arrow' : true,
      'multi_title' : App.lang(':num Projects Selected'),
      'multi_url' : '{assemble route=projects_mass_edit}',
      'multi_actions' : {$mass_manager|json nofilter},
      'print_url' : print_url,
      'prepare_item' : function (item) {
        return {
          'id'                : item['id'],
          'name'              : item['name'],
          'is_completed'      : item['completed_on'] === null ? 0 : 1,
          'category_id'       : item['category'] ? item['category']['id'] : (item['category_id'] ? item['category_id'] : 0),
          'label_id'          : item['label_id'],
          'company_id'        : item['company'] ? item['company']['id'] : null,
          'company_name'      : item['company'] ? item['company']['name'] : null,
          'company_permalink' : item['company'] ? item['company']['permalink'] : null,
          'icon'              : item['avatar']['small'],
          'goto_url'          : item['permalink'],
          'permalink'         : App.extendUrl(item['permalink'], { 'brief' : 1 }),
          'is_favorite'       : item['is_favorite'],
          'total_assignments' : item['total_assignments'],
          'open_assignments'  : item['open_assignments'],
          'is_archived'       : item['state'] == '2' ? 1 : 0,
          'label'             : item['label']
        };
      },

      'render_item' : function (item) {
        var row = '<td class="icon"><img src="' + item.icon + '" alt="icon"></td><td class="name">' + App.clean(item.name) + '</td><td class="project_options">';

        row += App.Wireframe.Utils.renderLabelTag(item.label);

        // Completed task
        if(item['is_completed']) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-mono-100.png', 'complete') + '">';

          // Open project
        } else {

          var total_assignments = typeof(item['total_assignments']) != 'undefined' && item['total_assignments'] ? item['total_assignments'] : 0;
          var open_assignments = typeof(item['open_assignments']) != 'undefined' && item['open_assignments'] ? item['open_assignments'] : 0;

          var completed_assignments  = total_assignments - open_assignments;

          var color_class = 'mono';

          if(total_assignments > 0 && completed_assignments > 0) {
            if(completed_assignments >= total_assignments) {
              row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-100.png', 'complete') + '">';
            } else {
              var percentage = Math.ceil((completed_assignments / total_assignments) * 100);

              if(percentage <= 10) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
              } else if(percentage <= 20) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-10.png', 'complete') + '">';
              } else if(percentage <= 30) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-20.png', 'complete') + '">';
              } else if(percentage <= 40) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-30.png', 'complete') + '">';
              } else if(percentage <= 50) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-40.png', 'complete') + '">';
              } else if(percentage <= 60) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-50.png', 'complete') + '">';
              } else if(percentage <= 70) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-60.png', 'complete') + '">';
              } else if(percentage <= 80) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-70.png', 'complete') + '">';
              } else if(percentage <= 90) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-80.png', 'complete') + '">';
              } else {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-90.png', 'complete') + '">';
              } // if
            } // if
          } else {
            row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
          } // if
        } // if

        row += '</td>';

        return row;
      },

      'search_index' : function (item) {
        return App.clean(item.name);
      },

      'grouping' : grouping,
      'filtering' : []
    }


    if (!{$in_archive|json nofilter}) {
      init_options.filtering.push({
        'label' : App.lang('Status'),
        'property' : 'is_completed',
        'values'  : [{
          'label' : App.lang('Active and Completed'),
          'value' : '',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete'),
          'default' : true,
          'breadcrumbs' : App.lang('Active and Completed')
        }, {
          'label' : App.lang('Active Only'),
          'value' : 0,
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'),
          'breadcrumbs' : App.lang('Active')
        }, {
          'label' : App.lang('Completed Only'),
          'value' : 1,
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'),
          'breadcrumbs' : App.lang('Completed')
        }]
      });

      init_options.requirements.is_archived = 0;
    } else {
      init_options.requirements.is_archived = 1;
    } // if

    wrapper.objectsList(init_options);

    // project added
    App.Wireframe.Events.bind('project_created.content', function (event, project) {
      wrapper.objectsList('add_item', project);
    });

    // project updated
    App.Wireframe.Events.bind('project_updated.content', function (event, project) {
      wrapper.objectsList('update_item', project);
    });

    // Project deleted
    App.Wireframe.Events.bind('project_deleted.content', function (event, project) {
      wrapper.objectsList('delete_item', project['id']);
    });

    // Keep company_id map up to date
    App.objects_list_keep_companies_map_up_to_date(wrapper, 'company_id', 'content');
    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', {$active_project->category()->getCategoryContextString()|json nofilter}, {$active_project->category()->getCategoryClass()|json nofilter});

    {if $active_project && $active_project->isLoaded()}
      wrapper.objectsList('load_item', {$active_project->getId()|json}, '{$active_project->getViewUrl()}');
    {/if}
  });
</script>