{title}Notebooks{/title}
{add_bread_crumb}Notebooks{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="milestone_notebooks">
  <div id="milestone_notebooks_list">
  </div>

  <div id="add_new_notebook_to_milestone">
    <a href="{$add_notebook_url}" title="{lang}New Notebook{/lang}" class="add_new_item">{lang}Add New Notebook{/lang}</a>
  </div>
</div>

<script type="text/javascript">
  $('#milestone_notebooks').each(function() {
    var wrapper = $(this);

    var inline_tabs = wrapper.parents('.inline_tabs:first');
    var scope = inline_tabs.attr('event_scope') ? inline_tabs.attr('event_scope') : '.inline_tab';

    var milestone_id = {$milestone_id|json nofilter};
    var total_items = {$total_items|json nofilter};
    var milestone_state = {$active_milestone->getState()|json nofilter};
    var milestone_inspector = wrapper.parents('div.object_wrapper:first').find('.object_inspector:first');

    var add_notebook = $('#add_new_notebook_to_milestone a');
    var add_notebook_url = {$add_notebook_url|json nofilter};
    var original_state = {$active_milestone->getState()|json nofilter};

    add_notebook.flyoutForm({
      'success_event' : 'notebook_created'
    });

    App.Wireframe.Events.bind('notebook_created' + scope, function(event, data) {
      if (data.milestone_id == milestone_id) {
        milestone_inspector.objectInspector('refresh');
      } // if

      handle_add_link(original_state);
    });

    App.Wireframe.Events.bind('notebook_updated' + scope, function(event, data) {
      var current_item = wrapper.find('tr[list_item_id=' + data.id + ']');

      // if notebook was in list, and now it's not more, we have to remove it
      if (current_item.length) {
        if (data.milestone_id != milestone_id) {
          current_item.remove();
          milestone_inspector.objectInspector('refresh');

          if (wrapper.find('tr.list_item').length == 0) {
            wrapper.find('#milestone_notebooks_list table').hide();
            wrapper.find('#milestone_notebooks_list p.empty_page').show();
          } // if
        } // if
      } else {
        if (data.milestone_id == milestone_id) {
          App.Wireframe.Events.trigger('notebook_created', [data]);
          return true;
        } // if
      } // if

      handle_add_link(original_state);
    });

    App.Wireframe.Events.bind('notebook_deleted' + scope, function(event, data) {
      if ($('#milestone_notebooks_list').find('table tbody tr').length == 1) {
        $('#add_new_notebook_to_milestone').hide();
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

        if (state < 3 || !add_notebook_url) {
          add_notebook.hide();
          add_another.hide();
        } else {
          if (has_items) {
            add_notebook.show();
            add_another.show();
          } else {
            add_notebook.hide();
            add_another.show();
          } // if
        } // if
      }, 100);
    }; // handle_add_link

    wrapper.find('#milestone_notebooks_list').pagedObjectsList({
      'init'              : function () {
        handle_add_link(original_state);
      },
      'load_more_url' : {$more_results_url|json nofilter},
      'items' : {$notebooks|json nofilter},
      'items_per_load' : {$items_per_page},
      'total_items' : total_items,
      'list_items_are' : 'tr',
      'columns' : {
        'favorite' : '',
        'details' : App.lang('Notebook Details'),
        'options' : ''
      },
      'empty_message' : function () {
        var empty_string = $('<p>' + App.lang('There are no notebooks in this milestone') + '</p>');
        var add_url = {$add_notebook_url|json nofilter};

        if (typeof(add_url) == 'string' && add_url) {
          var create_paragraph = $('<p class="add_another">' + App.lang('Would you like to <a href=":add_url" title="New Notebook">create one now</a>?', {
            'add_url' : add_url
          }) + '</p>').appendTo(empty_string);

          create_paragraph.find('a').flyoutForm({
            'success_event' : 'notebook_created'
          });
        } else {
          return empty_string;
        } // if

        return empty_string;
      },
      'listen' : 'notebook',
      'listen_constraint' : function(event, item) {
        return typeof(item) == 'object' && item && item['milestone_id'] == milestone_id;
      },
      'listen_scope' : scope.substring(1),
      'on_add_item' : function(notebook) {
        $('#add_new_notebook_to_milestone').show();
        var row = $(this);

        row.append(
          '<td class="favorite"></td>' +
          '<td class="details"></td>' +
          '<td class="options"></td>'
        );

        row.attr('id',notebook['id']);

        row.find('td.favorite').append($('<a href="#"></a>').asyncToggler({
          'is_on' : notebook['is_favorite'],
          'content_when_on' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-on.png', 'favorites') + "'></img>",
          'content_when_off' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-off.png', 'favorites') + "'></img>",
          'title_when_on' : App.lang('Remove from Favorites'),
          'title_when_off' : App.lang('Add to Favorites'),
          'url_when_on' : notebook['urls']['remove_from_favorites'],
          'url_when_off' : notebook['urls']['add_to_favorites'],
          'success_event' : 'notebook_updated'
        }));

        row.find('td.details').append('<a class="notebook_url" href="' + notebook['urls']['view'] + '">' + notebook['name'] + '</a>')
          .append('<br />' + App.lang('Created by') + ' ')
          .append(App.Wireframe.Utils.userLink(notebook['created_by']))
          .append(' ' + App.Wireframe.Utils.ago(notebook['created_on']));

        if (notebook['is_completed']) {
          row.find('td.details a.notebook_url').wrap("<del></del>");
        } //if

        if (notebook['permissions']['can_edit'] && notebook['permissions']['can_trash']) {
          row.find('td.options')
            .append('<a href="' + notebook['urls']['edit'] + '" class="edit_notebook" title="' + App.lang('Edit Notebook') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
            .append('<a href="' + notebook['urls']['trash'] + '" class="trash_notebook" title="' + App.lang('Move to Trash') + '"><img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" /></a>')
          ;
        } //if

        row.find('td.options a.edit_notebook').flyoutForm({
          'success_event' : 'notebook_updated'
        });

        row.find('td.options a.trash_notebook').asyncLink({
          'confirmation' : App.lang('Are you sure that you want to move this notebook to trash?'),
          'success_event' : 'notebook_deleted',
          'success_message' : App.lang('Selected notebook has been moved to trash')
        });
      }
    });
  });
</script>