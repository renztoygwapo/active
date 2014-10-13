{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#assets_new_text_document').flyoutForm({
    'title' : App.lang('Text Document'),
    'success_event' : 'asset_created'
  });

  $('#assets_new_upload_files').flyoutFileForm({
    'title' : App.lang('Upload Files'),
    'success_event' : 'multiple_assets_created'
  });

  $('#assets_new_youtube').flyoutForm({
    'title' : App.lang('YouTube Video'),
    'success_event' : 'asset_created'
  });

  $('#assets').each(function() {
    var wrapper = $(this);

    var items = {$assets|json nofilter};
    var project_id = {$active_project->getId()|json nofilter};
    var categories_map = {$categories|map nofilter};
    var milestones_map = {$milestones|map nofilter};
    var letters_map = {$letters|map nofilter};
    var created_on_dates_map = {$created_on_dates|map nofilter};
    var updated_on_dates_map = {$updated_on_dates|map nofilter};
    var type_map = {$types|map nofilter};
    var types_detailed = {$types_detailed|json nofilter};

    var print_url = {$print_url|json nofilter};

    var type_filter = [];
    type_filter.push({ label : App.lang('All types'), value : '', 'default' : true, icon : App.Wireframe.Utils.imageUrl('objects-list/all-assets.png', 'files')});
    App.each(type_map, function (type, title) {
      type_filter.push({
        'label' : title,
        'value' : type,
        'icon' : types_detailed[type].icon
      });
    });

    var init_options = {
      'id' : 'project_' + {$active_project->getId()} + '_assets',
      'items' : items,
      'required_fields' : ['id', 'name', 'category_id', 'milestone_id', 'first_letter', 'created_on_date', 'updated_on_date', 'type', 'preview', 'permalink', 'is_archived'],
      'requirements' : {},
      'objects_type' : 'assets',
      'events' : App.standardObjectsListEvents(),
      'multi_title' : App.lang(':num Assets Selected'),
      'multi_url' : '{assemble route=project_assets_mass_edit project_slug=$active_project->getSlug()}',
      'multi_actions' : {$mass_manager|json nofilter},
      'print_url' : print_url,
      'prepare_item' : function (item) {
        var result = {
          'id'              : item['id'],
          'name'            : item['name'],
          'first_letter'    : item['first_letter'],
          'created_on_date' : item['created_on_date'],
          'updated_on_date' : item['updated_on_date'],
          'type'            : item['type'],
          'icon'            : item['preview']['icons']['small'],
          'is_archived'     : item['state'] == 2 ? 1 : 0,
          'permalink'       : item['permalink'],
          'is_favorite'     : item['is_favorite'],
          'is_trashed'      : item['state'] == '1' ? 1 : 0,
          'visibility'      : item['visibility']
        };

        if(typeof(item['category']) == 'undefined') {
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
        return '<td class="icon small"><img src="' + App.clean(item['icon']) + '" alt=""></td><td class="name">' + App.clean(item.name) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</td><td class="asset_options"></td>';
      },

      'grouping' : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Category'),
        'property' : 'category_id' ,
        'map' : categories_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories'),
        'default' : true,
        'uncategorized_label' : App.lang('Uncategorized')
      }, {
        'label' : App.lang('By Milestone'),
        'property' : 'milestone_id',
        'map' : milestones_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-milestones.png', 'system'),
        'uncategorized_label' : App.lang('Unknown Milestone')
      }, {
        'label' : App.lang('By Name'),
        'property' : 'first_letter',
        'map' : letters_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-name.png', 'environment'),
        'uncategorized_label' : App.lang('*')
      }, {
        'label' : App.lang('By Date Created'),
        'property' : 'created_on_date',
        'map' : created_on_dates_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/date-created.png', 'files'),
        'uncategorized_label' : App.lang('Unknown Date of Creation')
      }, {
        'label' : App.lang('By Date Modified'),
        'property' : 'updated_on_date',
        'map' : updated_on_dates_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/date-modified.png', 'files'),
        'uncategorized_label' : App.lang('Not Modified')
      }],

      'filtering' : [{
        'label' : App.lang('Type'),
        'property'  : 'type',
        'values'  : type_filter
      }]
    };

    if ({$in_archive|json nofilter}) {
      init_options.requirements.is_archived = 1;
    } else {
      init_options.requirements.is_archived = 0;
    } // if

    wrapper.objectsList(init_options);

    // handle multiple assets added
    App.Wireframe.Events.bind('multiple_assets_created.content', function (event, data) {
      var counter = 0;
      var last_asset = null;

      $.each(data, function (key, asset) {
        App.Wireframe.Events.trigger('asset_created', [asset, true]);
        last_asset = asset;
      });

      if (last_asset) {
        wrapper.objectsList('load_item', last_asset.id, last_asset.permalink);
      } // if
    });

    // Asset added
    App.Wireframe.Events.bind('asset_created.content', function (event, asset, skip_loading) {
      if (asset.project_id == project_id) {
        wrapper.objectsList('add_item', asset, true, skip_loading);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(asset['urls']['view']);
        } // if
      } // if
    });

    // Asset updated
    App.Wireframe.Events.bind('asset_updated.content', function (event, asset) {
      if (asset['project_id'] == project_id) {
        wrapper.objectsList('update_item', asset);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(asset['urls']['view']);
        } else {
          wrapper.objectsList('delete_selected_item');
        } // if
      } // if
    });

    // Asset deleted
    App.Wireframe.Events.bind('asset_deleted.content', function (event, asset) {
      if (asset['project_id'] == project_id) {
        if (wrapper.objectsList('is_loaded', asset['id'], false)) {
          wrapper.objectsList('load_empty');
        } // if
        wrapper.objectsList('delete_item', asset['id']);
      } // if
    });

    // Manage maps
    App.objects_list_keep_milestones_map_up_to_date(wrapper, 'milestone_id', project_id);

    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', {$active_asset->category()->getCategoryContextString()|json nofilter}, {$active_asset->category()->getCategoryClass()|json nofilter});

  {if $active_text_document_version && $active_text_document_version->isLoaded()}
    wrapper.objectsList('load_item', null, {$active_text_document_version->getViewUrl()|json nofilter});
    {else if $active_asset->isLoaded()}
    wrapper.objectsList('load_item', {$active_asset->getId()}, {$active_asset->getViewUrl()|json nofilter});
  {/if}
  });
</script>