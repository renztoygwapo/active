{title}Discussions{/title}
{add_bread_crumb}Discussions{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="milestone_discussions">
  <div id="milestone_discussions_list"></div>

  <div id="add_new_discussion_to_milestone">
    <a href="{$add_discussion_url}" title="{lang}New Task{/lang}" class="add_new_item">{lang}Add New Discussion{/lang}</a>
  </div>
</div>

<script type="text/javascript">
  $('#milestone_discussions').each(function() {
    var wrapper = $(this);

    var inline_tabs = wrapper.parents('.inline_tabs:first');
    var scope = inline_tabs.attr('event_scope') ? inline_tabs.attr('event_scope') : '.inline_tab';

    var milestone_id = {$milestone_id|json nofilter};
    var total_items = {$total_items|json nofilter};
    var milestone_state = {$active_milestone->getState()|json nofilter}
    var milestone_inspector = wrapper.parents('div.object_wrapper:first').find('.object_inspector:first');

    var add_discussion = wrapper.find('#add_new_discussion_to_milestone a');
    var add_discussion_url = {$add_discussion_url|json nofilter};
    var original_state = {$active_milestone->getState()|json nofilter};

    add_discussion.flyoutForm({
      'success_event' : 'discussion_created'
    });

    App.Wireframe.Events.bind('discussion_created' + scope, function(event, data) {
      if (data.milestone_id == milestone_id) {
        milestone_inspector.objectInspector('refresh');
      } // if

      handle_add_link(original_state);
    });

    App.Wireframe.Events.bind('discussion_updated' + scope, function(event, data) {
      var current_item = wrapper.find('tr[list_item_id=' + data.id + ']');

      if (current_item.length) {
        if (data.milestone_id != milestone_id) {
          current_item.remove();
          milestone_inspector.objectInspector('refresh');

          if (wrapper.find('tr.list_item').length == 0) {
            wrapper.find('#milestone_discussions_list table').hide();
            wrapper.find('#milestone_discussions_list p.empty_page').show();
          } // if
        } // if
      } else {
        if (data.milestone_id == milestone_id) {
          App.Wireframe.Events.trigger('discussion_created', [data]);
          return true;
        } // if
      } // if

      handle_add_link(original_state);
    });

    App.Wireframe.Events.bind('discussion_deleted' + scope, function(event, data) {
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

        if (state < 3 || !add_discussion_url) {
          add_discussion.hide();
          add_another.hide();
        } else {
          if (has_items) {
            add_discussion.show();
            add_another.show();
          } else {
            add_discussion.hide();
            add_another.show();
          } // if
        } // if
      }, 100);
    }; // handle_add_link

    wrapper.find('#milestone_discussions_list').pagedObjectsList({
      'init'              : function () {
        handle_add_link(original_state);
      },
      'load_more_url' : {$more_results_url|json nofilter},
      'items' : {$discussions|json nofilter},
      'items_per_load' : {$items_per_page},
      'total_items' : total_items,
      'list_items_are' : 'tr',
      'columns' : {
        'favorite' : '',
        'details' : App.lang('Discussion Details'),
        'options' : ''
      },
      'empty_message' : function () {
        var empty_string = $('<p>' + App.lang('There are no discussions in this milestone') + '</p>');
        var add_url = {$add_discussion_url|json nofilter};

        if (typeof(add_url) == 'string' && add_url) {
          var create_paragraph = $('<p class="add_another">' + App.lang('Would you like to <a href=":add_url" title="New Discussion">create one now</a>?', {
            'add_url' : add_url
          }) + '</p>').appendTo(empty_string);

          create_paragraph.find('a').flyoutForm({
            'success_event' : 'discussion_created'
          });
        } // if

        return empty_string;
      },
      'listen' : 'discussion',
      'listen_constraint' : function(event, item) {
        return typeof(item) == 'object' && item && item['milestone_id'] == milestone_id;
      },
      'listen_scope' : scope.substring(1),
      'on_add_item' : function(discussion) {
        $('#add_new_discussion_to_milestone').show();
        var row = $(this);

        row.append(
          '<td class="favorite"></td>' +
          '<td class="details"></td>' +
          '<td class="options"></td>'
        );
        row.attr('id',discussion['id']);
        row.find('td.favorite').append($('<a href="#"></a>').asyncToggler({
        'is_on' : discussion['is_favorite'],
          'content_when_on' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-on.png', 'favorites') + "'></img>",
          'content_when_off' : "<img src='" + App.Wireframe.Utils.imageUrl('heart-off.png', 'favorites') + "'></img>",
          'title_when_on' : App.lang('Remove from Favorites'),
          'title_when_off' : App.lang('Add to Favorites'),
          'url_when_on' : discussion['urls']['remove_from_favorites'],
          'url_when_off' : discussion['urls']['add_to_favorites'],
          'success_event' : 'discussion_updated'
        }));

        row.find('td.details').append(App.Wireframe.Utils.renderLabel(discussion['label']) + " ")
          .append('<a class="discussion_url quick_view_item" href="' + discussion['urls']['view'] + '">' + discussion['name'] + '</a>')
          .append('<br />' + App.lang('Posted by') + ' ')
          .append(App.Wireframe.Utils.userLink(discussion['created_by']))
          .append(' ' + App.Wireframe.Utils.ago(discussion['created_on']));

        if (discussion['last_commented_on']) {
          row.find('td.details').append('<br />' + App.lang('Last commented') + ' ')
          .append(App.Wireframe.Utils.ago(discussion['last_commented_on']));
        } //if

        if (discussion['permissions']['can_edit'] && discussion['permissions']['can_trash']) {
          row.find('td.options')
            .append('<a href="' + discussion['urls']['edit'] + '" class="edit_discussion" title="' + App.lang('Edit Discussion') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
            .append('<a href="' + discussion['urls']['trash'] + '" class="trash_discussion" title="' + App.lang('Move to Trash') + '"><img src="{image_url name="icons/12x12/move-to-trash.png" module=$smarty.const.SYSTEM_MODULE}" /></a>')
          ;
        } //if

        row.find('td.options a.edit_discussion').flyoutForm({
          'success_event' : 'discussion_updated'
        });

        row.find('td.options a.trash_discussion').asyncLink({
          'confirmation' : App.lang('Are you sure that you want to move this discussion to trash?'),
          'success_event' : 'discussion_deleted',
          'success_message' : App.lang('Selected discussion has been moved to Trash')
        });
      }
    });
  });
</script>