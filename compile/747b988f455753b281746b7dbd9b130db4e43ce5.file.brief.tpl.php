<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 21:51:59
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/brief.tpl" */ ?>
<?php /*%%SmartyHeaderCode:92249987053ac95ffe2f123-09143878%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '747b988f455753b281746b7dbd9b130db4e43ce5' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/views/default/project/brief.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '92249987053ac95ffe2f123-09143878',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'active_project' => 0,
    'logged_user' => 0,
    'field_name' => 0,
    'details' => 0,
    'project_brief_stats' => 0,
    'project_brief_stat' => 0,
    'project_people' => 0,
    'project_person' => 0,
    'user_tasks_url' => 0,
    'user_subscriptions_url' => 0,
    'ical_subscribe_url' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac9600513995_09986775',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac9600513995_09986775')) {function content_53ac9600513995_09986775($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.add_bread_crumb.php';
if (!is_callable('smarty_function_image_url')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.image_url.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_project_progress')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.project_progress.php';
if (!is_callable('smarty_modifier_rich_text')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.rich_text.php';
if (!is_callable('smarty_function_user_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/authentication/helpers/function.user_link.php';
if (!is_callable('smarty_function_project_budget')) include '/home/abag/public_html/dev/activecollab/4.2.6/modules/system/helpers/function.project_budget.php';
if (!is_callable('smarty_function_object_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.object_label.php';
if (!is_callable('smarty_function_assemble')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.assemble.php';
if (!is_callable('smarty_block_assign_var')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.assign_var.php';
if (!is_callable('smarty_block_link')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.link.php';
if (!is_callable('smarty_modifier_json')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/modifier.json.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array('lang'=>false)); $_block_repeat=true; echo smarty_block_title(array('lang'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['active_project']->value->getName();?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array('lang'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
At a Glance<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="project_at_a_glance">

  <div id="project_at_a_glance_card" class="<?php if ($_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_ARCHIVED||$_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_TRASHED){?>with_warning<?php }?>">
	  <div class="project_at_a_glance_header">
      <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_ARCHIVED){?>
        <div class="project_brief_warning"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/16x16/archive.png",'module'=>"environment"),$_smarty_tpl);?>
" /><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This project is archived<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
        <?php }elseif($_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_TRASHED){?>
        <div class="project_brief_warning"><img src="<?php echo smarty_function_image_url(array('name'=>"icons/16x16/empty-trash.png",'module'=>"environment"),$_smarty_tpl);?>
" /><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
This project is in trash<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
      <?php }?>

      <table>
	      <tr>
	        <td class="logo">
	          <a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getViewUrl(),$_smarty_tpl);?>
"><img src="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
" alt="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getName(),$_smarty_tpl);?>
" /></a>
	        </td>
	        <td class="main">
	          <h2><a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getViewUrl(),$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getName(),$_smarty_tpl);?>
</a></h2>
	        </td>
	        <?php if (AngieApplication::isModuleLoaded(@TASKS_MODULE)){?>
  	        <?php if (Tasks::canAccess($_smarty_tpl->tpl_vars['logged_user']->value,$_smarty_tpl->tpl_vars['active_project']->value)){?>
            	<td class="progress"><?php echo smarty_function_project_progress(array('project'=>$_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>
</td>
            <?php }?>
          <?php }?>
	      </tr>
		  </table>
	  </div>

    <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getOverview()){?>
    <div class="project_at_a_glance_body">
      <div class="formatted_content"><?php echo smarty_modifier_rich_text($_smarty_tpl->tpl_vars['active_project']->value->getOverview());?>
</div>
    </div>
    <?php }?>
    
    <table class="project_at_a_glance_other">
      <tr>
        <td>
			    <div class="project_at_a_glance_meta">
			      <dl>
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Leader<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><?php echo smarty_function_user_link(array('user'=>$_smarty_tpl->tpl_vars['active_project']->value->getLeader()),$_smarty_tpl);?>
</dd>
			        
			        <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getCompany() instanceof Company){?>
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Client<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getCompany()->getViewUrl(),$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getCompany()->getName(),$_smarty_tpl);?>
</a></dd>
			        <?php }?>
			        
			        <?php if ($_smarty_tpl->tpl_vars['logged_user']->value->canSeeProjectBudgets()&&$_smarty_tpl->tpl_vars['active_project']->value->getBudget()){?>
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Budget<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><?php echo smarty_function_project_budget(array('project'=>$_smarty_tpl->tpl_vars['active_project']->value,'user'=>$_smarty_tpl->tpl_vars['logged_user']->value),$_smarty_tpl);?>
</dd>
			        <?php }?>
			        
			        <?php if ($_smarty_tpl->tpl_vars['active_project']->value->category()->get() instanceof ProjectCategory){?>
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->category()->get()->getName(),$_smarty_tpl);?>
</dd>
			        <?php }?>
			        
			        <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getBasedOn() instanceof ProjectRequest){?>
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Based On<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><a href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getBasedOn()->getViewUrl(),$_smarty_tpl);?>
" class="quick_view_item"><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getBasedOn()->getName(),$_smarty_tpl);?>
</a></dd>
			        <?php }?>
			        
			        <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
			        <dd><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getVerboseStatus(),$_smarty_tpl);?>
</dd>
			        
			        
			        <?php if ($_smarty_tpl->tpl_vars['active_project']->value->label()->get() instanceof ProjectLabel){?>
				      <dt><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Label<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</dt>
				      <dd><?php echo smarty_function_object_label(array('object'=>$_smarty_tpl->tpl_vars['active_project']->value),$_smarty_tpl);?>
</dd>
			        <?php }?>

            <?php  $_smarty_tpl->tpl_vars['details'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['details']->_loop = false;
 $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable;
 $_from = CustomFields::getEnabledCustomFieldsByType('Project'); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['details']->key => $_smarty_tpl->tpl_vars['details']->value){
$_smarty_tpl->tpl_vars['details']->_loop = true;
 $_smarty_tpl->tpl_vars['field_name']->value = $_smarty_tpl->tpl_vars['details']->key;
?>
              <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getFieldValue($_smarty_tpl->tpl_vars['field_name']->value)){?>
                <dt><?php echo clean($_smarty_tpl->tpl_vars['details']->value['label'],$_smarty_tpl);?>
:</dt>
                <dd><?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getFieldValue($_smarty_tpl->tpl_vars['field_name']->value),$_smarty_tpl);?>
</dd>
              <?php }?>
            <?php } ?>
			      </dl>
			    </div>
        </td>
        <td>
			    <?php if (is_foreachable($_smarty_tpl->tpl_vars['project_brief_stats']->value)){?>    
			    <div class="project_at_a_glance_details">
			      <ul>
			        <?php  $_smarty_tpl->tpl_vars['project_brief_stat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['project_brief_stat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['project_brief_stats']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['project_brief_stat']->key => $_smarty_tpl->tpl_vars['project_brief_stat']->value){
$_smarty_tpl->tpl_vars['project_brief_stat']->_loop = true;
?>
			          <li><?php echo $_smarty_tpl->tpl_vars['project_brief_stat']->value;?>
</li>
			        <?php } ?>
			      </ul>
			    </div>
			    <?php }?>
        </td>
      </tr>
    </table>

    <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getState()>@STATE_ARCHIVED){?>
      <div class="project_at_a_glance_people">
        <?php $_smarty_tpl->tpl_vars['project_people'] = new Smarty_variable($_smarty_tpl->tpl_vars['active_project']->value->users()->get($_smarty_tpl->tpl_vars['logged_user']->value,@STATE_VISIBLE), null, 0);?>
        <?php if (is_foreachable($_smarty_tpl->tpl_vars['project_people']->value)){?>
          <ul>
            <?php  $_smarty_tpl->tpl_vars['project_person'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['project_person']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['project_people']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['project_person']->key => $_smarty_tpl->tpl_vars['project_person']->value){
$_smarty_tpl->tpl_vars['project_person']->_loop = true;
?>
              <li><a href="<?php echo clean($_smarty_tpl->tpl_vars['project_person']->value->getViewUrl(),$_smarty_tpl);?>
" title="<?php echo clean($_smarty_tpl->tpl_vars['project_person']->value->getDisplayName(),$_smarty_tpl);?>
"><img src="<?php echo clean($_smarty_tpl->tpl_vars['project_person']->value->avatar()->getUrl(),$_smarty_tpl);?>
" /></a></li>
            <?php } ?>
          </ul>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['active_project']->value->canManagePeople($_smarty_tpl->tpl_vars['logged_user']->value)){?>
            <div class="project_at_a_glance_people_manage_people">
                <a href="<?php echo smarty_function_assemble(array('route'=>'project_people','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Manage People on this Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
            </div>
        <?php }?>
      </div>
    <?php }?>

    <div class="section_button_wrapper brief_action_button">
      <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_ARCHIVED){?>
        <a class="section_button" href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->state()->getUnarchiveUrl(),$_smarty_tpl);?>
" id="project_brief_unarchive_project"><span><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/restore-from-archive.png','module'=>'environment'),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Restore From Archive<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
      <?php }elseif($_smarty_tpl->tpl_vars['active_project']->value->getState()==@STATE_TRASHED){?>
        <a class="section_button" href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->state()->getUntrashUrl(),$_smarty_tpl);?>
" id="project_brief_untrash_project"><span><img src="<?php echo smarty_function_image_url(array('name'=>'icons/12x12/restore-from-trash.png','module'=>'environment'),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Restore From Trash<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
      <?php }else{ ?>
        <a class="section_button" href="<?php echo clean($_smarty_tpl->tpl_vars['active_project']->value->getViewUrl(),$_smarty_tpl);?>
"><span><img src="<?php echo smarty_function_image_url(array('name'=>'icons/16x16/go-to-project.png','module'=>'environment'),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Go to the Project<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>
      <?php }?>
    </div>
  </div>

  <?php if ($_smarty_tpl->tpl_vars['active_project']->value->getState()!=@STATE_TRASHED){?>
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'user_tasks_url')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'user_tasks_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_function_assemble(array('route'=>'project_user_tasks','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'user_tasks_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'user_subscriptions_url')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'user_subscriptions_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_function_assemble(array('route'=>'project_user_subscriptions','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'user_subscriptions_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    <ul class="project_at_glance_links">
      <li id="show_me_assignments"><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['user_tasks_url']->value)); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['user_tasks_url']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Assignments<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['user_tasks_url']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
      <li id="show_me_subscriptions"><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['user_subscriptions_url']->value)); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['user_subscriptions_url']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
My Subscriptions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['user_subscriptions_url']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
      <?php if ($_smarty_tpl->tpl_vars['logged_user']->value->isFeedUser()){?>
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('assign_var', array('name'=>'ical_subscribe_url')); $_block_repeat=true; echo smarty_block_assign_var(array('name'=>'ical_subscribe_url'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smarty_function_assemble(array('route'=>'project_ical_subscribe','project_slug'=>$_smarty_tpl->tpl_vars['active_project']->value->getSlug()),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_assign_var(array('name'=>'ical_subscribe_url'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

      <li id="show_me_ical"><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['ical_subscribe_url']->value)); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['ical_subscribe_url']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
iCalendar Feed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['ical_subscribe_url']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
      <?php if ($_smarty_tpl->tpl_vars['logged_user']->value->isFeedUser()){?>
      <li id="show_me_rss"><?php $_smarty_tpl->smarty->_tag_stack[] = array('link', array('href'=>$_smarty_tpl->tpl_vars['active_project']->value->getRssUrl($_smarty_tpl->tpl_vars['logged_user']->value),'target'=>'_blank')); $_block_repeat=true; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['active_project']->value->getRssUrl($_smarty_tpl->tpl_vars['logged_user']->value),'target'=>'_blank'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
RSS Feed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_link(array('href'=>$_smarty_tpl->tpl_vars['active_project']->value->getRssUrl($_smarty_tpl->tpl_vars['logged_user']->value),'target'=>'_blank'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</li>
      <?php }?>
      <?php }?>
    </ul>
  <?php }?>
</div>

<script type="text/javascript">
(function() {
  var wrapper = $('#project_at_a_glance');
  var project_id = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getId());?>
;
  var project_state = <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['active_project']->value->getState());?>
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

  App.Wireframe.Events.bind('project_updated.<?php echo clean($_smarty_tpl->tpl_vars['request']->value->getEventScope(),$_smarty_tpl);?>
', function (event, project) {
	  if (project['class'] == 'Project' && project['id'] == project_id) {
      // update logo
	    var logo_image = wrapper.find('#select_project_icon img:first');
	    logo_image.attr('src', project.avatar.large);

      // if project is untrashed
      if (project_state == 1 && project['state'] != 1) {
        App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
      } // if

      if (project_state == 2 && project['state'] > 2) {
        App.Wireframe.Content.setFromUrl(project['urls']['view'], true);
      } // if
      project_state = project['state'];
	  } // if
  });

  $('#page_action_restore_from_trash a, #project_brief_untrash_project').asyncLink({
    'confirmation'    : App.lang('Are you sure that you want to restore this :object_type from trash?', {
      'object_type' : App.lang('project')
    }),
    'success_message' : App.lang(':object_type has been successfully restored from trash', {
      'object_type' : App.lang('project')
    }),
    'success_event'   : 'project_updated'
  });

  $('#project_brief_unarchive_project').asyncLink({
    'confirmation'    : App.lang('Are you sure that you want to restore this :object_type from archive?', {
      'object_type' : App.lang('project')
    }),
    'success_message' : App.lang(':object_type has been successfully restored from archive', {
      'object_type' : App.lang('project')
    }),
    'success_event'   : 'project_updated'
  })

})();
</script><?php }} ?>