<?php /* Smarty version Smarty-3.1.12, created on 2014-08-16 03:25:26
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_roles_admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:210157620053eecf268ecdd2-61909598%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e171c4400a00444783fe30bb2484d2311067bedc' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project_roles_admin/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '210157620053eecf268ecdd2-61909598',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'project_roles' => 0,
    'project_roles_per_page' => 0,
    'total_project_roles' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53eecf26bd12d8_33421394',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53eecf26bd12d8_33421394')) {function content_53eecf26bd12d8_33421394($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_use_widget')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.use_widget.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All Project Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
All Project Roles<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php echo smarty_function_use_widget(array('name'=>"paged_objects_list",'module'=>"environment"),$_smarty_tpl);?>


<div id="project_roles_admin"></div>

<script type="text/javascript">
  $('#project_roles_admin').pagedObjectsList({
    'load_more_url' : '<?php echo smarty_function_assemble(array('route'=>'admin_project_roles'),$_smarty_tpl);?>
', 
    'items' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['project_roles']->value);?>
,
    'items_per_load' : <?php echo clean($_smarty_tpl->tpl_vars['project_roles_per_page']->value,$_smarty_tpl);?>
, 
    'total_items' : <?php echo clean($_smarty_tpl->tpl_vars['total_project_roles']->value,$_smarty_tpl);?>
, 
    'list_items_are' : 'tr', 
    'list_item_attributes' : { 'class' : 'project_role' }, 
    'columns' : {
      'is_default' : '', 
      'name' : App.lang('Role Name'), 
      'options' : '' 
    }, 
    'sort_by' : 'name', 
    'empty_message' : App.lang('There are no project roles defined'), 
    'listen' : 'project_role', 
    'on_add_item' : function(item) {
      var project_role = $(this);
      
      project_role.append(
       	'<td class="is_default"></td>' + 
        '<td class="name"></td>' + 
        '<td class="options"></td>'
      );
  
      var radio = $('<input name="set_default_project_role" type="radio" value="' + item['id'] + '" />').click(function() {
        if(!project_role.is('tr.is_default')) {
          if(confirm(App.lang('Are you sure that you want to set this project role as default project role?'))) {
            var cell = radio.parent();
            
            $('#project_roles_admin td.is_default input[type=radio]').hide();
  
            cell.append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');
  
            $.ajax({
              'url' : item['urls']['set_as_default'],
              'type' : 'post', 
              'data' : { 'submitted' : 'submitted' }, 
              'success' : function(response) {
                cell.find('img').remove();
                radio[0].checked = true;
                $('#project_roles_admin td.is_default input[type=radio]').show();
                $('#project_roles_admin tr.is_default').find('td.options a.delete_project_role').show();
                $('#project_roles_admin tr.is_default').removeClass('is_default');
  
                project_role.addClass('is_default').highlightFade();
                project_role.find('td.options a.delete_project_role').hide();
              }, 
              'error' : function(response) {
                cell.find('img').remove();
                $('#project_roles_admin td.is_default input[type=radio]').show();
  
                App.Wireframe.Flash.error('Failed to set selected project role as default');
              } 
            });
          } // if
        } // if
  
        return false;
      }).appendTo(project_role.find('td.is_default'));
  
      if(item['is_default']) {
        project_role.addClass('is_default');
        radio[0].checked = true;
      } // if
      
      project_role.find('td.name').text(item['name']);

      var project_role_options = project_role.find('td.options');
      if (item['permissions']['can_edit']) {
        project_role_options.append('<a href="' + item['urls']['edit'] + '" class="edit_project_role" title="' + App.lang('Change Settings') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/edit.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt="' + App.lang('Edit') + '" /></a>');
      } // if

      if (item['permissions']['can_delete'] && !item['is_default']) {
        project_role_options.append('<a href="' + item['urls']['delete'] + '" class="delete_project_role" title="' + App.lang('Remove Project Role') + '"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/delete.png",'module'=>@ENVIRONMENT_FRAMEWORK),$_smarty_tpl);?>
" alt="' + App.lang('Delete') + '" /></a>');
      } // if
      
      project_role.find('td.options a.edit_project_role').flyoutForm({
        'success_event' : 'project_role_updated'
      });
      
      project_role.find('td.options a.delete_project_role').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently delete this project role?'), 
        'success_event' : 'project_role_deleted', 
        'success_message' : App.lang('Project role has been deleted successfully')
      });
    }
  });
</script><?php }} ?>