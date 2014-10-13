{title}Files{/title}
{add_bread_crumb}Files{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="milestone_files">
  <div id="milestone_files_list">
  </div>

  <div id="add_new_file_to_milestone">
    <a href="{$add_file_url}" title="{lang}New File{/lang}" class="add_new_item">{lang}Add New File{/lang}</a>
  </div>
</div>

<script type="text/javascript">
$('#milestone_files').each(function() {
  var wrapper = $(this);

  var inline_tabs = wrapper.parents('.inline_tabs:first');
  var scope = inline_tabs.attr('event_scope') ? inline_tabs.attr('event_scope') : '.inline_tab';

  var milestone_id = {$milestone_id|json nofilter};
  var total_items = {$total_items|json nofilter};
  var milestone_state = {$active_milestone->getState()|json nofilter};
  var milestone_inspector = wrapper.parents('div.object_wrapper:first').find('.object_inspector:first');

  var add_file = $('#add_new_file_to_milestone a');
  var add_file_url = {$add_file_url|json nofilter};
  var original_state = {$active_milestone->getState()|json nofilter};

  add_file.flyoutFileForm({
    'success_event' : 'multiple_assets_created'
  });

  App.Wireframe.Events.bind('asset_created' + scope, function(event, data, batch) {
    if (!batch) {
      if (data.milestone_id == milestone_id) {
        milestone_inspector.objectInspector('refresh');
      } // if

      handle_add_link(original_state);
    } // if
  });

  App.Wireframe.Events.bind('multiple_assets_created' + scope, function (event, data) {
    $.each(data, function (index, asset) {
      App.Wireframe.Events.trigger('asset_created', [asset, true]);
    });

    if (data[0].milestone_id == milestone_id) {
      milestone_inspector.objectInspector('refresh');
    } // if

    handle_add_link(original_state);
  });

  App.Wireframe.Events.bind('asset_updated' + scope, function(event, data) {

    var current_item = wrapper.find('tr[list_item_id=' + data.id + ']');

    // if file was in list, and now it's not more, we have to remove it
    if (current_item.length) {
      if (data.milestone_id != milestone_id) {
        current_item.remove();
        milestone_inspector.objectInspector('refresh');
        if (wrapper.find('tr.list_item').length == 0) {
          wrapper.find('#milestone_files_list table').hide();
          wrapper.find('#milestone_files_list p.empty_page').show();
        } // if
      } // if
    } else {
      if (data.milestone_id == milestone_id) {
        App.Wireframe.Events.trigger('asset_created', [data]);
        return true;
      } // if
    } // if

    handle_add_link(original_state);
  });

  App.Wireframe.Events.bind('asset_deleted' + scope, function(event, data) {
    if ($('#milestone_files_list').find('table tbody tr').length == 1) {
      $('#add_new_file_to_milestone').hide();
    } // if

    if (data.milestone_id == milestone_id) {
      milestone_inspector.objectInspector('refresh');
    } // if

    handle_add_link(original_state);
  });

  App.Wireframe.Events.bind('milestone_updated' + scope + ', milestone_deleted' + scope, function (event, milestone) {
    handle_add_link(milestone.state);
  });

  /**
   * Handles how add links behave
   *
   * @param state
   */
  var handle_add_link = function(state) {
    setTimeout(function () {
      original_state = state;

      var has_items = wrapper.find('tr.list_item').length;
      var add_another = wrapper.find('p.add_another');

      if (state < 3 || !add_file_url) {
        add_file.hide();
        add_another.hide();
      } else {
        if (has_items) {
          add_file.show();
          add_another.show();
        } else {
          add_file.hide();
          add_another.show();
        } // if
      } // if
    }, 100);
  }; // handle_add_link

  var on_add_item_function = function(asset, row) {
    $('#add_new_file_to_milestone').show();

    row.attr('id',asset['id']);
    row.append(
            '<td class="favorite"></td>' +
                    '<td class="details"></td>' +
                    '<td class="options"></td>'
    );

    row.find('td.favorite').append($('<a href="#"></a>').asyncToggler({
      'is_on' : asset['is_favorite'],
      'content_when_on' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-on.png', 'favorites') + "'></img>",
      'content_when_off' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-off.png', 'favorites') + "'></img>",
      'title_when_on' : App.lang('Remove from Favorites'),
      'title_when_off' : App.lang('Add to Favorites'),
      'url_when_on' : asset['urls']['remove_from_favorites'],
      'url_when_off' : asset['urls']['add_to_favorites'],
      'success_event' : 'asset_updated'
    }));

    row.find('td.details').append('<a class="file_url" href="' + asset['urls']['view'] + '">' + asset['name'] + '</a>')
            .append('<br />' + App.lang('Created by') + ' ')
            .append(App.Wireframe.Utils.userLink(asset['created_by']))
            .append(' ' + App.Wireframe.Utils.ago(asset['created_on']));

    if (asset['is_completed']) {
      row.find('td.details a.file_url').wrap("<del></del>");
    } //if

    if (asset['permissions']['can_edit'] && asset['permissions']['can_trash']) {
      row.find('td.options')
              .append('<a href="' + asset['urls']['edit'] + '" class="edit_file" title="' + App.lang('Edit File') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
              .append('<a href="' + asset['urls']['trash'] + '" class="trash_file" title="' + App.lang('Move to Trash') + '"><img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" /></a>')
      ;
    } //if

    row.find('td.options a.edit_file').flyoutForm({
      'success_event' : 'asset_updated'
    });

    row.find('td.options a.trash_file').asyncLink({
      'confirmation' : App.lang('Are you sure that you want to move this file to trash?'),
      'success_event' : 'asset_deleted',
      'success_message' : App.lang('Selected file has been moved to trash')
    });
  };

  wrapper.find('#milestone_files_list').pagedObjectsList({
    'init'              : function () {
      handle_add_link(original_state);
    },
    'load_more_url' : {$more_results_url|json nofilter},
    'items' : {$files|json nofilter},
    'items_per_load' : {$items_per_page},
    'total_items' : total_items,
    'list_items_are' : 'tr',
    'columns' : {
      'favorite' : '',
      'details' : App.lang('File Details'),
      'options' : ''
    },
    'empty_message' : function () {
      var empty_string = $('<p>' + App.lang('There are no files in this milestone') + '</p>');
      var add_url = {$add_file_url|json nofilter};

      if (typeof(add_url) == 'string' && add_url) {
        var create_paragraph = $('<p class="add_another">' + App.lang('Would you like to <a href=":add_url" title="New File">create one now</a>?', {
          'add_url' : add_url
        }) + '</p>').appendTo(empty_string);

        create_paragraph.find('a').flyoutFileForm({
          'success_event' : 'multiple_assets_created'
        });
      } else {
        return empty_string;
      } // if

      return empty_string;
    },
    'listen' : 'asset',
    'listen_constraint' : function(event, item) {
      if ($.isArray(item)) {
        var $return = true;
        $.each(item, function (index, item_instance) {
          $return = ( typeof(item_instance) == 'object' && item_instance && item_instance['milestone_id'] == milestone_id);
          if ($return === false) {
            return false;
          } //if
        });//each

        return $return;
      } else {
        return typeof(item) == 'object' && item && item['milestone_id'] == milestone_id;
      }
    },
    'listen_scope' : scope.substring(1),
    'on_add_item' : function(asset) {
      var row = $(this);
      if ($.isArray(asset)) {
        $.each(asset, function (index, item_instance) {
          if (index > 0) {
            return false; //to consider
            row.after($('tr'));
            row = row.next();
            row.addClass('list_item');
          } //if
          on_add_item_function(item_instance, row);
        });//each
      } else {
        on_add_item_function(asset, row);
      } //if
    } //on_add_item
  });
});
</script>