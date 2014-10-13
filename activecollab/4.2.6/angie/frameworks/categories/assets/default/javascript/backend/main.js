/**
 * Prepare select categories
 *
 * @param String submit_as
 * @param String criterion
 * @param Object filter
 * @param Object data
 * @param Function get_name
 * @param Function get_value
 */
App['Wireframe']['Utils']['dataFilters']['prepareSelectCategories'] = function(submit_as, criterion, filter, data, get_name, get_value) {
  var options = [];

  if(typeof(data['categories']) != 'undefined') {
    App.each(data['categories'], function(k, v) {
      options.push(v);
    });
  } // if

  var picker_wrapper = $('<div class="autocomplete_wrapper"></div>').appendTo(this);

  var picker = $('<input type="text" name="' + submit_as + '[' + get_name(criterion) + ']">').appendTo(picker_wrapper).attr('value', get_value(filter, criterion)).focus();

  picker.multicomplete({
    'source' : options,
    'appendTo' : picker_wrapper,
    'position' : {'my' : 'left bottom', 'at' : 'left bottom', 'of' : picker}
  });

  picker_wrapper.width(picker.width());
}; // prepareSelectCategories

/**
 * Keep categories map up to date
 *
 * @param jQuery objects_list
 * @param String grouping_id
 * @param Object map
 * @return null
 */
App.objects_list_keep_categories_map_up_to_date = function (objects_list, grouping_id, category_context, category_type) {
  // Manage categories
  objects_list.find('.manage_objects_list_categories').manageCategories({
    'context' : category_context,
    'type'    : category_type
  });

  // hook to event when all categories are updated
  App.Wireframe.Events.bind('categories_updated.content', function (event, categories) {
    if (categories['context'] == category_context && categories['type'] == category_type) {
      objects_list.objectsList('grouping_map_replace', 'category_id', categories['map']);
    } // if
  });

  // hook up to event category created
  App.Wireframe.Events.bind('category_created.content', function (event, category, category_map, additional_params) {
    var formatted_category_map = {
      'context' : additional_params['context'],
      'type'    : additional_params['type'],
      'map'     : category_map
    };

    if (additional_params['context'] == category_context && additional_params['type'] == category_type) {
      App.Wireframe.Events.trigger('categories_updated', formatted_category_map);
    } // if
  });
}; // objects_list_keep_categories_map_up_to_date