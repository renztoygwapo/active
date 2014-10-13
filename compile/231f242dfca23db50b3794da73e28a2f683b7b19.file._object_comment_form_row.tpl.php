<?php /* Smarty version Smarty-3.1.12, created on 2014-06-26 22:05:59
         compiled from "/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/comments/views/default/_object_comment_form_row.tpl" */ ?>
<?php /*%%SmartyHeaderCode:57359034453ac9947206429-07388042%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '231f242dfca23db50b3794da73e28a2f683b7b19' => 
    array (
      0 => '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/comments/views/default/_object_comment_form_row.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '57359034453ac9947206429-07388042',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'comment' => 0,
    'comments_id' => 0,
    'user' => 0,
    'comment_parent' => 0,
    'comment_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53ac99473919b4_33189558',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ac99473919b4_33189558')) {function content_53ac99473919b4_33189558($_smarty_tpl) {?><?php if (!is_callable('smarty_block_wrap')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.wrap.php';
if (!is_callable('smarty_function_text_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/function.text_field.php';
if (!is_callable('smarty_block_editor_field')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/visual_editor/helpers/block.editor_field.php';
if (!is_callable('smarty_function_select_attachments')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/attachments/helpers/function.select_attachments.php';
if (!is_callable('smarty_block_submit')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/environment/helpers/block.submit.php';
if (!is_callable('smarty_block_lang')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/globalization/helpers/block.lang.php';
if (!is_callable('smarty_function_select_completion_status')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/complete/helpers/function.select_completion_status.php';
if (!is_callable('smarty_function_select_assignee')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/assignees/helpers/function.select_assignee.php';
if (!is_callable('smarty_function_select_label')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/labels/helpers/function.select_label.php';
if (!is_callable('smarty_function_select_category')) include '/home/abag/public_html/dev/activecollab/4.2.6/angie/frameworks/categories/helpers/function.select_category.php';
?><?php if ($_smarty_tpl->tpl_vars['comment']->value->isLoaded()){?>
<div class="comment edit_comment quick_comment_form" id="<?php echo clean($_smarty_tpl->tpl_vars['comments_id']->value,$_smarty_tpl);?>
" style="display: none">
<?php }else{ ?>
<div class="comment new_comment quick_comment_form" id="<?php echo clean($_smarty_tpl->tpl_vars['comments_id']->value,$_smarty_tpl);?>
" style="display: none">
<?php }?>

  <div class="comment_avatar_container">
		<?php if ($_smarty_tpl->tpl_vars['comment']->value->isLoaded()){?>
	    <span style="background-image: url(<?php echo clean($_smarty_tpl->tpl_vars['comment']->value->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
);" class="avatar">
	      <img src="<?php echo clean($_smarty_tpl->tpl_vars['comment']->value->getCreatedBy()->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
" alt="avatar" />
	    </span>
	  <?php }else{ ?>
	    <span style="background-image: url(<?php echo clean($_smarty_tpl->tpl_vars['user']->value->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
);" class="avatar">
	      <img src="<?php echo clean($_smarty_tpl->tpl_vars['user']->value->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG),$_smarty_tpl);?>
" alt="avatar" />
	    </span>
	  <?php }?>
  </div>

  <div class="comment_content_container">
    <div class="body">
    <?php if ($_smarty_tpl->tpl_vars['comment']->value->isLoaded()){?>
      <form action="<?php echo clean($_smarty_tpl->tpl_vars['comment']->value->getEditUrl(),$_smarty_tpl);?>
" method="post" enctype="multipart/form-data">
    <?php }else{ ?>
    	<form action="<?php echo clean($_smarty_tpl->tpl_vars['comment_parent']->value->comments()->getPostUrl(),$_smarty_tpl);?>
" method="post" enctype="multipart/form-data">
  	  <?php if ($_smarty_tpl->tpl_vars['user']->value instanceof AnonymousUser){?>
  	    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'created_by_name')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'created_by_name'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  	      <?php echo smarty_function_text_field(array('name'=>"comment[created_by_name]",'value'=>$_smarty_tpl->tpl_vars['comment_data']->value['created_by_name'],'label'=>"Your Name",'required'=>true),$_smarty_tpl);?>

  	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'created_by_name'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  	    
  	    <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'created_by_email')); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'created_by_email'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

  	      <?php echo smarty_function_text_field(array('name'=>"comment[created_by_email]",'value'=>$_smarty_tpl->tpl_vars['comment_data']->value['created_by_email'],'label'=>"Your Email",'required'=>true),$_smarty_tpl);?>

  	    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'created_by_email'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

  	  <?php }?>
    <?php }?>
    
        <input type="hidden" name="submitted" value="submitted" />
        
        <div class="expandable_editor">
          <div class="real_textarea">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'body','class'=>"comment_visual_editor")); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'body','class'=>"comment_visual_editor"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php $_smarty_tpl->smarty->_tag_stack[] = array('editor_field', array('name'=>'comment[body]','id'=>((string)$_smarty_tpl->tpl_vars['comments_id']->value)."_comment_body",'resize'=>"true",'label'=>"Your Comment",'required'=>true,'object'=>$_smarty_tpl->tpl_vars['comment_parent']->value,'headings_enabled'=>false)); $_block_repeat=true; echo smarty_block_editor_field(array('name'=>'comment[body]','id'=>((string)$_smarty_tpl->tpl_vars['comments_id']->value)."_comment_body",'resize'=>"true",'label'=>"Your Comment",'required'=>true,'object'=>$_smarty_tpl->tpl_vars['comment_parent']->value,'headings_enabled'=>false), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo $_smarty_tpl->tpl_vars['comment_data']->value['body'];?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_editor_field(array('name'=>'comment[body]','id'=>((string)$_smarty_tpl->tpl_vars['comments_id']->value)."_comment_body",'resize'=>"true",'label'=>"Your Comment",'required'=>true,'object'=>$_smarty_tpl->tpl_vars['comment_parent']->value,'headings_enabled'=>false), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'body','class'=>"comment_visual_editor"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('wrap', array('field'=>'attachments','class'=>"attachments_field_wrapper")); $_block_repeat=true; echo smarty_block_wrap(array('field'=>'attachments','class'=>"attachments_field_wrapper"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

              <?php echo smarty_function_select_attachments(array('name'=>"comment[attachments]",'object'=>$_smarty_tpl->tpl_vars['comment']->value,'user'=>$_smarty_tpl->tpl_vars['user']->value),$_smarty_tpl);?>

            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_wrap(array('field'=>'attachments','class'=>"attachments_field_wrapper"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            
            <div class="comment_subscribers"></div>
            
            <div class="comment_form_buttons_wrapper button_holder">
              <?php if ($_smarty_tpl->tpl_vars['comment']->value->isNew()){?>
	              <div class="comment_form_main_buttons">
                  <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Comment<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <a href="#" class="comment_cancel"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
	              </div>
	              
	              <div class="comment_form_additional_buttons">
		              <?php if (($_smarty_tpl->tpl_vars['comment_parent']->value->canEdit($_smarty_tpl->tpl_vars['user']->value)&&(($_smarty_tpl->tpl_vars['comment_parent']->value instanceof IComplete&&$_smarty_tpl->tpl_vars['comment_parent']->value->complete()->canChangeStatus($_smarty_tpl->tpl_vars['user']->value))||$_smarty_tpl->tpl_vars['comment_parent']->value instanceof ILabel||$_smarty_tpl->tpl_vars['comment_parent']->value instanceof ICategory))){?>
                    <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('type'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getVerboseType(true,$_smarty_tpl->tpl_vars['user']->value->getLanguage()))); $_block_repeat=true; echo smarty_block_lang(array('type'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getVerboseType(true,$_smarty_tpl->tpl_vars['user']->value->getLanguage())), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
also, update :type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('type'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getVerboseType(true,$_smarty_tpl->tpl_vars['user']->value->getLanguage())), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			              <?php if ($_smarty_tpl->tpl_vars['comment_parent']->value instanceof IComplete&&$_smarty_tpl->tpl_vars['comment_parent']->value->complete()->canChangeStatus($_smarty_tpl->tpl_vars['user']->value)){?>
			                <?php echo smarty_function_select_completion_status(array('name'=>"parent[is_completed]",'label'=>"Status",'label_type'=>"inner",'value'=>$_smarty_tpl->tpl_vars['comment_parent']->value->complete()->isCompleted(),'id'=>'parent_completion'),$_smarty_tpl);?>
<span>|</span>
                      <?php echo smarty_function_select_assignee(array('name'=>"parent[assignee_id]",'parent'=>$_smarty_tpl->tpl_vars['comment_parent']->value,'user'=>$_smarty_tpl->tpl_vars['user']->value,'value'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getAssigneeId(),'id'=>"parent_assignee_id"),$_smarty_tpl);?>
<span>|</span>
			              <?php }?>
			              <?php if ($_smarty_tpl->tpl_vars['comment_parent']->value instanceof ILabel&&$_smarty_tpl->tpl_vars['comment_parent']->value->canEdit($_smarty_tpl->tpl_vars['user']->value)){?>
			                <?php echo smarty_function_select_label(array('name'=>"parent[label_id]",'type'=>$_smarty_tpl->tpl_vars['comment_parent']->value->label()->getLabelType(),'user'=>$_smarty_tpl->tpl_vars['user']->value,'label'=>'Label','label_type'=>"inner",'value'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getLabelId(),'id'=>'parent_label_id'),$_smarty_tpl);?>
<span>|</span>
			              <?php }?>
			              <?php if ($_smarty_tpl->tpl_vars['comment_parent']->value instanceof ICategory&&$_smarty_tpl->tpl_vars['comment_parent']->value->canEdit($_smarty_tpl->tpl_vars['user']->value)){?>
			                <?php echo smarty_function_select_category(array('name'=>'parent[category_id]','parent'=>$_smarty_tpl->tpl_vars['comment_parent']->value->category()->getCategoryContext(),'type'=>$_smarty_tpl->tpl_vars['comment_parent']->value->category()->getCategoryClass(),'user'=>$_smarty_tpl->tpl_vars['user']->value,'label'=>'Category','label_type'=>"inner",'success_event'=>"category_created",'value'=>$_smarty_tpl->tpl_vars['comment_parent']->value->getCategoryId(),'id'=>"parent_category_id"),$_smarty_tpl);?>

			              <?php }?>
		              <?php }?>
	              </div>
              <?php }else{ ?>
                <?php $_smarty_tpl->smarty->_tag_stack[] = array('submit', array()); $_block_repeat=true; echo smarty_block_submit(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Save Changes<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_submit(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
or<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 <a href="#" class="comment_cancel"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
              <?php }?>
            </div>
          </div>
        </div>
        
        
        
        
      </form>
    </div>
  </div>
</div><?php }} ?>