  {title lang=false}{$active_language->getName()}{/title}
  {add_bread_crumb}Details{/add_bread_crumb}

  {object object=$active_language user=$logged_user}
    <div class="wireframe_content_wrapper" id="update_translation">
      {wrap field=checkbox}
        {checkbox_field label="Outline phrases that don't have a translation" class="outline_translations"}
      {/wrap}
      {wrap field=checkbox}
        {checkbox_field label="Only show phrases that don't have a translation" class="show_hide_translations"}
      {/wrap}
      {form action=$edit_translation_url id="edit_translation_form" method=post ask_on_leave=yes}
        {inline_tabs inline_tabs=$inline_tabs}
      {/form}
    </div>
  {/object}
  
  <script type="text/javascript">
    var wrapper = $('#update_translation');
    var save_translation_url = '{assemble route="admin_language_save_single_translation" language_id=$active_language->getId()}';
    var languages_index_url = '{$language_url nofilter}';
  
    var original_indicator_image = App.Wireframe.Utils.imageUrl('layout/bits/indicator-pending.png', 'environment');
    var processing_indicator_image = App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-normal.gif', 'environment');
    var save_indicator_image = App.Wireframe.Utils.imageUrl('icons/16x16/save.png', 'environment');
    var success_indicator_image = App.Wireframe.Utils.imageUrl('layout/bits/indicator-ok.png', 'environment');


    /**
     * Redirect to language index page after deleting the language
     */
    App.Wireframe.Events.bind('language_deleted', function (event, repository) {
      App.Wireframe.Content.setFromUrl(languages_index_url);
    });

    /**
     * Save the translation
     */
    function save_translation(control) {
      // nothing has changed
      if (control.val() == control.attr('saved_translation')) {
        return false;
      } // if
  
      var indicator = control.parents('tr:first').find('td.actions img.indicator').attr('src', processing_indicator_image);
  
      $.ajax({
        'url' : save_translation_url,
        'type' : 'post',
        'data' : {
          'hash' : control.attr('name'),
          'translation' : control.val(),
          'submitted' : 'submitted'
        },
        'success' : function (response) {
          indicator.attr('src', success_indicator_image);

          // check for outlined translations
          toggle_outline_phrases(control);
        },
        'error' : function (response) {
          indicator.attr('src', save_indicator_image);
        }
      });
    } // save_translation
  
    /**
     * Revert translation
    */
    function revert_translation(control) {
      control.val(control.attr('saved_translation'));
      save_translation(control);
      check_for_change(control);
      control.focus();
      return true;
    } // revert_translation
  
    /**
     * check if something has changed
     */
    function check_for_change(control) {
      var indicator = control.parents('tr:first').find('td.actions img.indicator');
  
      // nothing has changed
      if (control.val() == control.attr('saved_translation')) {
        indicator.attr('src', original_indicator_image);
        return true;
      } // if
  
      indicator.attr('src', save_indicator_image);
    } // check_for_change
  
    
    // on blur save the translation
    wrapper.delegate('input[type="text"],textarea', 'blur', function () {
      var control = $(this);
      save_translation(control);
    });
  
    // save icon
    wrapper.delegate('input[type="text"],textarea', 'keyup', function (event) {
      var control = $(this);
      if (event.which == 13) {
        save_translation(control);
        return false;
      } else if (event.which == 27) {
        revert_translation(control);
        return false;
      } else {
        check_for_change(control);
      } // if
    });
  
    // copy original phrase into field
    wrapper.delegate('td.copy_arrow img', 'click', function () {
      var row = $(this).parents('tr:first');
      var control = row.find('input[type="text"]:first, textarea:first');
      control.val(row.find('td.dictionary').text()).focus();
      check_for_change(control);
      return false;
    });

    $('.outline_translations').click(function() {
      var control = $('#update_translation input[type="text"],textarea');
      if ($(this).is(':checked')) {
        $.each(control, function(key, value) {
          toggle_outline_phrases($(value));
        }); //each
      } else {
        $('#update_translation td.red_text').removeClass('red_text');
      } //if
    });

    /**
     * Toggle finds all empty translations and paint them in red
     * @param control object
     */

    function toggle_outline_phrases(control) {
      if ($('.outline_translations').is(':checked')) {
        if(control.val() == '') {
          control.parents('tr:first').find('td.dictionary').addClass('red_text');
        } else {
          control.parents('tr:first').find('td.dictionary').removeClass('red_text');
        }//if
      } //if
    } //outline_phrases

    $('.show_hide_translations').click(function() {
      var control = $('#update_translation input[type="text"],textarea');
      if ($(this).is(':checked')) {
        $.each(control, function(key, value) {
          toggle_hide_translated_phrases($(value));
        }); //each
      } else {
        $('#update_translation tr.hidden').removeClass('hidden');
      } //if
    });

    /**
     * Toggle finds all translated rows and hides them
     * @param control object
     */
    function toggle_hide_translated_phrases(control) {
      if ($('.show_hide_translations').is(':checked')) {
        if(control.val() != '') {
          control.parents('tr:first').addClass('hidden');
        } //if
      } //if
    } //outline_phrases

    $('#edit_translation_form').find('div.inline_tabs_links a').click(function() {
      var checkbox_outline_translations = $('.outline_translations');
      if (checkbox_outline_translations.is(':checked')) {
        checkbox_outline_translations.removeAttr('checked');
      } //if

      var checkbox_hide_translations = $('.show_hide_translations');
      if (checkbox_hide_translations.is(':checked')) {
        checkbox_hide_translations.removeAttr('checked');
      } //if
    });
  </script>