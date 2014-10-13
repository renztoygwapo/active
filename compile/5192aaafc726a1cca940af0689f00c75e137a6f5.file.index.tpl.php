<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:10
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103693668753ac9916a83778-07729630%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5192aaafc726a1cca940af0689f00c75e137a6f5' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/tasks/views/default/tasks/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103693668753ac9916a83778-07729630',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'add_task_url' => 0,
    'active_project' => 0,
    'manage_categories_url' => 0,
    'can_manage_tasks' => 0,
    'to_clean_up' => 0,
    'clean_up_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9916bc3435_54493387',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9916bc3435_54493387')) {function content_53ac9916bc3435_54493387($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_block_button')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.button.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="tasks">
  <div class="empty_content">
      <div class="objects_list_title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tasks<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
      <div class="objects_list_icon"><img src="<?php echo smarty_function_image_url(array('name'=>'icons/48x48/tasks.png','module'=>@TASKS_MODULE),$_smarty_tpl);?>
" alt=""/></div>
      <div class="objects_list_details_actions">
        <ul>
          <?php if ($_smarty_tpl->tpl_vars['add_task_url']->value){?><li><a href="<?php echo smarty_function_assemble(array('route'=>'project_tasks_add','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
" id="new_project_task"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Task<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php }?>
          <?php if ($_smarty_tpl->tpl_vars['manage_categories_url']->value){?><li><a href="<?php echo clean($_smarty_tpl->tpl_vars['manage_categories_url']->value,$_smarty_tpl);?>
" class="manage_objects_list_categories" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Task Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage Categories<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></li><?php }?>
        </ul>
      </div>

      <?php if ($_smarty_tpl->tpl_vars['can_manage_tasks']->value){?>
        <div class="object_list_details_additional_actions">
          <a href="<?php echo smarty_function_assemble(array('route'=>'project_tasks_archive','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
" id="view_archive"><span><img src="<?php echo smarty_function_image_url(array('name'=>"icons/12x12/archive.png",'module'=>"environment"),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
        </div>
      <?php }?>

      <div class="object_lists_details_bottom">
        <?php if ($_smarty_tpl->tpl_vars['to_clean_up']->value){?>
          <div class="tidy_up_tasks" id="clean_up_tasks" style="display: block;">
            <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tidy Up!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h3>
            <p class="tidy_up_button_wrapper"><?php $_smarty_tpl->smarty->_tag_stack[] = array('button', array('class'=>'default')); $_block_repeat=true; echo smarty_block_button(array('class'=>'default'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Move to Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_button(array('class'=>'default'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p>
            <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Move tasks that you no longer need to archive to keep the main task list lean. By doing that, it will load faster, task will be easier to filter, reorder and more<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
.</p>
            <?php if ($_smarty_tpl->tpl_vars['to_clean_up']->value==1){?>
              <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There is <u>one task completed in more than 30 days ago</u>. Click on the button below to move it to archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</p>
              <?php }else{ ?>
              <p><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('num'=>$_smarty_tpl->tpl_vars['to_clean_up']->value)); $_block_repeat=true; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['to_clean_up']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
There are <u><strong>:num tasks completed in more than 30 days ago</strong></u>. Click on the button below to move them to archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('num'=>$_smarty_tpl->tpl_vars['to_clean_up']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</p>
            <?php }?>
          </div>
        <?php }?>

        <div class="object_lists_details_tips">
          <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Tips<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</h3>
          <ul>
            <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
To select a task and load its details, please click on it in the list on the left<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
            <li><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
It is possible to select multiple tasks at the same time. Just hold Ctrl key on your keyboard and click on all the tasks that you want to select<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
          </ul>
        </div>
      </div>
  </div>
</div>

<?php echo $_smarty_tpl->getSubTemplate (get_view_path('_initialize_objects_list','tasks',@TASKS_MODULE), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<script type="text/javascript">
  $('#clean_up_tasks button').click(function() {
    var button = $(this).hide();

    button.parent().append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');

    $.ajax({
      'url' : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['clean_up_url']->value);?>
,
      'type' : 'post',
      'data' : 'submitted=submitted',
      'success' : function(response) {
        if(jQuery.isArray(response)) {
          $("#clean_up_tasks").remove();

          if(response.length === 1) {
            App.Wireframe.Flash.success('One task has been moved to archive');
          } else {
            App.Wireframe.Flash.success(':num tasks have been moved to archive', {
              'num' : response.length
            });
          } // if

          $.each(response, function(k, v) {
            $('#tasks').objectsList('delete_item', v);
          });
        } else {
          App.Wireframe.Flash.error('Invalid response. Please try again later');

          button.parent().find('img').remove();
          button.show();
        } // if
      },
      'error' : function() {
        App.Wireframe.Flash.error('Failed to archive old tasks. Please try again later');

        button.parent().find('img').remove();
        button.show();
      }
    });
  });
</script><?php }} ?>