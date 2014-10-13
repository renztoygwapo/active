{if $label_type == 'ProjectLabel'}
  {title}Project Labels{/title}
  {add_bread_crumb}All Project Labels{/add_bread_crumb}
{else}
  {title}Assignment Labels{/title}
  {add_bread_crumb}All Assignment Labels{/add_bread_crumb}
{/if}

{use_widget name="paged_objects_list" module="environment"}

<div id="labels"></div>

<script type="text/javascript">
  $('#labels').pagedObjectsList({
    'load_more_url' : {$load_more_labels_url|json nofilter},
    'items' : {$labels|json nofilter},
    'items_per_load' : {$labels_per_page|json nofilter},
    'total_items' : {$total_labels|json nofilter},
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'label' }, 
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Label'),
      'options' : '' 
    },
    'sort_by' : function() {
      return $(this).find('td.name span.label').text();
    }, 
    'empty_message' : App.lang('There are no labels defined'),
    'listen' : {$active_label->getEventNamesPrefix()|json nofilter},
    'on_add_item' : function(item) {
      var label = $(this);
      
      label.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' +  
        '<td class="options"></td>'
      );

      var checkbox = $('<input name="set_default_label" type="checkbox" value="' + label['id'] + '">').click(function() {
        var was_default = !checkbox.prop('checked'); // Click changed the value
        var cell = checkbox.parent();
        
        $('#labels td.is_default input[type=checkbox]').hide();

        cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

        $.ajax({
          'url' : item['urls']['set_as_default'],
          'type' : 'post', 
          'data' : { 'submitted' : 'submitted' }, 
          'success' : function(response) {
            cell.find('img').remove();
            
            $('#labels tr.is_default').removeClass('is_default');
            $('#labels td.is_default input[type=checkbox]').prop('checked', false).show();

            if(!was_default) {
              checkbox.prop('checked', true);

              label.addClass('is_default');
            } // if

            label.highlightFade();
          }, 
          'error' : function(response) {
            cell.find('img').remove();
            $('#labels td.is_default input[type=checkbox]').show();

            App.Wireframe.Flash.error('Failed to set selected label as default');
          } 
        });

        return false;
      }).appendTo(label.find('td.is_default'));

      if(item['is_default']) {
        label.addClass('is_default');
        checkbox[0].checked = true;
      } // if
      
      label.find('td.name').html(App.Wireframe.Utils.renderLabel(item));

      var options_cell = label.find('td.options');

      options_cell.append('<a href="' + item['urls']['edit'] + '" class="edit_label" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>');

      options_cell.find('a.edit_label').flyoutForm({
        'success_event' : '{$active_label->getUpdatedEventName()}'
      });
      
      options_cell.append('<a href="' + item['urls']['delete'] + '" class="delete_label" title="' + App.lang('Remove Labels') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>')

      options_cell.find('a.delete_label').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this label?'), 
        'success_event' : '{$active_label->getDeletedEventName()}', 
        'success_message' : App.lang('Label has been deleted successfully')
      });
    }
  });
</script>