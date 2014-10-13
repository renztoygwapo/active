<?php /* Smarty version Smarty-3.1.12, created on 2014-06-25 16:25:14
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects/_initialize_objects_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:141028760353aaf7ea199c77-03706867%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1926f0d3ab03ebce78611915ce350e76e0038d4' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/projects/_initialize_objects_list.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '141028760353aaf7ea199c77-03706867',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'projects' => 0,
    'owner_company' => 0,
    'categories' => 0,
    'companies' => 0,
    'labels' => 0,
    'print_url' => 0,
    'active_project' => 0,
    'mass_manager' => 0,
    'in_archive' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53aaf7ea3301f1_31156477',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53aaf7ea3301f1_31156477')) {function content_53aaf7ea3301f1_31156477($_smarty_tpl) {?><?php if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_modifier_map')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.map.php';
if (!is_callable('smarty_function_custom_fields_prepare_objects_list')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/custom_fields/helpers/function.custom_fields_prepare_objects_list.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php echo smarty_function_use_widget(array('name'=>"objects_list",'module'=>"environment"),$_smarty_tpl);?>


<script type="text/javascript">
  $('#new_project').flyoutForm({
    'title' : App.lang('New Project'),
    'success_event' : 'project_created'
  });

  $('#projects').each(function() {
    var wrapper = $(this);

    var items = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['projects']->value);?>
;
    var owner_company_id = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['owner_company']->value->getId());?>
;
    var categories_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['categories']->value);?>
;
    var companies_map = <?php echo smarty_modifier_map($_smarty_tpl->tpl_vars['companies']->value);?>
;

    var labels = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['labels']->value);?>
;
    var labels_map = new App.Map();

    if(typeof(labels) == 'object' && labels) {
      App.each(labels, function(k, label) {
        labels_map.set(label['id'], label['name']);
      });
    } // if

    var print_url = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['print_url']->value);?>
;

    var grouping = [{
      'label' : App.lang("Don't group"),
      'property' : '',
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/dont-group.png', 'environment')
    }, {
      'label' : App.lang('By Category'),
      'property' : 'category_id',
      'map' : categories_map,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-category.png', 'categories'),
      'default' : true
    }, {
      'label' : App.lang('By Client'),
      'property' : 'company_id',
      'map' : companies_map ,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-client.png', 'system'),
      'uncategorized_label' : App.lang('Internal')
    }, {
      'label' : App.lang('By Label'),
      'property' : 'label_id',
      'map' : labels_map,
      'icon' : App.Wireframe.Utils.imageUrl('objects-list/group-by-label.png', 'labels'),
      'uncategorized_label' : App.lang('No Label')
    }];

    <?php echo smarty_function_custom_fields_prepare_objects_list(array('grouping_variable'=>'grouping','type'=>'Project','sample'=>$_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>


    var init_options = {
      'id' : 'projects',
      'items' : items,
      'required_fields' : ['id', 'name', 'is_completed', 'category_id', 'label_id', 'company_id', 'icon', 'permalink'],
      'requirements' : {},
      'objects_type' : 'projects',
      'events' : App.standardObjectsListEvents(),
      'show_goto_arrow' : true,
      'multi_title' : App.lang(':num Projects Selected'),
      'multi_url' : '<?php echo smarty_function_assemble(array('route'=>'projects_mass_edit'),$_smarty_tpl);?>
',
      'multi_actions' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['mass_manager']->value);?>
,
      'print_url' : print_url,
      'prepare_item' : function (item) {
        return {
          'id'                : item['id'],
          'name'              : item['name'],
          'is_completed'      : item['completed_on'] === null ? 0 : 1,
          'category_id'       : item['category'] ? item['category']['id'] : (item['category_id'] ? item['category_id'] : 0),
          'label_id'          : item['label_id'],
          'company_id'        : item['company'] ? item['company']['id'] : null,
          'company_name'      : item['company'] ? item['company']['name'] : null,
          'company_permalink' : item['company'] ? item['company']['permalink'] : null,
          'icon'              : item['avatar']['small'],
          'goto_url'          : item['permalink'],
          'permalink'         : App.extendUrl(item['permalink'], { 'brief' : 1 }),
          'is_favorite'       : item['is_favorite'],
          'total_assignments' : item['total_assignments'],
          'open_assignments'  : item['open_assignments'],
          'is_archived'       : item['state'] == '2' ? 1 : 0,
          'label'             : item['label']
        };
      },

      'render_item' : function (item) {
        var row = '<td class="icon"><img src="' + item.icon + '" alt="icon"></td><td class="name">' + App.clean(item.name) + '</td><td class="project_options">';

        row += App.Wireframe.Utils.renderLabelTag(item.label);

        // Completed task
        if(item['is_completed']) {
          row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-mono-100.png', 'complete') + '">';

          // Open project
        } else {

          var total_assignments = typeof(item['total_assignments']) != 'undefined' && item['total_assignments'] ? item['total_assignments'] : 0;
          var open_assignments = typeof(item['open_assignments']) != 'undefined' && item['open_assignments'] ? item['open_assignments'] : 0;

          var completed_assignments  = total_assignments - open_assignments;

          var color_class = 'mono';

          if(total_assignments > 0 && completed_assignments > 0) {
            if(completed_assignments >= total_assignments) {
              row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-100.png', 'complete') + '">';
            } else {
              var percentage = Math.ceil((completed_assignments / total_assignments) * 100);

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
          } else {
            row += '<img src="' + App.Wireframe.Utils.imageUrl('progress/progress-' + color_class + '-0.png', 'complete') + '">';
          } // if
        } // if

        row += '</td>';

        return row;
      },

      'search_index' : function (item) {
        return App.clean(item.name);
      },

      'grouping' : grouping,
      'filtering' : []
    }


    if (!<?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['in_archive']->value);?>
) {
      init_options.filtering.push({
        'label' : App.lang('Status'),
        'property' : 'is_completed',
        'values'  : [{
          'label' : App.lang('Active and Completed'),
          'value' : '',
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active-and-completed.png', 'complete'),
          'default' : true,
          'breadcrumbs' : App.lang('Active and Completed')
        }, {
          'label' : App.lang('Active Only'),
          'value' : 0,
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/active.png', 'complete'),
          'breadcrumbs' : App.lang('Active')
        }, {
          'label' : App.lang('Completed Only'),
          'value' : 1,
          'icon' : App.Wireframe.Utils.imageUrl('objects-list/completed.png', 'complete'),
          'breadcrumbs' : App.lang('Completed')
        }]
      });

      init_options.requirements.is_archived = 0;
    } else {
      init_options.requirements.is_archived = 1;
    } // if

    wrapper.objectsList(init_options);

    // project added
    App.Wireframe.Events.bind('project_created.content', function (event, project) {
      wrapper.objectsList('add_item', project);
    });

    // project updated
    App.Wireframe.Events.bind('project_updated.content', function (event, project) {
      wrapper.objectsList('update_item', project);
    });

    // Project deleted
    App.Wireframe.Events.bind('project_deleted.content', function (event, project) {
      wrapper.objectsList('delete_item', project['id']);
    });

    // Keep company_id map up to date
    App.objects_list_keep_companies_map_up_to_date(wrapper, 'company_id', 'content');
    // Kepp categories map up to date
    App.objects_list_keep_categories_map_up_to_date(wrapper, 'category_id', <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->category()->getCategoryContextString());?>
, <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->category()->getCategoryClass());?>
);

    <?php if ($_smarty_tpl->tpl_vars['active_project']->value&&$_smarty_tpl->tpl_vars['active_project']->value->isLoaded()){?>
      wrapper.objectsList('load_item', <?php echo clean(smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getId()),$_smarty_tpl);?>
, '<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getViewUrl(),$_smarty_tpl);?>
');
    <?php }?>
  });
</script><?php }} ?>