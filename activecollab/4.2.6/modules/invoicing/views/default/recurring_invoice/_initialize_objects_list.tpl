<script type="text/javascript">
  $('#new_recurring_profile').flyoutForm({
    'success_event' : 'recurring_profile_created',
    'title' : App.lang('New Recurring Profile')
  });

  $('#recurring_profile').each(function() {
    var objects_list_wrapper = $(this);

    var items = {$recurring_profiles|json nofilter};
    var companies_map = {$companies_map|json nofilter};
    var mass_edit_url = '{assemble route=recurring_profiles_mass_edit}';

    var print_url = {$print_url|json nofilter};

    var in_archive = {$in_archive|json nofilter}

    objects_list_wrapper.find('div#skipped_profiles_wrapper td.skipped_link a').asyncLink({
      'success_event' : 'recurring_profile_updated',
      'confirmation' : App.lang('Are you sure that you want to trigger this profile?'),
      'success_message' : App.lang('Recurring invoice profile has been successfully triggered')
    });

    objects_list_wrapper.objectsList({
      'id'                : 'recurring_invoices',
      'items'             : items,
      'required_fields'   : ['id', 'name', 'client_id', 'permalink'],
      'objects_type'      : 'recurring_profiles',
      'events'            : App.standardObjectsListEvents(),
      'multi_title'       : App.lang(':num Recurring Profiles Selected'),
      'multi_url'         : mass_edit_url,
      'print_url'          : print_url,
      'prepare_item'      : function (item) {
        return {
          'id' : item['id'],
          'name' : item['name'],
          'client_id' : item['client']['id'],
          'permalink' : item['urls']['view'],
          'is_archived' : item['state'] == '2' ? '1' : '0',
          'is_skipped' : item['is_skipped']
        };
      },
      'render_item'       : function (item) {
        var warning_image = '';
        if (item.is_skipped) {
          warning_image = '<img src="' + App.Wireframe.Utils.indicatorUrl('warning') + '" id="item_icon_' + item.id + '" />';
        }//if
        return '<td class="recurring_profile_name">' + App.clean(item.name) + '</td><td class="recurring_profile_attention_image"> ' + warning_image + '</td>';
      },

      'grouping'          : [{
        'label' : App.lang("Don't group"),
        'property' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
      }, {
        'label' : App.lang('By Client'),
        'property' : 'client_id',
        'map' : companies_map ,
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
        'uncategorized_label' : App.lang('Unknown Client'),
        'default' : true
      }]

    });
    // recurring profile added
    App.Wireframe.Events.bind('recurring_profile_created.content', function (event, profile) {
      objects_list_wrapper.objectsList('add_item', profile);
    });

    // recurring profile update
    App.Wireframe.Events.bind('recurring_profile_updated.content', function (event, profile) {
      if((in_archive && profile['is_archived'] == 0) || (!in_archive && profile['is_archived'] == 1)) {
        objects_list_wrapper.objectsList('delete_item', profile['id']);
        objects_list_wrapper.objectsList('load_empty');
        if(profile['is_archived'] == 1) {
          remove_skipped_table(profile);
        }//if
        return false;
      } //if

      objects_list_wrapper.objectsList('update_item', profile);
      remove_skipped_table(profile);
    });

    function remove_skipped_table(profile) {
      var skipped_profiles_wrapper = objects_list_wrapper.find('div#skipped_profiles_wrapper');
      skipped_profiles_wrapper.find('tr#skipped_profile_' + profile.id).remove();
      if(skipped_profiles_wrapper.find('.skipped_profiles_table tr').length == 0) {
        skipped_profiles_wrapper.remove();
      }
    } //remove_skiped_table

    // recurring profile deleted
    App.Wireframe.Events.bind('recurring_profile_deleted.content', function (event, profile) {
      objects_list_wrapper.objectsList('delete_item', profile['id']);
    });

    // keep company_id map up to date
    App.objects_list_keep_companies_map_up_to_date(objects_list_wrapper, 'client_id', 'content');

    // Pre select item if this is permalink
    {if $active_recurring_profile->isLoaded()}
      objects_list_wrapper.objectsList('load_item', {$active_recurring_profile->getId()}, {$active_recurring_profile->getViewUrl()|json nofilter});
    {/if}

  });
</script>