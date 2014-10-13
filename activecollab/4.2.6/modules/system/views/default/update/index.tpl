{title}Update activeCollab{/title}
{add_bread_crumb}Update activeCollab{/add_bread_crumb}
{use_widget name="wizard" module="system"}

<div id="update_activecollab_wrapper"></div>

<script type="text/javascript">
  var wizard = $('#update_activecollab_wrapper');
  var download_update_url = {Router::assemble('application_update_download_package')|json nofilter};
  var check_download_progress_url = {Router::assemble('application_update_check_download_progress')|json nofilter};
  var unpack_update_package_url = {Router::assemble('application_unpack_download_package')|json nofilter};
  var updated_steps_url = {Router::assemble('application_update_get_upgrade_steps')|json nofilter};
  var upgrade_script_url = {$upgrade_script_url|json nofilter};
  var rebuild_indexes_steps_url = {Router::assemble('indices_admin_rebuild')|json nofilter};
  var install_new_modules_url = {Router::assemble('application_update_install_new_modules')|json nofilter};
  var modules_to_install = {$new_modules|json nofilter};

  var user_email = {$logged_user->getEmail()|json nofilter};
  var user_password = null;
  var update_package_filename = null;
  var update_package_version = null;

  var password_wrapper;
  var password_form;
  var password_field;
  var password_submit_button;

  var indexes_rebuild_required = false;

  var flyout_instance = wizard.parents('.flyout_dialog:first');
  var scope = flyout_instance.length ? 'flyout' : 'content';

  /**
   * Prevent navigating away
   */
  var prevent_navigating_away = function () {
    // on navigating away
    App.Wireframe.Events.bind('navigate_away.' + scope, function (event) {
      window.prevent_navigation = true;
      window.target_form = App.lang('upgrade form');
    });

    // on window unload
    window.onbeforeunload = function () {
      return true;
    };

    $(window).bind('keydown.upload_page.' + scope, function (e) {
      if (e.keyCode == 27) {
        e.preventDefault();
        return false;
      } // if
    });

    if (flyout_instance.length) {
      App.widgets.FlyoutDialog.enableBlockMode(flyout_instance);
    } // if
  } // prevent_navigating_away

  /**
   * allow navigating away
   */
  var allow_navigating_away = function () {
    App.Wireframe.Events.unbind('navigate_away.' + scope);
    window.onbeforeunload = null;
    $(window).unbind('keydown.upload_page.' + scope);

    if (flyout_instance.length) {
      App.widgets.FlyoutDialog.disableBlockMode(flyout_instance);
    } // if
  } // enable_navigating_away


  /**
   * Render progressbar
   *
   * @param wrapper
   */
  var render_progressbar = function (wrapper) {
    var progressbar_outer_wrapper = $('<div class="progressbar_bar_wrapper"></div>').appendTo(wrapper);
    var progressbar_outer = $('<div class="progressbar_bar_outer"></div>').appendTo(progressbar_outer_wrapper);
    var progressbar_inner = $('<div class="progressbar_bar"></div>').appendTo(progressbar_outer);
    var progressbar_progress = $('<div class="progressbar_bar_progress"></div>').appendTo(progressbar_outer_wrapper);
    var progressbar_details = $('<div class="progressbar_bar_details"></div>').appendTo(wrapper);

    progressbar_progress.text('0%');
    progressbar_inner.css('width', '0%');

    return [progressbar_outer_wrapper, progressbar_outer, progressbar_inner, progressbar_progress, progressbar_details];
  } // render_progressbar

  wizard.wizard({
    'steps' : [
      // step 1
      {
        'render' : function (step_wrapper, is_active) {
          var warning_message_wrapper = $('<div class="warning_message_wrapper"></div>').appendTo(step_wrapper);
          warning_message_wrapper.append('<p class="title"><strong>' + App.lang('Welcome to activecollab upgrade process.' + '</strong></p>'));
          warning_message_wrapper.append('<p>' + App.lang("Once the progress is started, it <u>shouldn't be interrupted</u>, as it may leave the system in an unusable state." + '</p>'));
          warning_message_wrapper.append('<p>' + App.lang('By entering your password you are acknowledging this warning.' + '</p>'));

          password_wrapper = $('<div class="password_wrapper"></div>').appendTo(step_wrapper);
          password_form = $('<form action="{Router::assemble('application_update_check_password')}" method="POST" autocomplete="off"></form>').appendTo(password_wrapper);
          password_field = $('<input type="password" name="password" id="password_field" placeholder="' + App.lang('Your Password') + '" />').appendTo(password_form);
          password_submit_button = $('<button class="default" accesskey="s" type="submit">' + App.lang('Start the upgrade') + '</button>').appendTo(password_form);

          var check_password_request = false;

          password_form.submit(function () {
            if (check_password_request) {
              check_password_request.abort();
            } // if

            password_form.block();

            $.ajax({
              'url'       : App.extendUrl(password_form.attr('action'), { 'async' : 1 }),
              'data'      : {
                  'submitted' : 'submitted',
                  'password'  : password_field.val()
              },
              'type'      : 'post',
              'complete'  : function () {
                check_password_request = null;
              },
              'success'   : function (response) {
                user_password = password_field.val();
                wizard.wizard('next_step');
              },
              'error'     : function (response) {
                {literal} var response_is_obj = response.responseText.substr(0, 1) == '{'; {/literal}

                if (response_is_obj) {
                  eval('var response_obj = ' + response.responseText + ';');
                  var validation_errors = response_obj['validation_errors']
                  if (validation_errors) {
                    step_wrapper.empty();
                    step_wrapper.append('<label>' + response_obj['message'] + '</label>');

                    var error_list = $('<ul class="error_list"></ul>').appendTo(step_wrapper);
                    $.each(validation_errors, function (index, validation_error) {
                      error_list.append('<li><label class="error_label">' + validation_error + '</label></li>');
                    });

                    allow_navigating_away();
                    return false;
                  } // if
                } // if

                password_form.unblock();
              }
            })

            return false;
          });
        },
        'deactivate' : function (step_wrapper) {
          step_wrapper.empty();
          step_wrapper.append('<label class="success_label">' + App.lang('Authenticated') + '</label>');
        }

      // step 2
      }, {
        'render' : function (step_wrapper, is_active) {
          step_wrapper.append('<label>' + App.lang('Download new version') + '</label>');
        },
        'activate' : function (step_wrapper) {
          prevent_navigating_away();

          step_wrapper.empty().append('<label>' + App.lang('Downloading new version') + '</label>');
          var progressbar_wrapper = $('<div class="progressbar_wrapper"></div>').appendTo(step_wrapper);
          var rendered_progressbar = render_progressbar(progressbar_wrapper);

          var progressbar_outer_wrapper = rendered_progressbar[0];
          var progressbar_outer = rendered_progressbar[1];
          var progressbar_inner = rendered_progressbar[2];
          var progressbar_progress = rendered_progressbar[3];
          var progressbar_details = rendered_progressbar[4];

          progressbar_details.text(App.lang('Downloading Update') + ' ...');

          var progress_interval;
          var progress_ajax;

          // main request that actually downloads update file
          var download_update_ajax = $.ajax({
            'url'     : App.extendUrl(download_update_url, {
              'async' : 1
            }),
            'type'    : 'POST',
            'data'    : {
              'submitted' : 'submitted'
            },
            'success' : function (response) {
              if (typeof(response) == 'object' && response['success']) {
                update_package_filename = response['package_filename'];
                update_package_version = response['package_version'];
                wizard.wizard('next_step');
              } else {
                allow_navigating_away();
                App.Wireframe.Flash.error('Unexpected error occurred');
              } // if
            },
            'error' : function () {
              allow_navigating_away();
            },
            'complete' : function () {
              clearInterval(progress_interval);
              if (progress_ajax) {
                progress_ajax.abort();
              } // if
            }
          });

          /**
           * Check download progress
           */
          var check_download_progress = function () {
            // abort previous check progress report
            if (progress_ajax) {
              progress_ajax.abort();
            } // if

            // check for progress
            progress_ajax = $.ajax({
              'url': App.extendUrl(check_download_progress_url, { 'async' : 1 }),
              'success' : function (response) {
                if (typeof(response) == 'object' && response['progress']) {
                  progressbar_progress.text(response['progress'] + '%');
                  progressbar_inner.stop().animate({
                    'width' : response['progress'] + '%'
                  }, 6000);
                } // if
              }
            });
          }; // check_download_progress

          // interval request which checks progress of the download
          progress_interval = setInterval(function () {
            check_download_progress();
          }, 5000);

          // check progress right away
          setTimeout(function () {
            check_download_progress();
          }, 3000);

        },
        'deactivate' : function (step_wrapper) {
          step_wrapper.empty().append('<label class="success_label">' + App.lang('Update downloaded') + '</label>');
        }

        // step 3
      }, {

        'render' : function (step_wrapper, is_active) {
          step_wrapper.append('<label>' + App.lang('Unpack update package') + '</label>');
        },

        'activate' : function (step_wrapper) {
          step_wrapper.empty().append('<label class="in_progress_label">' + App.lang('Unpacking update package') + '</label>');

          $.ajax({
            'url' : App.extendUrl(unpack_update_package_url, { 'async' : 1 }),
            'type' : 'post',
            'data' : {
              'submitted' : 'submitted',
              'package_filename' : update_package_filename,
              'package_version' : update_package_version
            },
            'success' : function (response) {
              if (typeof(response) == 'object' && response['success']) {
                wizard.wizard('next_step');
              } else {
                allow_navigating_away();
                step_wrapper.empty().append('<label class="error_label">' + App.lang('Unpacking Failed') + '</label>');
                App.Wireframe.Flash.error('Unexpected error occurred');
              } // if
            },
            'error' : function () {
              step_wrapper.empty().append('<label class="error_label">' + App.lang('Unpacking Failed') + '</label>');
              allow_navigating_away();
            }
          });
        },

        'deactivate' : function (step_wrapper) {
          step_wrapper.empty().append('<label class="success_label">' + App.lang('Update package unpacked') + '</label>');
        }

      // step 4
      }, {
        'render' : function (step_wrapper, is_active) {
          step_wrapper.append('<label>' + App.lang('Update application') + '</label>');
        },

        'activate' : function (step_wrapper) {
          step_wrapper.empty().append('<label>' + App.lang('Updating application') + '</label>');

          var progressbar_wrapper = $('<div class="progressbar_wrapper"></div>').appendTo(step_wrapper);
          var rendered_progressbar = render_progressbar(progressbar_wrapper);

          var progressbar_outer_wrapper = rendered_progressbar[0];
          var progressbar_outer = rendered_progressbar[1];
          var progressbar_inner = rendered_progressbar[2];
          var progressbar_progress = rendered_progressbar[3];
          var progressbar_details = rendered_progressbar[4];

          progressbar_details.text(App.lang('Getting list of upgrade steps') + ' ...');
          progressbar_progress.text('');

          var perform_database_upgrade_step;
          var total_steps = 0;
          var current_step = 0;

          /**
           * Perform database upgrade step
           *
           * @param Array steps
           */
          perform_database_upgrade_step = function (steps) {
            if (!steps.length) {
              wizard.wizard('next_step');
              return;
            } // if

            // get current step
            var step = steps.shift();
            current_step++;

            // update progressbar
            progressbar_details.text(step['description']);
            progressbar_progress.text(current_step + '/' + total_steps);

            if (step['action'] == 'scheduleIndexesRebuild') {
              indexes_rebuild_required = true;
            } // if

            $.ajax({
              'url'     : App.extendUrl(upgrade_script_url, { 'async' : 1 }),
              'defaultErrorHandler' : false,
              'type'    : 'POST',
              'data'    : {
                'submitted'     : 'submitted',
                'upgrade_step'  : {
                  'email'         : user_email,
                  'password'      : user_password,
                  'group'         : step['group'],
                  'action'        : step['action']
                }
              },
              'success' : function (response) {
                // update progressbar
                progressbar_inner.stop().animate({
                  'width' : Math.round((current_step + 1) * 100 / (total_steps)) + '%'
                }, 4000);

                perform_database_upgrade_step(steps);
              },
              'error'   : function (response) {
                allow_navigating_away();
                step_wrapper.empty().append('<label class="error_label">' + response['responseText'] + '</label>');
              }
            });
          } // perform_database_upgrade_step

          // get the list of update steps
          $.ajax({
            'url' : App.extendUrl(updated_steps_url, { async : 1 }),
            'type' : 'GET',
            'defaultErrorHandler' : false,
            'success' : function (response) {
              total_steps = response['steps'].length;
              perform_database_upgrade_step(response['steps']);
            },
            'error' : function (response) {
              allow_navigating_away();
              step_wrapper.empty().append('<label class="error_label">' + App.lang('Failed to retrieve upgrade steps') + '</label>');
            }
          })
        },

        'deactivate' : function (step_wrapper) {
          step_wrapper.empty().append('<label class="success_label">' + App.lang('Application updated') + '</label>');
        }

      // step 5
      }, {
        'render' : function (step_wrapper, is_active) {
          step_wrapper.append('<label>' + App.lang('Install new modules') + '</label>');
        },

        'activate' : function (step_wrapper) {
          // if there are no modules to install proceed
          if (!modules_to_install || !modules_to_install.length) {
            step_wrapper.empty().append('<label class="success_label">' + App.lang('No new modules found') + '</label>');
            wizard.wizard('next_step');
            return;
          } // if

          step_wrapper.empty().append('<label class="in_progress_label">' + App.lang('Installing new modules') + '</label>');

          $.ajax({
            'url' : App.extendUrl(install_new_modules_url, { async : 1 }),
            'type' : 'GET',
            'defaultErrorHandler' : false,
            'data' : {
              'modules' : modules_to_install
            },
            'success' : function (response) {
              step_wrapper.empty();

              if (response['errors'] && response['errors'].length) {
                $.each(response['errors'], function (index, error) {
                  if (!step_wrapper.text()) {
                    step_wrapper.append('<br />');
                  } // if
                  step_wrapper.append('<label class="warning_label">' + error + '</label>');
                });
              } else {
                step_wrapper.append('<label class="success_label">' + App.lang('New modules installed') + '</label>');
              } // if
              wizard.wizard('next_step');
            },
            'error' : function (response) {
              step_wrapper.empty().append('<label class="error_label">' + App.lang('Unknown error occurred') + '</label>');
              wizard.wizard('next_step');
            }
          });
        }

      // step 6
      }, {
        'render' : function (step_wrapper, is_active) {
          step_wrapper.append('<label>' + App.lang('Rebuild indexes') + '</label>');
        },

        'activate' : function (step_wrapper) {
          // if indexes rebuild is not needed proceed to next step
          if (!indexes_rebuild_required) {
            wizard.wizard('next_step');
            return;
          } // if

          var progressbar_wrapper = $('<div class="progressbar_wrapper"></div>').appendTo(step_wrapper);
          var rendered_progressbar = render_progressbar(progressbar_wrapper);

          var progressbar_outer_wrapper = rendered_progressbar[0];
          var progressbar_outer = rendered_progressbar[1];
          var progressbar_inner = rendered_progressbar[2];
          var progressbar_progress = rendered_progressbar[3];
          var progressbar_details = rendered_progressbar[4];

          progressbar_details.text(App.lang('Getting list of rebuild steps') + ' ...');
          progressbar_progress.text('');

          var perform_index_rebuild;
          var total_steps = 0;
          var current_step = 0;

          /**
           * Perform index rebuild step
           *
           * @param Array steps
           */
          perform_index_rebuild = function (steps) {
            if (!steps.length) {
              wizard.wizard('next_step');
              return;
            } // if

            // get current step
            var step = steps.shift();
            var step_url = step['__k'];
            var step_description = step['__v'];
            current_step++;

            // update progressbar
            progressbar_details.text(step_description);
            progressbar_progress.text(current_step + '/' + total_steps);

            $.ajax({
              'url'     : App.extendUrl(step_url, { 'async' : 1 }),
              'defaultErrorHandler' : false,
              'type'    : 'POST',
              'data'    : {
                'submitted'     : 'submitted'
              },
              'success' : function (response) {
                // update progressbar
                progressbar_inner.stop().animate({
                  'width' : Math.round((current_step + 1) * 100 / (total_steps)) + '%'
                }, 4000);

                perform_index_rebuild(steps);
              },
              'error'   : function (response) {
                perform_index_rebuild(steps);
              }
            });
          } // perform_index_rebuild

          // get the list of update steps
          $.ajax({
            'url' : App.extendUrl(rebuild_indexes_steps_url, { async : 1, 'return_steps' : 1 }),
            'type' : 'GET',
            'defaultErrorHandler' : false,
            'success' : function (response) {
              total_steps = response.length;
              perform_index_rebuild(response);
            },
            'error' : function (response) {
              step_wrapper.empty().append('<label class="error_label">' + App.lang('Failed to retrieve list of rebuild index steps') + '</label>');
              wizard.wizard('next_step');
            }
          })
        },

        'deactivate' : function (step_wrapper) {
          if (indexes_rebuild_required) {
            step_wrapper.empty().append('<label class="success_label">' + App.lang('Indexes rebuilt') + '</label>');
          } else {
            step_wrapper.empty().append('<label class="success_label">' + App.lang('Rebuilding indexes not needed') + '</label>');
          } // if
        }
      }

    ],
    'complete' : function () {
      allow_navigating_away();
      var wizard_table = wizard.find('table.wizard_table').hide();
      var success_wrapper = $('<div class="update_successful"></div>').insertAfter(wizard_table);

      success_wrapper.append('<h3>' + App.lang('Upgrade completed') + '</h3>');
      success_wrapper.append('<p>' + App.lang('All windows and tabs with activeCollab opened should be reloaded.') + '</p>');
      success_wrapper.append('<p class="button_wrapper"><button class="default" type="submit">Refresh page and complete upgrade</button></p>');

      success_wrapper.find('button').click(function () {
        document.location.reload();
        return false;
      });
    }
  })
</script>

<style type="text/css">
  #update_activecollab_wrapper .warning_message_wrapper p {
    margin: 2px 0px !important;
  }

  #update_activecollab_wrapper .warning_message_wrapper p.title {
    margin-bottom: 7px !important;
  }

  #update_activecollab_wrapper .password_wrapper {
    padding-top: 7px;
  }

  #update_activecollab_wrapper label {
    font-weight: bold;
    display: block;
    margin-bottom: 8px;
    height: 16px;
  }

  #update_activecollab_wrapper button {
    margin-left: 10px;
  }

  #update_activecollab_wrapper .progressbar_bar_outer {
    background: #333;
    width: 250px;
    height: 8px;
    border-radius: 5px;
    border: 1px solid #333;
    position: relative;
  }

  #update_activecollab_wrapper .progressbar_bar_outer .progressbar_bar {
    position: absolute;
    left: 0px;
    bottom: 0px;
    top: 0px;
    background: #FFF;
    border-radius: 5px;
  }

  #update_activecollab_wrapper .progressbar_wrapper {
    position: relative;
  }

  #update_activecollab_wrapper .progressbar_wrapper .progressbar_bar_progress {
    position: absolute;
    left: 260px;
    top: -2px;
    color: #666;
  }

  #update_activecollab_wrapper .progressbar_wrapper .progressbar_bar_details {
    margin-top: 7px;
    color: #999;
  }

  #update_activecollab_wrapper .update_successful {
    background: #FFF;
    padding: 35px 0px;
    text-align: center;
  }

  #update_activecollab_wrapper .update_successful h3 {
    font-weight: bold;
    margin-bottom: 15px;
  }

  #update_activecollab_wrapper .update_successful p.button_wrapper {
    margin-top: 15px;
  }
</style>