{title}Data Sources{/title}
{add_bread_crumb}List{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="sources_admin" class="wireframe_content_wrapper settings_panel">

  <div class="settings_panel_body"><div id="data_sources"></div></div>
</div>

<script type="text/javascript">

  $('#data_sources').pagedObjectsList({
    'load_more_url' : '{assemble route=data_sources}',
    'items' : {$data_sources|json nofilter},
    'items_per_load' : {$data_sources_per_page},
    'total_items' : {$total_sources},
    'list_items_are' : 'tr',
    'list_item_attributes' : { 'class' : 'data_sources' },
    'columns' : {
      'name' : App.lang('Name'),
      'type' : App.lang('Source'),
      'import' : App.lang('Import'),
      'options' : App.lang('Options')
    },
    'sort_by' : 'name',
    'empty_message' : App.lang('There are no data sources defined'),
    'listen' : 'data_source',
    'on_add_item' : function(item) {
      var source = $(this);

      source.append(
        '<td class="name"></td>' +
        '<td class="type"></td>' +
        '<td class="import"></td>' +
        '<td class="options"></td>'
      );

      source.find('td.name').text(item['name']);
      source.find('td.type').text(item['type']);

      source.find('td.import')
        .append('<a href="' + item['urls']['import'] + '" class="import_from_source" title="' + App.lang('Import from this source') + '"><img src="{image_url name="status-bar/importer.png" module=$smarty.const.DATA_SOURCES_FRAMEWORK}" /></a>');

      source.find('td.import a.import_from_source').flyoutForm({
        width : 600,
        title : App.lang('Import data from :name', {
            'name' : item['name']
          }
        ),
        success_event : 'data_imported'

      });

      source.find('td.options')
        .append('<a href="' + item['urls']['view'] + '" class="source_details" title="' + App.lang('View Details') + '"><img src="{image_url name="icons/12x12/preview.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_source" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');

      source.find('td.options a.source_details').flyout({
        'width' : 350
      });

      source.find('td.options a.edit_source').flyoutForm({
        'success_event' : 'data_source_updated',
        'width' : 1050,
        'title' : App.lang('Edit Data Source')
      });


      source.find('td.options')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_source" title="' + App.lang('Remove source') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');

      source.find('td.options a.delete_source').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this data source?'),
        'success_event' : 'data_source_deleted',
        'success_message' : App.lang('Data Source has been deleted successfully')
      });

    }
  });
</script>
