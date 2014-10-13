{title}Tax Rates{/title}
{add_bread_crumb}List All{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

{if $request->get('flyout')}
  <script type="text/javascript">
    App.widgets.FlyoutDialog.front().addButton('new_tax_rate', {$wireframe->actions->get('new_tax_rate')|json nofilter});
  </script>
{/if}

<div id="tax_rates"></div>

<script type="text/javascript">
  $('#tax_rates').each(function() {
    var tax_rates_widget = $(this);
    var flyout_id = {$request->get('flyout')|json nofilter};

    tax_rates_widget.pagedObjectsList({
      'load_more_url' : '{assemble route=admin_tax_rates}',
      'items' : {$tax_rates|json nofilter},
      'items_per_load' : {$items_per_page},
      'total_items' : {$total_items},
      'list_items_are' : 'tr',
      'list_item_attributes' : { 'class' : 'tax_rates' },
      'class' : 'admin_list',
      'columns' : {
        'default'     : '',
        'name'        : App.lang('Name'),
        'tax_rate'    : App.lang('Tax Rate (%)'),
        'options'     : ''
      },
      'sort_by' : 'name',
      'empty_message' : App.lang('There are no tax rates defined'),
      'listen' : 'tax_rate', // created, updated, deleted
      'listen_scope' : flyout_id ? flyout_id : 'content',
      'on_add_item' : function(item) {
        var tax_rate = $(this);

        tax_rate.append(
          '<td class="default_toggler"></td>',
          '<td class="name"></td>' +
            '<td class="percentage"></td>' +
            '<td class="options"></td>'
        );

        var toggler_cell = tax_rate.find('td.default_toggler');
        var checkbox_wrapper = $('<span class="checkbox_wrapper"></span>').appendTo(toggler_cell);
        var checkbox_label_set = $('<span class="checkbox_label checkbox_label_set">' + App.lang('Set as Default') + '</span>').appendTo(checkbox_wrapper);
        var checkbox_label_remove = $('<span class="checkbox_label checkbox_label_remove">' + App.lang('Remove Default') + '</span>').appendTo(checkbox_wrapper);
        var checkbox = $('<input type="checkbox" set_as_default_url="' + item['urls']['set_as_default'] + '" remove_default_url="' + item['urls']['remove_default'] + '" />').appendTo(checkbox_wrapper);

        if (item['is_default']) {
          tax_rate.addClass('default');
          checkbox.attr('checked', true);
        } // if

        tax_rate.attr('id',item['id']);
        tax_rate.find('td.name').text(App.clean(item['name']));
        tax_rate.find('td.percentage').text(App.numberFormat(item['percentage'], null, 3, true));

        if(item['permissions']['can_edit']) {
          tax_rate.find('td.options').append('<a href="' + item['urls']['edit'] + '" class="edit_tax_rate" title="' + App.lang('Change Settings') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');
          tax_rate.find('td.options a.edit_tax_rate').flyoutForm({
            'success_event' : 'tax_rate_updated',
            'width' : 500
          });
        } // if

        if(item['permissions']['can_delete']) {
          tax_rate.find('td.options').append('<a href="' + item['urls']['delete'] + '" class="delete_tax_rate" title="' + App.lang('Remove Item') + '"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" /></a>');
          tax_rate.find('td.options a.delete_tax_rate').asyncLink({
            'confirmation' : App.lang('Are you sure that you want to permanently delete this item?'),
            'success_event' : 'tax_rate_deleted',
            'success_message' : App.lang('Tax rate has been deleted successfully'),
            'error' : function() {
              App.Wireframe.Flash.error(App.lang('Failed to delete selected item'));
            }
          });
        } // if
      }
    }).on('click', 'input[type="checkbox"]', function (event) {
      var checkbox = $(this).hide();
      var indicator = $('<img src="' + App.Wireframe.Utils.indicatorUrl('small') + '" />').insertAfter(checkbox);

      var ajax_url = checkbox.attr('set_as_default_url');
      if (!checkbox.is(':checked')) {
        ajax_url = checkbox.attr('remove_default_url');
      } // if

      $.ajax({
        'url' : ajax_url,
        'success' : function (response) {
          if (checkbox.is(':checked')) {
            tax_rates_widget.find('tr.default').each(function () {
              $(this).removeClass('default').find('input[type="checkbox"]').removeAttr('checked');
            });
          } // if

          App.Wireframe.Events.trigger('tax_rate_updated', [response]);
          indicator.remove();
          checkbox.show();
        },
        'error' : function (response) {
          indicator.remove();
          checkbox.show();
          if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
          } else {
            checkbox.attr('checked', true);
          } // if
        }
      })
    });
  });
</script>