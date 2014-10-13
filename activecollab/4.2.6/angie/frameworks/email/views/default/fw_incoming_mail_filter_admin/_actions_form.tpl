{use_widget name="add_edit_incoming_mailbox_form" module="email"}
{use_widget name="select_named_object" module="environment"}
{use_widget name="select_assignees" module="assignees"}
{use_widget name="select_project_object_by_type" module="email"}
{use_widget name="select_users_inline" module="authentication"}
{use_widget name="select_subscribers" module="subscriptions"}


{if is_foreachable($incoming_mail_actions)}
	{assign var=num value=1}
	{foreach from=$incoming_mail_actions key=action_name item=action}
	  {if $action->getCanUse()}
	    {wrap field=$action_name}	
    	    <input type="radio" class="show_action_tpl" name="filter[action_name]" value="{$action->getActionClassName()}" id="action_form_{$num}" for_id={$action->getActionClassName()} {if ($action->getPreSelected() && !$filter_data) || ($filter_data.action_name == $action->getActionClassName())}checked=checked{/if}></input>
    	    {label style="float:left;" for="action_form_$num" after_text=""}{$action->getName()}{/label} 
    	    <p class="description">&nbsp; &mdash; {$action->getDescription()}</p>
    	    {if $action->getTemplateName()}
        	    <div id="{$action->getActionClassName()}" class="action_form_box" >	
        	    
            	    {if $filter_data}
            	    	{if $filter_data.action_name == $action->getActionClassName()}
            	    		{$action_forms[$action->getActionClassName()] nofilter}
            	    	{/if}
            	    {else}
                	    {if $action->getPreSelected()}
                	    	{$action_forms[$action->getActionClassName()] nofilter}
                	    {/if} 
            	    {/if}
        	        
        		</div>
    		{/if}
    	{/wrap}
	  {/if}
	  {assign var=num value=$num+1}
	{/foreach}
{else}
	<p class="empty_page"><span class="inner">{lang}There are no actions defined.{/lang}</span></p>
{/if}

<script type="text/javascript">

	var action_form_box = $(".action_form_box");
	var to_url = '{$to_url}';

  if (to_url) {
    // for new filter - render project elements
    if(action_form_box.children().length == 1) {
      var action_class_name = $("input[type=radio].show_action_tpl:checked").val();
      App.widgets.RenderProjectElement.init(action_form_box.children().attr('id'),{$active_filter->getId()|json nofilter}, action_class_name, to_url);
    }//if
  } //if

  var template = {$action_forms|json nofilter};

  //actions - load template into appropriate div
  var action_radio = $("input[type=radio].show_action_tpl");

  action_radio.change(function () {
    var object = $(this);
    var action_class_name = object.val();
    var action_form  = $("#" + object.attr('for_id'));
    if(object.attr('checked')) {
      action_form_box.children().remove();
      if(action_form.length > 0) {
        action_form.append(template[action_form.attr('id')]);
        if (to_url) {
          var wrapper = $(template[action_form.attr('id')]).attr('id');
          App.widgets.RenderProjectElement.init(wrapper,{$active_filter->getId()|json nofilter}, action_class_name, to_url);
        }
      }//if
    }//if
  });

	

</script>
