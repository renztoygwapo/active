{if !$request->isAsyncCall()}
  {title}Advanced Search{/title}
  {add_bread_crumb}Advanced Search{/add_bread_crumb}
{/if}

<div id="backend_search" class="{if !$request->isAsyncCall()}page_mode{/if}">
  {form action=Router::assemble('backend_search') id="backend_search_form"}
    <div id="backend_search_for">
    	<input type="text">
    </div>
    
    <div id="backed_search_additional_wrapper">
      <div id="backend_search_types">
    {foreach Search::getIndices() as $index}
      {if $index->isAdvanced()}
      	{radio_field name="search[type]" value=$index->getShortName() label=$index->getName()}
      {/if}
    {/foreach}
      </div>
      
      <div id="backend_search_additional">
        <ul>
          <li><a href="#" id="backend_search_filters_toggler">{lang}More Options{/lang}</a></li>
          <li><a href="#" id="backend_search_tips_toggler">{lang}Search Tips{/lang}</a></li>
        </ul>
      </div>
    </div>
    
    <div id="backend_search_filters_and_tips">
      <div id="backend_search_filters" class="backend_search_filters_and_tips_wrapper">
        <h3>{lang}Additional Filters{/lang}</h3>
        
      {foreach Search::getIndices() as $index}
        {if is_foreachable($index->getFilters())}
        <div id="backend_search_filters_for_{$index->getShortName()}" class="backend_search_filters_for">
        {foreach $index->getFilters() as $filter}
          {$filter->render() nofilter}
        {/foreach}
        </div>
        {/if}
      {/foreach}
      </div>
      
      <div id="backend_search_tips" class="backend_search_filters_and_tips_wrapper">
        <h3>{lang}Search Tips{/lang}</h3>
      {if Search::hasTips()}
        <ul>
        {foreach Search::getTips() as $search_tip}
          <li>{$search_tip|clickable nofilter}</li>
        {/foreach}
        </ul>
      {else}
        <p>{lang}Active search engine does not have any search tips{/lang}</p>
      {/if}
      </div>
    </div>
    
    <div id="backend_search_button">
    	{submit}Search{/submit}
    </div>
  {/form}
  
  <div id="backend_search_results"></div>
</div>

<script type="text/javascript">
  $('#backend_search').each(function() {
    var wrapper = $(this);

    var form = wrapper.find('#backend_search_form');
    var input = form.find('#backend_search_for input[type=text]').focus();
    var button_wrapper = form.find('#backend_search_button');
    var button = button_wrapper.find('button[type=submit]');

    var filters = wrapper.find('#backend_search_filters');
    var tips = wrapper.find('#backend_search_tips');

    var results = wrapper.find('#backend_search_results');

    /**
     * Set specific search type
     *
     * @param String type_name
     */
    var set_search_type = function(type_name) {
      var radio = wrapper.find('#backend_search_types input[value=' + type_name + ']');

      if(radio.length) {
        if(!radio[0].checked) { 
        	radio[0].checked = true;
        } // if

        var anim = filters.is(':visible') ? 'fast' : null;

        filters.find('div.backend_search_filters_for').hide(anim);
        filters.find('#backend_search_filters_for_' + type_name).show(anim);
      } // if
    };

    wrapper.find('#backend_search_types input[type=radio]').click(function() {
      set_search_type($(this).attr('value'));
      input.focus();
    });

    // Select first search type
    set_search_type(wrapper.find('#backend_search_types input[type=radio]:first').val());

    // Show / hide additional filters
    wrapper.find('#backend_search_filters_toggler').click(function() {
      if(filters.is(':visible')) {
        filters.slideUp('fast');
      } else {
        if(tips.is(':visible')) {
          tips.slideUp('fast');
        } // if

        filters.slideDown('fast');
      } // if
      
      return false;
    });

    // Show / hide tips
    wrapper.find('#backend_search_tips_toggler').click(function() {
      if(tips.is(':visible')) {
        tips.slideUp('fast');
      } else {
        if(filters.is(':visible')) {
          filters.slideUp('fast');
        } // if

        tips.slideDown('fast');
      } // if
      
      return false;
    });

    form.submit(function() {
      if(form.is('form.searching')) {
        return false;
      } // if
      
      var search_for = jQuery.trim(input.val());
      var search_index = form.find('#backend_search_types input[type=radio]:checked').val();

      if(search_for && search_index) {
        this['search_form_searching'] = true;

        var indicator = $('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">').appendTo(button_wrapper);
        button.hide();

        var url = App.extendUrl(form.attr('action'), {
          'search[for]' : search_for, 
         	'search[index]' : search_index
        });

        var additional_filters_wrapper = filters.find('#backend_search_filters_for_' + search_index);
        if(additional_filters_wrapper.length && additional_filters_wrapper.is(':visible')) {
          var additional_filters = additional_filters_wrapper.find('input, select').serialize();

          if(additional_filters) {
            url += '&' + additional_filters;
          } // if
        } // if

        form.addClass('searching');
        results.hide();

        $.ajax({
          'url' : url, 
          'type' : 'get', 
          'success' : function(response) {
            form.removeClass('searching');
            indicator.remove();
            button.show();

            if(jQuery.isArray(response) && response.length == 2) {
              results.empty();

              if(response[1] > 0) {
                var results_table = $('<table class="common" cellspacing="0"><tbody></tbody></table>').appendTo(results);

                /**
                 * Render content for a row
                 *
                 * @param Object search_result_record
                 */
                var render_row = function(search_result_record) {
                  var extra_name_class = '';
                  if(search_result_record['is_crossed_over']) {
                    extra_name_class += ' crossed_over';
                  } // if

                  return '<tr class="search_result"><td class="name' + extra_name_class + '"><a href="' + App.clean(search_result_record['permalink']) + '">' + App.clean(search_result_record['name']) + '</a></td></tr>';
                }; // render_row

                var rows = '';
								
                App.each(response[0], function(key, value) {
                  rows += render_row(value);
                });

                results_table.find('tbody').append(rows);

                if(response[0].length == response[1]) {
                  if(response[1] == 1) {
                    var search_returned_message = App.lang('Search returned one result');
                    
                    $('<p class="results_count">' +  + '</p>').appendTo(results);
                  } else {
                    var search_returned_message = App.lang('Search returned <span class="num">:num</span> results', { 
                      'num' : response[1] 
                    });
                  } // if
                } else {
                  var next_page = 2;
                  var search_returned_message = App.lang('Showing <span class="num">:num</span> of <span class="total">:total</span> results. <a href=":load_more_url" class="load_more">Load more</a>', {
                    'num' : response[0].length, 
                    'total' : response[1], 
                  	'load_more_url' : App.extendUrl(url, { 'page' : next_page })
                  });
                } // if

                var search_returned = $('<p id="backend_search_returned_message"></p>').append(search_returned_message).appendTo(results);

                // Load more results behavior
                search_returned.find('a.load_more').click(function() {
                  var link = $(this);
                  var indicator = $('<p id="backend_search_loading_more"><img src="' + App.Wireframe.Utils.indicatorUrl() + '"></p>');

                  search_returned.hide().after(indicator);
                  
                  $.ajax({
                    'url' : App.extendUrl(url, { 'page' : next_page }), 
                    'type' : 'get', 
                    'success' : function(response) {
                      indicator.remove();
                      search_returned.show();
                      
                      if(jQuery.isArray(response) && response.length == 2) {
                        var rows = '';

                        App.each(response[0], function(i,object) {
                        	rows += render_row(object);
                        }); // App.each

                        results_table.find('tbody').append(rows);

                        var loaded_records = results_table.find('tbody tr.search_result').length;

                        search_returned.find('span.num').text(loaded_records);
                        if(loaded_records >= response[1]) {
                          search_returned.html(App.lang('Showing all <span class="total">:total</span> results', { 'total' : loaded_records }));
                        } else {
                          next_page++;
                          search_returned.find('a').attr('href', App.extendUrl(url, { 'page' : next_page }));
                        } // if
                      } else {
                        App.Wireframe.Flash.error('Search return no additional results');
                      } // if
                    }, 
                    'error' : function() {
                      indicator.remove();
                      search_returned.show();
                      
                      App.Wireframe.Flash.error('Failed to execute your search query. Please try again later');
                    }
                  });
                  
                  return false;
                });
              } else {
                results.append('<p class="empty_page">' + App.lang('Search return an empty result') + '</p>');
              } // if

              results.show();
            } else {
              App.Wireframe.Flash.error('Failed to execute your search query. Please try again later');
            } // if
          }, 
          'error' : function() {
            form.removeClass('searching');
            indicator.remove();
            button.show();
          }
        });
      } else {
        input.val('').focus();
      } // if
      
      return false;
    });
  });
</script>