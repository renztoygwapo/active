{title}Hourly Rates{/title}
{add_bread_crumb}Hourly Rates{/add_bread_crumb}
{use_widget name="paged_objects_list" module="environment"}

<div id="project_hourly_rates"></div>

<script type="text/javascript">
  $('#project_hourly_rates').each(function() {
    var wrapper = $(this);

    var project_currency = {$active_project->getCurrency()|json nofilter};

    wrapper.pagedObjectsList({
      'paged' : false,
      'items' : {$job_types|json nofilter},
      'list_items_are' : 'tr',
      'list_item_attributes' : { 'class' : 'job_type' },
      'columns' : {
        'name' : App.lang('Job Type'),
        'hourly_rate' : App.lang('Hourly Rate'),
        'options' : ''
      },
      'empty_message' : App.lang('There are no job types defined'),
      'listen' : 'job_type',
      'listen_constraint' : function(event, job_type) {
        if(event.type == 'job_type_updated') {
          return wrapper.find('tr[list_item_id=' + job_type.id + ']').length ? true : false;
        } // if
      },
      'on_add_item' : function(item) {
        var job_type = $(this);

        if(!item['is_active']) {
          job_type.addClass('archived');
        } // if

        job_type.append(
          '<td class="name"></td>' +
          '<td class="hourly_rate"></td>' +
          '<td class="options"></td>'
        );

        job_type.find('td.name').html(App.clean(item['name']));

        if(!item['custom_hourly_rate']) {
          job_type.find('td.hourly_rate').html(App.moneyFormat(item['default_hourly_rate'], project_currency, null, true));
        } else {
          job_type.addClass('custom_hourly_rate');
          job_type.find('td.hourly_rate').html(App.moneyFormat(item['custom_hourly_rate'], project_currency, null, true));
        } // if

        var options_cell = job_type.find('td.options');

        options_cell.append('<a href="' + item['urls']['edit'] + '" class="edit_project_hourly_rate" title="' + App.lang('Edit Project Hourly Rate') + '"><img src="{image_url name="icons/12x12/edit.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="' + App.lang('Edit') + '" /></a>').find('a.edit_project_hourly_rate').flyoutForm({
          'title' : App.lang('Hourly Rate'),
          'success_event' : 'job_type_updated'
        });
      }
    });

    // Job type archived
    App.Wireframe.Events.bind('job_type_archived', function(event, job_type) {
      var current_item = wrapper.find('tr[list_item_id=' + job_type.id + ']');

      if(current_item.length) {
        current_item.addClass('archived');
      } else {
        App.Wireframe.Events.trigger('job_type_deleted.content', [job_type]);
      } // if
    });

    // Job type unarchived
    App.Wireframe.Events.bind('job_type_unarchived', function(event, job_type) {
      var current_item = wrapper.find('tr[list_item_id=' + job_type.id + ']');

      if(current_item.length) {
        current_item.removeClass('archived');
      } else {
        App.Wireframe.Events.trigger('job_type_created.content', [job_type]);
      } // if
    });
  });
</script>