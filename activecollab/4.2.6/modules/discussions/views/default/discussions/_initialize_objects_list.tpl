{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#new_discussion').flyoutForm({
    'success_event' : 'discussion_created',
    'title' 				: App.lang('New Discussion')
  });

  $('#discussions').each(function() {
    var wrapper = $(this);

    var project_id = {$active_project->getId()|json nofilter};
    var categories_map = {$categories|map nofilter};
    var milestones_map = {$milestones|map nofilter};
    var read_status_map = {$read_statuses|map nofilter};
    var current_discussion_id;

    var print_url = {$print_url|json nofilter};

    var init_options = {
      'id' : 'project_' + {$active_project->getId()} + '_discussions',
      'refresh_url' : '{assemble route=project_discussions project_slug=$active_project->getSlug() async=true objects_list_refresh=true}',
      'items' : {$discussions|json nofilter},
      'required_fields' : ['id', 'name', 'category_id', 'milestone_id', 'icon', 'is_read', 'is_pinned', 'permalink', 'is_archived'],
      'requirements' : {},
      'objects_type' : 'discussions',
      'events' : App.standardObjectsListEvents(),
      'multi_title' : App.lang(':num Discussions Selected'),
      'multi_url' : '{assemble route=project_discussions_mass_edit project_slug=$active_project->getSlug()}',
      'multi_actions' : {$mass_manager|json nofilter},
      'print_url' : print_url,
      'prepare_item' : function (item) {
        var result = {
          'id'            : item['id'],
          'name'          : item['name'],
          'icon'          : item['icon'],
          'is_read'       : item['is_read'],
          'is_pinned'     : item['is_pinned'],
          'permalink'     : item['permalink'],
          'is_favorite'   : item['is_favorite'],
          'is_archived'   : item['state'] == '2' ? '1' : '0',
          'is_trashed'    : item['state'] == '1' ? 1 : 0,
          'visibility'    : item['visibility']
        };

        if (typeof(item['category']) == 'undefined') {
          result['category_id'] = item['category_id'];
        } else {
          result['category_id'] = item['category'] ? item['category']['id'] : 0;
        } // if

        if(typeof(item['milestone']) == 'undefined') {
          result['milestone_id'] = item['milestone_id'];
        } else {
          result['milestone_id'] = item['milestone'] ? item['milestone']['id'] : 0;
        } // if

        return result;
      },

      'render_item' : function (item) {
        return '<td class="icon"><img src="' + item['icon'] + '" alt=""></td><td class="name" is_pinned="' + (item['is_pinned'] ? 1 : 0) + '">' + App.clean(item['name']) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</td><td class="discussion_options"></td>';
      },

      'grouping' : [{
        'label' : App.lang("Don't group"),
        'property' : '', icon : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Category'),
        'property' : 'category_id',
        'map' : categories_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories')
      }, {
        'label' : App.lang('By Milestone'),
        'property' : 'milestone_id',
        'map' : milestones_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-milestones.png', 'system'),
        'uncategorized_label' : App.lang('Unknown Milestone')
      },{
        'label' : App.lang('By Read Status'),
        'property' : 'is_read',
        'map' : read_status_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-status.png', 'environment'),
        'default' : true,
        'uncategorized_label' : App.lang('Unread')
      }]
    };

    if ({$in_archive|json nofilter}) {
      init_options.requirements.is_archived = 1;
    } else {
      init_options.requirements.is_archived = 0;
    } // if

    wrapper.objectsList(init_options);

    // discussion added
    App.Wireframe.Events.bind('discussion_created.content', function (event, discussion) {
      if (discussion['project_id'] == project_id) {
        wrapper.objectsList('add_item', discussion, true);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(discussion['urls']['view']);
        } // if
      } // if
    });

    // discussion updated
    App.Wireframe.Events.bind('discussion_updated.content', function (event, discussion) {
      if (discussion['project_id'] == project_id) {
        var existing_item = wrapper.objectsList('get_item', discussion['id']);
        if (existing_item && existing_item.length) {
          if (existing_item.find('td.name').attr('is_pinned') != discussion.is_pinned) {
            wrapper.objectsList('refresh');
            return true;
          } // if
        } //if

        wrapper.objectsList('update_item', discussion, true);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(discussion['urls']['view']);
        } else {
          wrapper.objectsList('delete_selected_item');
        } // if
      } // if
    });

    // Discussion deleted
    App.Wireframe.Events.bind('discussion_deleted.content', function (event, discussion) {
      if (discussion['project_id'] == project_id) {
        if (wrapper.objectsList('is_loaded', discussion['id'], false)) {
          wrapper.objectsList('load_empty');
        } // if
        wrapper.objectsList('delete_item', discussion['id']);
      } // if
    });

    // Manage milestones
    App.objects_list_keep_milestones_map_up_to_date(wrapper, 'milestone_id', project_id);

    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', {$active_discussion->category()->getCategoryContextString()|json nofilter}, {$active_discussion->category()->getCategoryClass()|json nofilter});

  {if $active_discussion->isLoaded()}
    wrapper.objectsList('load_item', {$active_discussion->getId()}, {$active_discussion->getViewUrl()|json nofilter}); // Pre select item if this is permalink
  {/if}
  });
</script>