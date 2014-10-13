{title}All Announcements{/title}
{add_bread_crumb}All Announcements{/add_bread_crumb}

{use_widget name="paged_objects_list" module=$smarty.const.ENVIRONMENT_FRAMEWORK}
{use_widget name="ui_sortable" module="environment"}

<div id="announcements"></div>

<script type="text/javascript">
  $('#announcements').pagedObjectsList({
    'load_more_url' : '{assemble route=admin_announcements}',
    'items' : {$announcements|json nofilter},
    'items_per_load' : {$announcements_per_page},
    'total_items' : {$total_announcements},
    'list_items_are' : 'tr',
    'list_item_attributes' : { 'class' : 'announcement' },
    'class' : 'admin_list',
    'columns' : {
      'drag' : '',
      'is_enabled' : '',
      'icon' : App.lang('Icon'),
      'subject' : App.lang('Subject'),
      'created_on' : App.lang('Created On / By'),
      'dismissals' : App.lang('Visible To / Dismissed By'),
      'expires_on' : App.lang('Expires On'),
      'options' : ''
    },
    'empty_message' : App.lang('There are no announcements defined'),
    'listen' : 'announcement',
    'on_add_item' : function(item) {
      var announcement = $(this);

      announcement.append(
        '<td class="drag_icon"></td>' +
        '<td class="is_enabled"></td>' +
        '<td class="icon"></td>' +
        '<td class="subject"></td>' +
        '<td class="created_on"></td>' +
        '<td class="dismissals"></td>' +
        '<td class="expires_on"></td>' +
        '<td class="options"></td>'
      );

      announcement.find('.drag_icon').append('<img src="{image_url name="layout/bits/handle-drag.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" style="cursor: move;">');

      var checkbox = $('<input type="checkbox" />').attr({
        'on_url' : item['urls']['enable'],
        'off_url' : item['urls']['disable']
      }).asyncCheckbox({
        'success_event' : 'announcement_updated',
        'success_message' : [ App.lang('Announcement has been disabled'), App.lang('Announcement has been enabled') ]
      }).appendTo(announcement.find('td.is_enabled'));

      if(item['is_enabled']) {
        checkbox[0].checked = true;
      } // if

      announcement.find('td.icon').append('<img src="' + item['icon_url'] + '" />');

      if(typeof(item['subject']) == 'string' && item['subject']) {
        if(item['subject'].length > 50) {
          announcement.find('td.subject').append(App.clean(item['subject'].substr(0, 50)) + '...');
        } else {
          announcement.find('td.subject').append(App.clean(item['subject']));
        } // if
      } // if

      announcement.find('td.created_on').append(item['created_by']['created_ago']);
      announcement.find('td.created_on').append(App.lang(' by <a href=":user_link">:user_name</a>', {
        'user_link' : item['created_by']['urls']['view'],
        'user_name' : item['created_by']['short_display_name']
      }));

      announcement.find('td.dismissals').append(App.lang(':visible_to_count / :dismissed_by_count', {
        'visible_to_count' : item['visible_to_count'],
        'dismissed_by_count' : item['dismissed_by_count']
      }));

      announcement.find('td.expires_on').append(item['expires_on']);

      var options_cell = announcement.find('td.options');

      options_cell
        .append('<a href="' + item['urls']['edit'] + '" class="edit_announcement" title="' + App.lang('Edit Announcement') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
        .append('<a href="' + item['urls']['delete'] + '" class="delete_announcement" title="' + App.lang('Remove Announcement') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>')
      ;

      options_cell.find('a.edit_announcement').flyoutForm({
        'success_event' : 'announcement_updated',
        'width' : 565
      });
      options_cell.find('a.delete_announcement').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this announcement?'),
        'success_event' : 'announcement_deleted',
        'success_message' : App.lang('Announcement has been deleted successfully')
      });
    }
  }).sortable({
    'axis' : 'y',
    'handle' : 'td.drag_icon',
    'items' : 'tr.announcement',
    'helper' : function(event, ui) {
      ui.siblings().andSelf().children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    },
    'start' : function(event, ui) {
      ui.item.css('background', '#E6E6E6');
    },
    'stop' : function(event, ui) {
      ui.item.css('background', 'transparent');
    },
    'update' : function(event, ui) {
      var data = {
        'submitted' : 'submitted'
      };

      var counter = 1;

      $(this).find('table.admin_list tr.announcement').each(function() {
        data['announcements[' + $(this).attr('list_item_id') + ']'] = counter++;
      });

      $.ajax({
        'url' : '{assemble route=admin_announcements_reorder}',
        'type' : 'post',
        'data' : data,
        'error' : function() {
          App.Wireframe.Flash.error('Failed to reorder announcements. Please try again later.');
        }
      });
    }
  }).disableSelection();
</script>