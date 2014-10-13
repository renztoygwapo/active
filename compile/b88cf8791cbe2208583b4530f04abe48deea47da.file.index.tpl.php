<?php /* Smarty version Smarty-3.1.12, created on 2014-10-04 12:02:09
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\angie\frameworks\help\views\default\fw_help_whats_new\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30137542fe1c1c6ec25-99541006%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b88cf8791cbe2208583b4530f04abe48deea47da' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\angie\\frameworks\\help\\views\\default\\fw_help_whats_new\\index.tpl',
      1 => 1403109851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30137542fe1c1c6ec25-99541006',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'articles_by_version' => 0,
    'version' => 0,
    'version_articles' => 0,
    'selected_article' => 0,
    'article' => 0,
    'articles' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542fe1c2e286a1_99254498',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542fe1c2e286a1_99254498')) {function content_542fe1c2e286a1_99254498($_smarty_tpl) {?><?php if (!is_callable('smarty_block_title')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.title.php';
if (!is_callable('smarty_block_add_bread_crumb')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\block.add_bread_crumb.php';
if (!is_callable('smarty_function_replace')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.replace.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('title', array()); $_block_repeat=true; echo smarty_block_title(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New and Newsworthy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_title(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('add_bread_crumb', array()); $_block_repeat=true; echo smarty_block_add_bread_crumb(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New and Newsworthy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_add_bread_crumb(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="help_whats_new" class="help_content_page">
  <div id="help_whats_new_articles_list" class="help_content_page_sidebar">
  <?php  $_smarty_tpl->tpl_vars['version_articles'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['version_articles']->_loop = false;
 $_smarty_tpl->tpl_vars['version'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['articles_by_version']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['version_articles']->key => $_smarty_tpl->tpl_vars['version_articles']->value){
$_smarty_tpl->tpl_vars['version_articles']->_loop = true;
 $_smarty_tpl->tpl_vars['version']->value = $_smarty_tpl->tpl_vars['version_articles']->key;
?>
    <p><?php echo clean($_smarty_tpl->tpl_vars['version']->value,$_smarty_tpl);?>
</p>
    <ol>
    <?php  $_smarty_tpl->tpl_vars['article'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['article']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['version_articles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['article']->key => $_smarty_tpl->tpl_vars['article']->value){
$_smarty_tpl->tpl_vars['article']->_loop = true;
?>
      <li <?php if ($_smarty_tpl->tpl_vars['selected_article']->value&&$_smarty_tpl->tpl_vars['selected_article']->value==$_smarty_tpl->tpl_vars['article']->value->getSlug()){?>class="selected"<?php }?>><a href="<?php echo clean($_smarty_tpl->tpl_vars['article']->value->getUrl(),$_smarty_tpl);?>
" data-short-name="<?php echo smarty_function_replace(array('search'=>'-','in'=>$_smarty_tpl->tpl_vars['article']->value->getShortName(),'replacement'=>'_'),$_smarty_tpl);?>
"><?php echo clean($_smarty_tpl->tpl_vars['article']->value->getTitle(),$_smarty_tpl);?>
</a><?php if (AngieApplication::help()->isNewSinceLastUpgrade($_smarty_tpl->tpl_vars['article']->value)){?> <span class="new_since_last_upgrade" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New Since Your Last Upgrade<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span><?php }?></li>
    <?php } ?>
    </ol>
  <?php } ?>
  </div>

  <div id="help_whats_new_articles" class="help_content_page_content">
    <div id="help_whats_new_articles_content" class="help_content_page_content_wrapper">
      <?php  $_smarty_tpl->tpl_vars['article'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['article']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['articles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['article']->key => $_smarty_tpl->tpl_vars['article']->value){
$_smarty_tpl->tpl_vars['article']->_loop = true;
?>
        <div class="help_whats_new_article help_element" data-short-name="<?php echo smarty_function_replace(array('search'=>'-','in'=>$_smarty_tpl->tpl_vars['article']->value->getShortName(),'replacement'=>'_'),$_smarty_tpl);?>
" style="display: none">
          <div class="help_new_in"><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array('version'=>$_smarty_tpl->tpl_vars['article']->value->getVersionNumber())); $_block_repeat=true; echo smarty_block_lang(array('version'=>$_smarty_tpl->tpl_vars['article']->value->getVersionNumber()), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New in v:version<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array('version'=>$_smarty_tpl->tpl_vars['article']->value->getVersionNumber()), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
          <h1><?php echo clean($_smarty_tpl->tpl_vars['article']->value->getTitle(),$_smarty_tpl);?>
</h1>
          <div class="help_whats_new_article_content help_element_content"><?php echo AngieApplication::help()->renderBody($_smarty_tpl->tpl_vars['article']->value);?>
</div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  var wrapper = $('#help_whats_new');
  var page_title = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
New and Newsworthy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';

  /**
   * Show article
   *
   * @param String short_name
   */
  var do_show_article = function (short_name) {
    var page = wrapper.find('#help_whats_new_articles div.help_whats_new_article[data-short-name=' + short_name + ']');
    var link = wrapper.find('#help_whats_new_articles_list a[data-short-name=' + short_name + ']');

    if(page.length > 0) {
      var selected_article = wrapper.find('div.help_whats_new_article:visible');

      if(selected_article.length > 0) {
        if(selected_article.data('shortName') == page.data('shortName')) {
          return false;
        } // if

        wrapper.find('#help_whats_new_articles_list li').removeClass('selected');
        link.parent().addClass('selected');

        $('#help_whats_new_articles').scrollTo(0, 200, {
          'onAfter' : function() {
            selected_article.fadeOut({
              'complete' : function() {
                page.fadeIn();
              }
            });
          }
        });
      } else {
        page.show();
        link.parent().addClass('selected');
      } // if
    } // if
  };

  // Handle history requests
  App.Wireframe.Events.bind('history_state_changed.content', function (event) {
    var state = History.getState();
    var handler = state.data['handler'];
    var handler_id = state.data['handler_id'];

    // this is not handler we're looking for
    if (handler != 'whats_new') {
      return true;
    } // if

    App.Wireframe.PageTitle.set(page_title);
    do_show_article(state.data['additional'] ? state.data['additional']['page_short_name'] : null);

    History.handled = true;
    return false;
  });

  wrapper.each(function() {
    wrapper.on('click', '#help_whats_new_articles_list a', function(event, is_initial) {
      var anchor = $(this);

      // push history state
      History.pushState({
        'handler' : 'whats_new',
        'additional' : {
          'page_short_name'  : anchor.attr('data-short-name')
        }
      }, App.lang('Loading'), anchor.attr('href'));

      return false;
    });

    // Open first article
    var selected_item = wrapper.find('#help_whats_new_articles_list li.selected a');

    if(selected_item.length > 0) {
      do_show_article(selected_item.attr('data-short-name'));
    } else {
      wrapper.find('#help_whats_new_articles_list li:first a:first').each(function() {
        do_show_article($(this).attr('data-short-name'));
      });
    } // if
  });
</script><?php }} ?>