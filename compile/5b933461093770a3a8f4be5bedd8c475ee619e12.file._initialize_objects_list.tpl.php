<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:10
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/_initialize_objects_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:59816673953ac9916bd2082-60943957%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b933461093770a3a8f4be5bedd8c475ee619e12' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/_initialize_objects_list.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '59816673953ac9916bd2082-60943957',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_project' => 0,
    'categories' => 0,
    'milestones' => 0,
    'labels' => 0,
    'users' => 0,
    'priority' => 0,
    'print_url' => 0,
    'active_task' => 0,
    'tasks' => 0,
    'mass_manager' => 0,
    'in_archive' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9916ce9701_95713679',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9916ce9701_95713679')) {function content_53ac9916ce9701_95713679($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_map')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.map.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_function_custom_fields_prepare_objects_list')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/custom_fields/helpers/function.custom_fields_prepare_objects_list.php';
?><?php echo smarty_function_use_widget(array('name'=>"objects_list",'module'=>"environment"),$_smarty_tpl);?>


<script type="text/javascript">
$('#new_project_task').flyoutForm({
  'success_event' : 'task_created',
  'title' : App.lang('New Task')
});

$('#tasks').each(function() {
  var wrapper = $(this);

  var project_id = '<?php echo $_smarty_tpl->tpl_vars['active_project']->value->getId();?>
';
  var categories_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['categories']->value);?>
;
  var milestones_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['milestones']->value);?>
;
  var labels_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['labels']->value);?>
;
  var users_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['users']->value);?>
;
  var priority_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['priority']->value);?>
;
  var reorder_url = '<?php echo smarty_function_assemble(array('route'=>'project_tasks_reorder','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
';

  var print_url = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['print_url']->value);?>
;

  var grouping = [{
    'label' : App.lang("Don't group"),
    'property' : '',
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
  }, {
    'label' : App.lang('By Category'),
    'property' : 'category_id' ,
    'map' : categories_map,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories'),
    'default' : true
  }, {
    'label' : App.lang('By Milestone'),
    'property' : 'milestone_id',
    'map' : milestones_map ,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-milestones.png', 'system'),
    'uncategorized_label' : App.lang('No Milestone')
  }, {
    'label' : App.lang('By Label'),
    'property' : 'label_id',
    'map' : labels_map ,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-label.png', 'labels'),
    'uncategorized_label' : App.lang('No Label')
  }, {
    'label' : App.lang('By Assignee'),
    'property' : 'assignee_id',
    'map' : users_map ,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
    'uncategorized_label' : App.lang('Not Assigned')
  }, {
    'label' : App.lang('By Delegate'),
    'property' : 'delegated_by_id',
    'map' : users_map ,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
    'uncategorized_label' : App.lang('Not Delegated')
  }, {
    'label' : App.lang('By Priority'),
    'property' : 'priority',
    'map' : priority_map ,
    'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-priority.png', 'system'),
    'uncategorized_label' : App.lang('No Priority')
  }];

  <?php echo smarty_function_custom_fields_prepare_objects_list(array('grouping_variable'=>'grouping','type'=>'Task','sample'=>$_smarty_tpl->tpl_vars['active_task']->value),$_smarty_tpl);?>


  var init_options = {
    'id' : 'project_' + <?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getId(),$_smarty_tpl);?>
 + '_tasks',
    'items' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['tasks']->value);?>
,
    'required_fields' : ['id', 'name', 'category_id', 'milestone_id', 'task_id', 'is_completed', 'permalink'],
    'requirements' : {
      'project_id' : '<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getId(),$_smarty_tpl);?>
',
    },
    'objects_type' : 'tasks',
    'print_url' : print_url,
    'events' : App.standardObjectsListEvents(),
    'multi_title' : App.lang(':num Tasks Selected'),
    'multi_url' : '<?php echo smarty_function_assemble(array('route'=>'project_tasks_mass_edit','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
',
    'multi_actions' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_manager']->value);?>
,
    'reorder_url' : reorder_url,
    'prepare_item' : function (item) {
      var result = {
        'id'              : item['id'],
        'name'            : item['name'],
        'project_id'      : item['project_id'],
        'task_id'         : item['task_id'],
        'is_completed'    : item['is_completed'],
        'priority'        : item['priority'],
        'permalink'       : item['permalink'],
        'is_favorite'     : item['is_favorite'],
        'total_subtasks'  : item['total_subtasks'],
        'open_subtasks'   : item['open_subtasks'],
        'is_trashed'      : item['state'] == '1' ? 1 : 0,
        'is_archived'     : item['state'] == '2' ? 1 : 0,
        'label'           : item['label'],
        'visibility'      : item['visibility']
      };

      if(typeof(item['assignee']) == 'undefined') {
        result['assignee_id'] = item['assignee_id'];
      } else {
        result['assignee_id'] = item['assignee'] ? item['assignee']['id'] : 0;
      } // if

      if(typeof(item['delegated_by']) == 'undefined') {
        result['delegated_by_id'] = item['delegated_by_id'];
      } else {
        result['delegated_by_id'] = item['delegated_by'] ? item['delegated_by']['id'] : 0;
      } // if

      if(typeof(item['category']) == 'undefined') {
        result['category_id'] = item['category_id'];
      } else {
        result['category_id'] = item['category'] ? item['category']['id'] : 0;
      } // if

      if(typeof(item['milestone']) == 'undefined') {
        result['milestone_id'] = item['milestone_id'];
      } else {
        result['milestone_id'] = item['milestone'] ? item['milestone']['id'] : 0;
      } // if

      if(typeof(item['label']) == 'undefined') {
        result['label_id'] = item['label_id'];
      } else {
        result['label_id'] = item['label'] ? item['label']['id'] : 0;
      } // if

      // Put custom field values as fields, so group by feature can find them
      if(typeof(item['custom_fields']) == 'object' && item['custom_fields']) {
        App.each(item['custom_fields'], function(field_name, details) {
          result[field_name] = details['value'];
        });
      } // if

      return result;
    },
    'render_item' : function (item) {
      var row = '<td class="task_name">' +
        '<span class="task_name_wrapper">' +
        '<span class="task_id">#' + item['task_id'] + '</span>';

      // label
      row += App.Wireframe.Utils.renderLabelTag(item.label);

      // task name
      row += '<span class="real_task_name">' + App.clean(item['name']) + App.Wireframe.Utils.renderVisibilityIndicator(item['visibility']) + '</span></span></td><td class="task_options">';

      // Completed task
      if(item['is_completed']) {
        if(typeof(item['tracked_time']) != 'undefined' && item['tracked_time']) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-blue-100.png', 'complete') + '">';
        } else {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-mono-100.png', 'complete') + '">';
        } // if

        // Still open
      } else {
        var total_subtasks = typeof(item['total_subtasks']) != 'undefined' && item['total_subtasks'] ? item['total_subtasks'] : 0;
        var open_subtasks = typeof(item['open_subtasks']) != 'undefined' && item['open_subtasks'] ? item['open_subtasks'] : 0;
        var completed_subtasks = total_subtasks - open_subtasks;

        var color_class = 'mono';

        if(typeof(item['estimated_time']) != 'undefined' && typeof(item['tracked_time']) != 'undefined') {
          if(item['estimated_time'] > 0) {
            if(item['tracked_time'] > item['estimated_time']) {
              var color_class = 'red';
            } else if(item['tracked_time'] > 0) {
              var color_class = 'blue';
            } // if
          } else if(item['tracked_time'] > 0) {
            var color_class = 'blue';
          } // if
        } // if

        if (item['is_completed']) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-100.png', 'complete') + '">';
        } else if (completed_subtasks == 0) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
        } else {
          if(completed_subtasks >= total_subtasks) {
            row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-100.png', 'complete') + '">';
          } else {
            var percentage = Math.ceil((completed_subtasks / total_subtasks) * 100);

            if(percentage <= 10) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
            } else if(percentage <= 20) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-10.png', 'complete') + '">';
            } else if(percentage <= 30) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-20.png', 'complete') + '">';
            } else if(percentage <= 40) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-30.png', 'complete') + '">';
            } else if(percentage <= 50) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-40.png', 'complete') + '">';
            } else if(percentage <= 60) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-50.png', 'complete') + '">';
            } else if(percentage <= 70) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-60.png', 'complete') + '">';
            } else if(percentage <= 80) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-70.png', 'complete') + '">';
            } else if(percentage <= 90) {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-80.png', 'complete') + '">';
            } else {
                row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-90.png', 'complete') + '">';
            } // if
          } // if
        } // if
      } // if

      row += '</td>';

      return row;
    },

    'search_index' : function (item) {
      return App.clean(item.name) + ' ' + '#' + item.task_id;
    },

    'grouping' : grouping,
    'filtering' : []
  };

  if (!<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['in_archive']->value);?>
) {
    init_options.filtering.push({
      'label' : App.lang('Status'),
      'property' : 'is_completed',
      'values' : [{
        'label' : App.lang('All Tasks'),
        'value' : '',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete'),
        'breadcrumbs' : App.lang('All Tasks')
      }, {
        'label' : App.lang('Open Tasks'),
        'value' : '0',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'),
        'default' : true,
        'breadcrumbs' : App.lang('Open Tasks')
      }, {
        'label' : App.lang('Completed Tasks'),
        'value' : '1',
        'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'),
        'breadcrumbs' : App.lang('Completed Tasks')
      }]
    });

    init_options.requirements.is_archived = 0;
  } else {
    init_options.requirements.is_archived = 1;
  } // if

  wrapper.objectsList(init_options);

  if (!<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['in_archive']->value);?>
) {
    // Task added
    App.Wireframe.Events.bind('task_created.content', function (event, task) {
      if (task['project_id'] == project_id) {
        wrapper.objectsList('add_item', task);
      } else {
        if ($.cookie('ac_redirect_to_target_project')) {
          App.Wireframe.Content.setFromUrl(task['urls']['view']);
        } // if
      } // if
    });
  } // if

  // Task updated
  App.Wireframe.Events.bind('task_updated.content', function (event, task) {
    if (task['project_id'] == project_id) {
      wrapper.objectsList('update_item', task);
    } else {
      if ($.cookie('ac_redirect_to_target_project')) {
        App.Wireframe.Content.setFromUrl(task['urls']['view']);
      } else {
        wrapper.objectsList('delete_selected_item');
      } // if
    } // if
  });

  // Task deleted
  App.Wireframe.Events.bind('task_deleted.content', function (event, task) {
    if (task['project_id'] == project_id) {
      if (wrapper.objectsList('is_loaded', task['id'], false)) {
        wrapper.objectsList('load_empty');
      } // if
      wrapper.objectsList('delete_item', task['id']);
    } // if
  });

  // Manage mappings
  App.objects_list_keep_milestones_map_up_to_date(wrapper, 'milestone_id', project_id);

  // Kepp categories map up to date
  App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_task']->value->category()->getCategoryContextString());?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_task']->value->category()->getCategoryClass());?>
);

  // Pre select item if this is permalink
  <?php if ($_smarty_tpl->tpl_vars['active_task']->value->isLoaded()){?>
    wrapper.objectsList('load_item', <?php echo clean(smarty_modifier_json($_smarty_tpl->tpl_vars['active_task']->value->getId()),$_smarty_tpl);?>
, '<?php echo clean($_smarty_tpl->tpl_vars['active_task']->value->getViewUrl(),$_smarty_tpl);?>
');
  <?php }?>
});
</script><?php }} ?>