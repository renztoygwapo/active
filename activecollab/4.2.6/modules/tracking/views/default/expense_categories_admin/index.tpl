{title}Expense Categories{/title}
{add_bread_crumb}All Expense Categories{/add_bread_crumb}
{use_widget name="paged_objects_list" module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="expense_categories"></div>

<script type="text/javascript">
  $('#expense_categories').pagedObjectsList({
    'load_more_url' : '{assemble route=expense_categories_admin}', 
    'items' : {$expense_categories|json nofilter},
    'items_per_load' : {$expense_categories_per_page}, 
    'total_items' : {$total_expense_categories}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'expense_category' }, 
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Name'),  
      'options' : '' 
    },
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no expense categories defined'),
    'listen' : 'expense_category',  
    'on_add_item' : function(item) {
      var expense_category = $(this);
      
      expense_category.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' +  
        '<td class="options"></td>'
      );

      var radio = $('<input name="set_default_expense_category" type="radio" value="' + expense_category['id'] + '" />').click(function() {
        if(!expense_category.is('tr.is_default')) {
          if(confirm(App.lang('Are you sure that you want to set this expense category as default expense category?'))) {
            var cell = radio.parent();
            var table = cell.parent().parent().parent();
            
            table.find('td.is_default input[type=radio]').hide();

            cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

            $.ajax({
              'url' : item['urls']['set_as_default'],
              'type' : 'post', 
              'data' : { 'submitted' : 'submitted' }, 
              'success' : function(response) {
                cell.find('img').remove();
                radio.prop('checked', true);

                table.find('td.is_default input[type=radio]').show();
                table.find('tr.is_default').removeClass('is_default');

                expense_category.addClass('is_default');               
              }, 
              'error' : function(response) {
                cell.find('img').remove();
                
                table.find('td.is_default input[type=radio]').show();

                App.Wireframe.Flash.error('Failed to set selected expense category as default');
              } 
            });
          } // if
        } // if

        return false;
      }).appendTo(expense_category.find('td.is_default'));

      if(item['is_default']) {
        expense_category.addClass('is_default');
        radio[0].checked = true;
      } // if
      
      expense_category.find('td.name').text(item['name']);
 
      expense_category.find('td.options')
        .append('<a href="' + item['urls']['edit'] + '" class="edit_expense_category" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>')
      ;
      
      if(item['permissions']['can_delete']) {
    	  expense_category.find('td.options').append('<a href="' + item['urls']['delete'] + '" class="delete_expense_category" title="' + App.lang('Remove Expense Category') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>');
      }

      if (expense_category.is('tr.is_default')) {
    	  expense_category.find('td.options a.delete_expense_category').hide();           
      } //if
      
      expense_category.find('td.options a.edit_expense_category').flyoutForm({
        'success_event' : 'expense_category_updated'
      });
      
      expense_category.find('td.options a.delete_expense_category').asyncLink({
        'before' : function () {
          if ($(this).parent().parent().is('tr.is_default')) {
        	  App.Wireframe.Flash.error(App.lang('Cannot delete default expense category'));
            return false;
          } //if
        },
        'confirmation' : App.lang('Are you sure that you want to permanently delete this expense category?'), 
        'success_event' : 'expense_category_deleted', 
        'success_message' : App.lang('Expense category has been deleted successfully')
      });
    }
  });
</script>