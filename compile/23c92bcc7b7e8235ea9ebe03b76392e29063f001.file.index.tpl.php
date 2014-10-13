<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:04:11
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:166172624453ac98dbd34e10-74512153%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '23c92bcc7b7e8235ea9ebe03b76392e29063f001' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/index.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '166172624453ac98dbd34e10-74512153',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logged_user' => 0,
    'active_project' => 0,
    'late_and_today' => 0,
    'object' => 0,
    'upcoming_objects' => 0,
    'home_sidebars' => 0,
    'home_sidebar' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac98dc061509_33080998',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac98dc061509_33080998')) {function content_53ac98dc061509_33080998($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_user_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.user_link.php';
if (!is_callable('smarty_function_due_on')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/complete/helpers/function.due_on.php';
if (!is_callable('smarty_function_activity_logs_in')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/activity_logs/helpers/function.activity_logs_in.php';
if (!is_callable('smarty_block_object')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.object.php';
if (!is_callable('smarty_function_project_progress')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.project_progress.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Overview<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Overview<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="project_home">
  <div class="project_home_left"><div class="project_home_left_inner">
    <?php if (Milestones::canAccess($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
      <?php if (is_foreachable($_smarty_tpl->tpl_vars['late_and_today']->value)){?>
      <div id="late_today" class="project_overview_box">
        <div class="project_overview_box_title">
          <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Late / Today Milestones<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
        </div>
        <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
					<table class="common" cellspacing="0">
            <thead>
              <tr>
	              <th class="milestone"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Milestone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	              <th class="responsible"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Responsible Person<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	              <th class="due"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Due On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
              </tr>
            </thead>
						<tbody>
							<?php  $_smarty_tpl->tpl_vars['object'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['object']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['late_and_today']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['object']->key => $_smarty_tpl->tpl_vars['object']->value){
$_smarty_tpl->tpl_vars['object']->_loop = true;
?>
						  <tr class="<?php if ($_smarty_tpl->tpl_vars['object']->value->isLate()){?>late<?php }elseif($_smarty_tpl->tpl_vars['object']->value->isUpcoming()){?>upcoming<?php }else{ ?>today<?php }?>">
                <td class="milestone"><a href="<?php echo clean($_smarty_tpl->tpl_vars['object']->value->getViewUrl(),$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['object']->value->getName(),$_smarty_tpl);?>
</a></td>
								<td class="responsible">
									<?php if ($_smarty_tpl->tpl_vars['object']->value->assignees()->hasAssignee()){?>
									 <span class="details block"><?php echo smarty_function_user_link(array('user'=>$_smarty_tpl->tpl_vars['object']->value->assignees()->getAssignee()),$_smarty_tpl);?>
</span>
                  <?php }else{ ?>
                    ---
									<?php }?>
								</td>
								<td class="due"><?php echo smarty_function_due_on(array('object'=>$_smarty_tpl->tpl_vars['object']->value),$_smarty_tpl);?>
</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
        </div></div>
      </div>
      <?php }?>
      
      <?php if (is_foreachable($_smarty_tpl->tpl_vars['upcoming_objects']->value)){?>
        <div id="upcoming" class="project_overview_box">
          <div class="project_overview_box_title">
            <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Upcoming Milestones<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
          </div>
          <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
            <table class="common" cellspacing="0">
	            <thead>
	              <tr>
		              <th class="milestone"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Milestone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
		              <th class="responsible"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Responsible Person<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
		              <th class="due"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Due On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</th>
	              </tr>
	            </thead>
              <tbody>
              <?php  $_smarty_tpl->tpl_vars['object'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['object']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['upcoming_objects']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['object']->key => $_smarty_tpl->tpl_vars['object']->value){
$_smarty_tpl->tpl_vars['object']->_loop = true;
?>
                <tr class="<?php if ($_smarty_tpl->tpl_vars['object']->value->isLate()){?>late<?php }elseif($_smarty_tpl->tpl_vars['object']->value->isUpcoming()){?>upcoming<?php }else{ ?>today<?php }?>">
                  <td class="milestone"><a href="<?php echo clean($_smarty_tpl->tpl_vars['object']->value->getViewUrl(),$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['object']->value->getName(),$_smarty_tpl);?>
</a></td>
                  <td class="responsible">
	                  <?php if ($_smarty_tpl->tpl_vars['object']->value->assignees()->hasAssignee()){?>
	                    <span class="details block"><?php echo smarty_function_user_link(array('user'=>$_smarty_tpl->tpl_vars['object']->value->assignees()->getAssignee()),$_smarty_tpl);?>
</span>
	                  <?php }else{ ?>
	                    ---
	                  <?php }?>
                  </td>
                  <td class="due"><?php echo smarty_function_due_on(array('object'=>$_smarty_tpl->tpl_vars['object']->value),$_smarty_tpl);?>
</td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div></div>
        </div>
      <?php }?>
    <?php }?>
      
      <div class="project_overview_box" id="project_recent_activities">
        <div class="project_overview_box_title">
          <h2><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Recent Activities<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
        </div>
        <div class="project_overview_box_content">
          <div class="project_overview_box_content_inner"><?php echo smarty_function_activity_logs_in(array('user'=>$_smarty_tpl->tpl_vars['logged_user']->value,'in'=>$_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>
</div>
        </div>
      </div>
  </div></div>
  
  <div class="project_home_right" id="project_details">
    <div class="project_home_right_inner">
    
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('object', array('object'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value)); $_block_repeat=true; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    	<?php if (AngieApplication::isModuleLoaded(@TASKS_MODULE)){?>
      	<?php if (Tasks::canAccess($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>	
       		<div id="project_progress"><?php echo smarty_function_project_progress(array('project'=>$_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>
</div>
       	<?php }?>
      <?php }?>
     	 
    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_object(array('object'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    
    <?php if (is_foreachable($_smarty_tpl->tpl_vars['home_sidebars']->value)){?>
      <?php  $_smarty_tpl->tpl_vars['home_sidebar'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['home_sidebar']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['home_sidebars']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['home_sidebar']->key => $_smarty_tpl->tpl_vars['home_sidebar']->value){
$_smarty_tpl->tpl_vars['home_sidebar']->_loop = true;
?>
        <?php if ($_smarty_tpl->tpl_vars['home_sidebar']->value['body']){?>
          <div class="project_overview_box <?php if (!$_smarty_tpl->tpl_vars['home_sidebar']->value['is_important']){?>alt<?php }?>" id="<?php echo clean($_smarty_tpl->tpl_vars['home_sidebar']->value['id'],$_smarty_tpl);?>
">
            <div class="project_overview_box_title">
              <h2><?php echo clean($_smarty_tpl->tpl_vars['home_sidebar']->value['label'],$_smarty_tpl);?>
</h2>
            </div>
            <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
              <?php echo $_smarty_tpl->tpl_vars['home_sidebar']->value['body'];?>

            </div></div>
          </div>
        <?php }?>
      <?php } ?>
    <?php }?>
    
  </div></div>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('project_updated.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, project) {
    if (project['class'] == 'Project' && project.id == '<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getId(),$_smarty_tpl);?>
') {
      var wrapper = $('#project_home');
      var logo_image = wrapper.find('#select_project_icon img:first');
      logo_image.attr('src', project.avatar.large);
      App.clean($('#wireframe_page_tabs').find('#page_tabs li.first a').html(App.excerpt(project['name'], 25)));
    } // if
  });

  App.Wireframe.Events.bind('project_completed.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
 project_opened.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, project) {
    var project_status = $('#project_details').find('.meta_status .meta_data');
    if(project.is_completed) {
      project_status.html(App.lang('Completed'));
    } else {
    	project_status.html(App.lang('Active'));
    } // if
  });

  App.Wireframe.Events.bind('create_invoice_from_project.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, invoice) {
    if (invoice['class'] == 'Invoice') {
    	App.Wireframe.Flash.success(App.lang('New invoice created.'));
    	App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
    } // if
  });

  App.Wireframe.Events.bind('project_settings_updated.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, project_settings) {
    // refresh the project tabs
    var project = project_settings.project;
    if (project) {

      // trigger project updated
      App.Wireframe.Events.trigger('project_updated', project);

      // if we are currently in right project, update it's tabs  
	    if (project.id == <?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getId(),$_smarty_tpl);?>
) {
        var settings = project_settings.settings;
        if (settings) {
          var tabs = settings.tabs;
          if (tabs) {
            App.Wireframe.PageTabs.batchSet(tabs);
            App.Wireframe.PageTabs.setAsCurrent('overview');
          } // if
        } // if	      
	    } // if    
    }
  });

  var wrapper = $('#project_at_a_glance');
  var project_id = <?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getId(),$_smarty_tpl);?>
;
  var project_state = <?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getState(),$_smarty_tpl);?>
;
  var projects_url = '<?php echo smarty_function_assemble(array('route'=>'projects'),$_smarty_tpl);?>
';

  App.Wireframe.Events.bind('project_deleted.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, project) {
    if (project['class'] == 'Project' && project['id'] == project_id) {
      if (!wrapper.parents('objects_list').length) {
        if (project.state == 1) {
          App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
        } else if (project.state == 0) {
          App.Wireframe.Content.setFromUrl(projects_url);
        } // if
      } // if
      project_state = project['state'];
    } // if
  });
  
</script><?php }} ?>