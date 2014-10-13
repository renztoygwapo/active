{use_widget name="objects_list" module="environment"}

<script type="text/javascript">
  $('#documents').each(function() {
    var objects_list_wrapper = $(this);

    var items = {$documents|json nofilter};
    var categories_map = {$categories|map nofilter};
    var letters_map = {$letters|map nofilter};
    var print_url = '{assemble route=documents print=1}';
    var mass_edit_url = '{assemble route=documents_mass_edit}';

    var init_options = {
      'id'                 : 'global_documents',
      'items'              : items,
      'required_fields'    : ['id', 'name', 'first_letter', 'category_id', 'permalink'],
      'requirements'       : {},
      'objects_type'       : 'documents',
      'print_url'          : print_url,
      'events'             : App.standardObjectsListEvents(),
      'multi_title'        : App.lang(':num Documents Selected'),
      'multi_url'          : mass_edit_url,
      'multi_actions' : {$mass_manager|json nofilter},
      'prepare_item'       : function (item) {
        var result = {
          'id' : item['id'],
          'name' : item['name'],
          'first_letter' : item['first_letter'],
          'permalink' : item['permalink'],
          'is_archived' : item['is_archived'],
          'is_pinned' : item['is_pinned'],
          'is_favorite' : item['is_favorite'],
          'is_trashed' : item['state'] == '1' ? 1 : 0,
          'visibility'    : item['visibility']
        };

        if(typeof(item['category']) == 'undefined') {
          result['category_id'] = item['category_id'];
        } else {
          result['category_id'] = item['category'] ? item['category']['id'] : 0;
        } // if

        return result;
      },
      'render_item'        : function(item) {
        return '<td class="name">' + App.clean(item.name) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</td><td class="task_options"></td>';
      },
      'grouping' : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Category'),
        'property' : 'category_id' ,
        'map' : categories_map,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories')
      }, {
        'label' : App.lang('By Name'),
        'property' : 'first_letter',
        'map' : letters_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-name.png', 'environment'),
        'uncategorized_label' : App.lang('*'), 'default' : true
      }],
      'filtering' : []
    };

    if (!{$in_archive|json nofilter}) {
      init_options.requirements.is_archived = 0;
    } else {
      init_options.requirements.is_archived = 1;
    } // if

    objects_list_wrapper.objectsList(init_options);

    // document added
    if (!{$in_archive|json nofilter}) {
      App.Wireframe.Events.bind('document_created.content', function (event, document) {
        objects_list_wrapper.objectsList('add_item', document);
      });
    } // if

    // document updated
    App.Wireframe.Events.bind('document_updated.content', function (event, document) {
      objects_list_wrapper.objectsList('update_item', document);

      App.Wireframe.PageTitle.set(App.clean(document.name));
      $('.object_body .object_body_content').html(document.body);
      App.Wireframe.Flash.success(App.lang('Document has been updated'));
    });

    // document deleted
    App.Wireframe.Events.bind('document_deleted.content', function (event, document) {
      if (objects_list_wrapper.objectsList('is_loaded', document['id'], false)) {
        objects_list_wrapper.objectsList('load_empty');
      } // if
      objects_list_wrapper.objectsList('delete_item', document['id']);
    });

    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(objects_list_wrapper, 'category_id', {$active_document->category()->getCategoryContextString()|json nofilter}, {$active_document->category()->getCategoryClass()|json nofilter});

    $('#documents_new_text_document').flyoutForm({
      'title' : App.lang('New Text Document'),
      'success_event' : 'document_created',
      'success_message' : App.lang('Text document has been created')
    });

    $('#documents_upload_document').flyoutForm({
      'title' : App.lang('Upload File'),
      'success_event' : 'document_created',
      'success_message' : App.lang('File has been uploaded')
    });

  {if $active_document->isLoaded()}
    objects_list_wrapper.objectsList('load_item', {$active_document->getId()}, {$active_document->getViewUrl()|json nofilter}); // Pre select item if this is permalink
  {/if}
  });
</script>