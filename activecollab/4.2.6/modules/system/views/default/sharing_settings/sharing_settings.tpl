{use_widget name="form" module="environment"}

<div id="sharing_settings">
  <div id="sharing_setting_sharing_disabled">
    <p>{lang type=$active_object->getVerboseType(true)}This :type is not shared{/lang}.</p>
    <p>{lang type=$active_object->getVerboseType(true)}If you wish to show this :type to users that are not registered on the system, you should share it{/lang}.</p>

    {if $active_object->getVisibility() == VISIBILITY_PRIVATE && !($active_object->sharing()->getSharingProfile() instanceof SharedObjectProfile)}
      <p id="sharing_settings_visibility_warning">{lang type=$active_object->getVerboseType(true) link_to_manual='http://www.activecollab.com/docs/manuals/tutorials/tips/protect-privacy-of-your-data#use-private-visibility-option'}This :type is marked as <b>Private</b>. If you choose to share it, :type will become visible for all registered users. <a href=":link_to_manual" target="_blank">Click here</a> to learn more{/lang}</p>
    {/if}

    <div class="section_button_wrapper" id="sharing_settings_start_sharing_button">
      <a class="section_button" href="#" style="display: inline-block;"><span>{lang}Start Sharing{/lang}</span></a>
    </div>
  </div>

  <div id="sharing_setting_sharing_enabled">
    <p>{lang}This object is shared to public and it's visible on this url:{/lang}</p>
    <p class="public_url"><strong><a href="#" target="_blank"></a></strong></p>

    <dl>
      <dt>{lang}Sharing Expires{/lang}</dt>
      <dd id="sharing_settings_property_sharing_expires"></dd>

      <dt>{lang}Comments Enabled{/lang}</dt>
      <dd id="sharing_setting_property_comments_enabled"></dd>

      <dt>{lang}Attachments Enabled{/lang}</dt>
      <dd id="sharing_setting_property_attachments_enabled"></dd>

      <dt>{lang object_type=$active_object->getVerboseType(true)}Reopen :object_type on new comment{/lang}</dt>
      <dd id="sharing_setting_property_reopen_on_new_comment"></dd>
    </dl>

    <div class="section_button_wrapper">
      <a class="section_button first" href="#" style="display: inline-block;" id="sharing_settings_update_sharing_button"><span>{lang}Sharing Settings{/lang}</span></a>
      <a class="section_button last" href="#" style="display: inline-block;" id="sharing_settings_stop_sharing_button"><span>{lang}Stop Sharing{/lang}</span></a>
    </div>
  </div>

  <div id="sharing_settings_sharing_update">
    {form action=$active_object->sharing()->getSettingsUrl()}
      {wrap_fields}
        <input type="hidden" name="sharing[code]" value="{$sharing_data.code}" />
        <input type="hidden" name="sharing[enabled]" value="true" id="sharing_settings_sharing_enabled"/>

        <div id="sharing_settings_stop">
          {yes_no name="sharing[unsubscribe_unregistered]" value=0 label="Unsubscribe all unregistered users"}
        </div>

        <div id="sharing_settings_wrapper" class="slide_down_settings" style="">
          {wrap field=expires_on}
            {label}Expiry Date{/label}

            <div id="sharing_settings_does_not_expire">
              {radio_field name="sharing[expires]" checked=empty($sharing_data.expires_on) value=false label="Public Page is Always Available"}
            </div>
            <div id="sharing_settings_does_expire">
              {radio_field name="sharing[expires]" checked=$sharing_data.expires_on value=true label="Public Page is Available Until the Given Date"}
              <div class="slide_down_settings borderless" id="sharing_settings_expire_on_date" {if empty($sharing_data.expires_on)}style="display: none"{/if}>
                {select_date name="sharing[expires_on]" value=$sharing_data.expires_on}
              </div>
            </div>
          {/wrap}

          {if $active_object->sharing()->supportsComments()}
            {wrap field=additional_settings}
              {label}Additional Settings{/label}

              {checkbox name="sharing[comments_enabled]" checked=$sharing_data.comments_enabled label="Enable Comments" id="sharing_settings_comments_enabled"}
              {checkbox name="sharing[attachments_enabled]" checked=$sharing_data.attachments_enabled label="Enable Attachments" id="sharing_settings_attachments_enabled"}

              {if $active_object instanceof IComplete}
                {checkbox name="sharing[comment_reopens]" checked=$sharing_data.comment_reopens label="Reopen on New Comment" id="sharing_settings_comment_reopens"}
              {/if}
            {/wrap}
          {/if}

          <div id="sharing_settings_invitees">
            {wrap field=additional_settings}
              {label type=$active_object->getVerboseType(true)}Invite people to collaborate on this :type{/label}
              {textarea_field name="sharing[invitees]"}{/textarea_field}
              <p class="details">{lang}Enter comma-separated list of email addresses (Invalid ones will be ignored){/lang}</p>
            {/wrap}
          </div>
        </div>
      {/wrap_fields}

      {wrap_buttons}
        {submit}Save Changes{/submit}{lang}or <a href="#" id="sharing_settings_cancel_button">Cancel</a>{/lang}
      {/wrap_buttons}
    {/form}
  </div>
</div>

<script type="text/javascript">

  // encapsulate
  (function () {

    // wrappers
    var wrapper = $('#sharing_settings');
    var enabled_wrapper = wrapper.find('#sharing_setting_sharing_enabled');
    var disabled_wrapper = wrapper.find('#sharing_setting_sharing_disabled');
    var update_wrapper = wrapper.find('#sharing_settings_sharing_update').hide();
    var sharing_settings_wrapper = wrapper.find('#sharing_settings_wrapper');
    var update_form = update_wrapper.find('form:first');
    var sharing_enabled = update_wrapper.find('#sharing_settings_sharing_enabled');
    var invitees_field = update_wrapper.find('#sharing_settings_invitees textarea:first');
    var sharing_settings_stop = update_wrapper.find('#sharing_settings_stop');

    sharing_settings_stop.hide();

    var processing_overlay, confirm_changes;

    /**
     * Puts the form in processing mode
     */
    var start_processing_form = function () {
      if (processing_overlay) {
        return false;
      } // if

      processing_overlay = $('<div class="sharing_settings_processing_overlay"></div>').appendTo(wrapper).css({
        'opacity' : 0,
        'background-image' : 'url(' + App.Wireframe.Utils.indicatorUrl('big') + ')'
      }).animate({
        'opacity' : 1
      });
    }; // start_processing_forms

    /**
     * Stops the form processing
     *
     * @param transition_to
     */
    var stop_processing_form = function (transition_to) {
      if (transition_to) {
        var transition_from;
        if (transition_to.is(enabled_wrapper)) {
          transition_from = update_wrapper
        } else {
          transition_from = enabled_wrapper;
        } // if

        transition_from.slideUp(function () {
          processing_overlay.remove();
          processing_overlay = null;
          transition_to.slideDown();
        });
      } else {
        processing_overlay.animate({
          'opacity' : 0
        }, {
          'complete' : function () {
            processing_overlay.remove();
            processing_overlay = null;
          }
        })
      } // if
    }; // stop_processing_form

    /**
     * Update sharing properties
     *
     * @param Object sharing_properties
     */
    var update_sharing_properties = function (sharing_properties) {
      // update public url
      var public_url = sharing_properties && sharing_properties['urls'] && sharing_properties['urls']['sharing_public'] ? sharing_properties['urls']['sharing_public'] : '';
      enabled_wrapper.find('p.public_url a:first').attr('href', public_url).text(public_url);

      var sharing_expires = sharing_properties && sharing_properties['sharing'] && sharing_properties['sharing']['expires'] ? sharing_properties['sharing']['expires']['formatted_date'] : App.lang('Never');
      enabled_wrapper.find('dd#sharing_settings_property_sharing_expires').text(sharing_expires);

      // comments enabled
      var comments_enabled = sharing_properties && sharing_properties['sharing'] && sharing_properties['sharing']['comments_enabled'] ? App.lang('Yes') : App.lang('No');
      enabled_wrapper.find('dd#sharing_setting_property_comments_enabled').text(comments_enabled);

      // attachments enabled
      var attachments_enabled = sharing_properties && sharing_properties['sharing'] && sharing_properties['sharing']['attachments_enabled'] ? App.lang('Yes') : App.lang('No');
      enabled_wrapper.find('dd#sharing_setting_property_attachments_enabled').text(attachments_enabled);

      // reopen enabled
      var reopen_enabled = sharing_properties && sharing_properties['sharing'] && sharing_properties['sharing']['reopen_on_new_comment'] ? App.lang('Yes') : App.lang('No');
      enabled_wrapper.find('dd#sharing_setting_property_reopen_on_new_comment').text(reopen_enabled);
    }; // update_sharing_properties

    // initially update sharing properties
    update_sharing_properties({$active_object->describe($logged_user, true, true)|json nofilter});

    // is shared
    var is_shared = {$active_object->sharing()->isShared()|json};
    if (is_shared) {
      enabled_wrapper.show();
      disabled_wrapper.hide();
    } else {
      enabled_wrapper.hide();
      disabled_wrapper.show();
    } // if

    // buttons
    var button_start_sharing = wrapper.find('#sharing_settings_start_sharing_button a');
    var button_cancel_sharing = wrapper.find('#sharing_settings_cancel_button');
    var button_save_changes = wrapper.find('#sharing_settings_sharing_update form div.button_holder button:first');
    var button_update_sharing = wrapper.find('#sharing_settings_update_sharing_button');
    var button_stop_sharing = wrapper.find('#sharing_settings_stop_sharing_button');

    // start sharing button behavior
    button_start_sharing.click(function (event) {
      disabled_wrapper.slideUp(function () {
        sharing_settings_wrapper.show();
        sharing_settings_stop.hide();
        confirm_changes = false;
        sharing_enabled.val('true');
        button_save_changes.text(App.lang('Start Sharing'));
        update_wrapper.slideDown();
      });
      event.preventDefault();
    });

    // update settings button behavior
    button_cancel_sharing.click(function (event) {
      update_wrapper.slideUp(function () {
        if (is_shared) {
          sharing_settings_wrapper.show();
          sharing_settings_stop.hide();
          enabled_wrapper.slideDown();
        } else {
          sharing_settings_wrapper.hide();
          sharing_settings_stop.show();
          disabled_wrapper.slideDown();
        } // if
      });
      event.preventDefault();
    });

    // update settings form behavior
    var input_radio_does_not_expire = wrapper.find('#sharing_settings_does_not_expire input[type=radio]');
    var input_text_does_expire = wrapper.find('#sharing_settings_does_expire div.select_date input[type=text]');
    var input_radio_does_expire = wrapper.find('#sharing_settings_does_expire input[type=radio]');
    var date_picker_expire = wrapper.find('#sharing_settings_expire_on_date');

    input_radio_does_not_expire.click(function() {
      input_text_does_expire.val('');
      date_picker_expire.slideUp('fast');
    });

    input_radio_does_expire.click(function () {
      date_picker_expire.slideDown('fast');
    });

    var comments_enabled = wrapper.find('#sharing_settings_comments_enabled');
    var comment_reopens_parent = wrapper.find('#sharing_settings_comment_reopens').parents('div:first');
    var comment_attachments = wrapper.find('#sharing_settings_attachments_enabled').parents('div:first');

    comments_enabled.change(function () {
      if (comments_enabled.is(':checked')) {
        comment_reopens_parent.show();
        comment_attachments.show();
      } else {
        comment_reopens_parent.hide();
        comment_attachments.hide();
      } // if
    });

    comments_enabled.trigger('change');

    // save changes
    button_save_changes.click(function () {
      if (confirm_changes) {
        if (!confirm(App.lang('Are you sure that you want to stop sharing this item?'))) {
          return false;
        } // if
      } // if

      start_processing_form();

      update_form.ajaxSubmit({
        'url' : App.extendUrl(update_form.attr('action'), {
          'async' : 1
        }),
        'success' : function (response) {
          is_shared = response && typeof response['sharing'] == 'object';
          if (!is_shared) {
            enabled_wrapper.hide();
            update_wrapper.hide();
          } // if

          update_sharing_properties(response);

          invitees_field.val('');

          if (response && response['event_names'] && response['event_names']['updated']) {
            App.Wireframe.Events.trigger(response['event_names']['updated'], [response]);
          } // if

          stop_processing_form(is_shared ? enabled_wrapper : disabled_wrapper);
        },
        'error' : function (response) {
          stop_processing_form();
        }
      })
      return false;
    });

    // update sharing
    button_update_sharing.click(function (event) {
      enabled_wrapper.slideUp(function () {
        button_save_changes.text(App.lang('Update Sharing Settings'));
        update_wrapper.slideDown();
      });

      sharing_enabled.val('true');
      confirm_changes = false;
      event.preventDefault();
    });

    // stop sharing
    button_stop_sharing.click(function (event) {
      enabled_wrapper.slideUp(function () {
        sharing_settings_wrapper.hide();
        sharing_settings_stop.show();
        button_save_changes.text(App.lang('Stop sharing'));
        update_wrapper.slideDown();
      });

      sharing_enabled.val('');
      confirm_changes = true;
      event.preventDefault();
    });
  }());
</script>