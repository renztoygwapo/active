{title}Currencies{/title}
{add_bread_crumb}List All{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

{if $request->get('flyout')}
<script type="text/javascript">
  App.widgets.FlyoutDialog.front().addButton('new_currency', {$wireframe->actions->get('new_currency')|json nofilter});
</script>
{/if}

<div id="currencies"></div>

<script type="text/javascript">
  var flyout_id = {$request->get('flyout')|json nofilter};

  $('#currencies').pagedObjectsList({
    'load_more_url' : '{assemble route=admin_currencies}', 
    'items' : {$currencies|json nofilter},
    'items_per_load' : {$currencies_per_page}, 
    'total_items' : {$total_currencies}, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'currency' }, 
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Currency'), 
      'code' : App.lang('Code'), 
      'options' : '' 
    },
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no currencies defined'), 
    'listen' : 'currency',
    'listen_scope' : flyout_id ? flyout_id : 'content',
    'class' : 'admin_list',
    'on_add_item' : function(item) {
      var currency = $(this);
      
      currency.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' + 
        '<td class="code"></td>' + 
        '<td class="options"></td>'
      );
  
      var radio = $('<input name="set_default_currency" type="radio" value="' + item['id'] + '" />').click(function() {
        if(!currency.is('tr.is_default')) {
          if(confirm(App.lang('Are you sure that you want to set this currency as default currency?'))) {
            var cell = radio.parent();
            
            $('#currencies td.is_default input[type=radio]').hide();
  
            cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');
  
            $.ajax({
              'url' : item['urls']['set_as_default'],
              'type' : 'post', 
              'data' : { 'submitted' : 'submitted' }, 
              'success' : function(response) {
                cell.find('img').remove();
                radio[0].checked = true;
                $('#currencies td.is_default input[type=radio]').show();
                $('#currencies tr.is_default').find('td.options a.delete_currency').show();
                $('#currencies tr.is_default').removeClass('is_default');
  
                currency.addClass('is_default').highlightFade();
                currency.find('td.options a.delete_currency').hide();
              }, 
              'error' : function(response) {
                cell.find('img').remove();
                $('#currencies td.is_default input[type=radio]').show();
  
                App.Wireframe.Flash.error('Failed to set selected currency as default');
              } 
            });
          } // if
        } // if
  
        return false;
      }).appendTo(currency.find('td.is_default'));
  
      if(item['is_default']) {
        currency.addClass('is_default');
        radio[0].checked = true;
      } // if
      
      currency.find('td.name').text(item['name']);
      currency.find('td.code').text(item['code']);

      var currency_options = currency.find('td.options');
      currency_options.append('<a href="' + item['urls']['edit'] + '" class="edit_currency" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>');
      if (item['permissions']['can_delete'] && !item['is_default']) {
        currency_options.append('<a href="' + item['urls']['delete'] + '" class="delete_currency" title="' + App.lang('Remove Currency') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Delete') + '" /></a>');
      } // if
      
      currency.find('td.options a.edit_currency').flyoutForm({
        'success_event' : 'currency_updated',
        'width' : 'narrow'
      });
      
      currency.find('td.options a.delete_currency').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this currency?'), 
        'success_event' : 'currency_deleted', 
        'success_message' : App.lang('Currency has been deleted successfully')
      });
    }
  });
</script>