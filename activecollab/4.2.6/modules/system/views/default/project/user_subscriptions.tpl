{title}My Project Subscriptions{/title}
{add_bread_crumb}List My Subscriptions{/add_bread_crumb}

<div id="subscriptions">
  <div id="subscriptions_list">
  {if is_foreachable($subscriptions)}
    <table class="common" cellspacing="0">
      <thead>
	      <tr>
	        <th class="name">{lang}Object{/lang}</th>
          <th class="author">{lang}Created{/lang}</th>
          <th class="checkbox">{lang}Unsubscribe{/lang}</th>
	      </tr>
      </thead>
      <tbody>
        {include file=get_view_path('_user_subscriptions_loop', 'project', $smarty.const.SYSTEM_MODULE)}
      </tbody>
      <tfoot>
        <tr class="button">
        <td colspan="3" class="action">
          {wrap_buttons}
            <a href="{$show_more_results_url}" id="more_results_button">{lang}Show More Results{/lang}</a>
            <button id="mass_unsubscribe_button" async_url="{$mass_unsubscribe_url}" class="default">{lang}Unsubscribe{/lang}</button>
          {/wrap_buttons}
        </td>
      </tr>
      </tfoot>
    </table>
  {else}
    <p class="empty_page"><span class="inner">{lang}You do not have any subscriptions in this project{/lang}</span></p>
  {/if}
  </div>
</div>

<script type="text/javascript">
  var wrapper = $('#subscriptions_list table tbody');
  var button_unsubscribe = $('#mass_unsubscribe_button');
  var button_more_results = $('#more_results_button');
  var offset = 0;

  button_more_results.click(function() {
    button_more_results.hide().after('<img src="' + App.Wireframe.Utils.indicatorUrl()  + '" alt="" />');    
    offset += 1;

    $.ajax({
      'url' : App.extendUrl(button_more_results.attr('href'), {
        'offset' : offset
      }),
      'type' : 'get',
      'success' : function (response) {
	      if (response == 'empty') {
		  	  App.Wireframe.Flash.success(App.lang('There are no more results to load'));
		    } else {
		  	  wrapper.append(response);
		    } //if
		  	button_more_results.parent().find('img').remove();
		  	button_more_results.show();
      } 
    });

    return false;
  });

   
  button_unsubscribe.click(function() {
	  $(this).hide();
    $(this).after('<img src="' + App.Wireframe.Utils.indicatorUrl()  + '" alt="" class="loading_indicator"/>');

    var unsubscribe = { 'unsubscribes[]' : [] , 'async' : 'true'};
    $(".unsubscribe_checkbox[name=unsubscribe]:checked").each(function() {
    	unsubscribe['unsubscribes[]'].push($(this).val());
    });

    $.get($(this).attr('async_url'), unsubscribe, function(response) {
    	button_unsubscribe.parent().find('img').remove();
    	button_unsubscribe.show();
    	if ($.trim(response) === 'ok') {
	      $(".unsubscribe_checkbox[name=unsubscribe]:checked").each(function() {
	        $(this).parent().parent().parent().remove();      
				});
		  } else {
        App.Wireframe.flash.error('Failed to unsubscribe');
		  }

      if ($('input.unsubscribe_checkbox').length < 1) {
        $('#subscriptions_list').html("<p class=\"empty_page\"><span class=\"inner\">"+App.lang('You do not have any subscriptions in this project')+"</span></p>");
      } // if
    });
  });
</script>