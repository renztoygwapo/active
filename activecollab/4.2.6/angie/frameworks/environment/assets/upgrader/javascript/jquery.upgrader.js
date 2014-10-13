/**
 * Angie application upgrade script routine 
 */
(function($) {

  var methods = {
      
    /**
     * Initialize object priority
     * 
     * @param s
     */
    'init' : function(s) {
      var settings = jQuery.extend({ 
        'name' : 'Application'
      }, s);
      
      return this.each(function() {
        var wrapper = $(this);
        
        wrapper.find('div.upgrader_section div.body').hide();
        wrapper.upgrader('show_section', wrapper.find('div.upgrader_section:first').attr('upgrader_section'));
      });
    }, 
    
    /**
     * Show section
     * 
     * @param String section_name
     * @param Object prev_data
     * @param Boolean animate
     * @returns
     */
    'show_section' : function(section_name, animate) {
      return this.each(function() {
        var wrapper = $(this);
        
        /**
         * Initialize section
         * 
         * @param jQuery section
         */
        var init_section = function(section) {
          section.find('form:first').submit(function() {
            var form = $(this);
            var is_valid = true, form_data = {}, validation_errors = [];
            
            var validators = wrapper.data('validators');
            
            if(typeof(validators) == 'object' && typeof(validators[section_name]) == 'function') {
              var is_valid = validators[section_name].apply(form[0], [ form_data, validation_errors ]); 
            } // if
            
            // Form is valid, submit it
            if(is_valid) {
              form.find('button[type=submit], input[type=submit]').text('Working ...').attr('disabled', 'disabled');
              
              // Start with form data
              var data = form_data;
              
              // Now add data from previous steps
              var upgrader_data = wrapper.data('upgrader_data');
              if(typeof(upgrader_data) == 'object' && typeof(upgrader_data)) {
                for(var i in upgrader_data) {
                  data[i] = upgrader_data[i];
                } // for
              } // if
              
              // And system data
              data['submitted'] = 'submitted';
              data['upgrader_section'] = section.attr('upgrader_section');
              
              // And now submit..
              $.ajax({
                'url' : form.attr('action'), 
                'type' : 'post', 
                'data' : form_data, 
                'success' : function(response) {
                  wrapper.data('upgrader_data', upgrader_data);
                  
                  section.removeClass('has_errors');
                  section.find('div.body').empty().append(response);
                  
                  var next_section = section.next();
                  if(next_section.length > 0 && next_section.is('div.upgrader_section')) {
                    
                    // Remember data from this step
                    var upgrader_data = wrapper.data('upgrader_data');
                    
                    if(typeof(upgrader_data) != 'object') {
                      upgrader_data = {};
                    } // if
                    
                    for(var i in form_data) {
                      upgrader_data[i] = form_data[i]
                    } // for
                    
                    wrapper.data('upgrader_data', upgrader_data);
                    
                    // And show the next one
                    wrapper.upgrader('show_section', next_section.attr('upgrader_section'), true);
                  } // if
                }, 
                'error' : function(response) {
                  section.addClass('has_errors');
                  section.find('div.body').empty().append(response['responseText']);
                  init_section(section);
                }
              });
              
            // Form is not valid
            } else {
              section.addClass('has_errors');
              
              var validation = section.find('div.validation_errors');
              
              if(validation.length > 0) {
                validation.empty();
              } else {
                validation = $('<div class="validation_errors"></div>');
                section.find('div.body').prepend(validation);
              } // if
              
              if(validation_errors.length > 0) {
                validation.append('<p>Data you inserted is not valid. System found following errors:</p>');
                
                var list = $('<ul></ul>').appendTo(validation);
                
                for(var i in validation_errors) {
                  $('<li>' + validation_errors[i] + '</li>').text(validation_errors[i]).appendTo(list);
                } // for
              } else {
                validation.append('<p>Data you inserted is not valid. Please check it again.</p>');
              } // if
            } // if
            
            return false;
          });
        };
        
        var section = wrapper.find('div.upgrader_section[upgrader_section=' + section_name + ']');
        
        if(section.length > 0) {
          init_section(section);
          
          if(animate) {
            section.find('div.body').slideDown('fast', function() {
              var input = section.find('div.body form input:first');
              
              if(input.length > 0) {
                input.focus();
              } // if
            });
          } else {
            section.find('div.body').show();
            
            var input = section.find('div.body form input:first');
            
            if(input.length > 0) {
              input.focus();
            } // if
          } // if
        } // if
      });
    }, 
    
    /**
     * Run all steps
     * 
     * @param String email
     * @param String password
     */
    'run' : function(email, password) {
      return this.each(function() {
        var wrapper = $(this);
        var url = location['href'];

        if (url.substr(url.length - 1) == '/') {
          url += 'index.php';
        } // if
        
        if(url.substr(url.length - 10) != '/index.php') {
          url += '/index.php';
        } // if
        
        var next_action = function() {
          var next_action_item = wrapper.find('ul#upgrader_actions_list li.not_executed:first');
          
          if(next_action_item.length == 1) {
            next_action_item.removeClass('not_executed').addClass('executing');
            
            var original_text = next_action_item.text();
            
            var counter = 0;
            var interval = setInterval(function() {
              counter++;
              
              if(counter <= 3) {
                next_action_item.text(next_action_item.text() + '.');
              } else {
                next_action_item.text(original_text);
                counter = 0;
              } // if
            }, 500);
            
            $.ajax({
              'url' : url, 
              'type' : 'post', 
              'data' : {
                'upgrade_step[group]' : next_action_item.attr('upgrade_group'), 
                'upgrade_step[action]' : next_action_item.attr('upgrade_action'), 
                'upgrade_step[email]' : email, 
                'upgrade_step[password]' : password, 
                'submitted' : 'submitted'
              }, 
              'success' : function(response) {
                clearInterval(interval);
                next_action_item.text(original_text).removeClass('executing').addClass('executed').addClass('ok');
                
                next_action();
              }, 
              'error' : function(response) {
                clearInterval(interval);
                next_action_item.text(original_text + ' (' + response.responseText + ')').removeClass('executing').addClass('executed').addClass('error');
              }
            })
          } else {
            wrapper.find('ul#upgrader_actions_list').after('<p>All done!</p>');
          } // if
        };
        
        next_action();
      });
    },
    
    /**
     * Register validator for given section name
     * 
     * @param String section_name
     * @param Function with_function
     */
    'validate' : function(section_name, with_function) {
      return this.each(function() {
        var wrapper = $(this);
        
        var validators = wrapper.data('validators');
        
        if(typeof(validators) != 'object') {
          validators = {};
        } // if
        
        validators[section_name] = with_function;
        
        wrapper.data('validators', validators);
      });
    }
  };

  // Definition and dispatcher
  $.fn.upgrader = function(method) {
    if(methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if(typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      throw 'Method ' +  method + ' does not exist on jQuery.upgrader';
    } // if    
  
  };

})(jQuery);

/**
 * Validate login parameters
 * 
 * @param Object form_data
 * @param Array validation_errors
 * @return boolean
 */
var validate_login_parameters = function(form_data, validation_errors) {
  var form = $(this);
  
  form_data['login[email]'] = jQuery.trim(form.find('input#login_email_input').val());
  form_data['login[pass]'] = jQuery.trim(form.find('input#login_pass_input').val());
  
  if(form_data['login[email]']) {
    if(!form_data['login[email]'].match(/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/)) {
      validation_errors.push('Email address is not valid');
    } // if                                                                 
  } else {
    validation_errors.push('Email address for administrator account is required');
  } // if
  
  if(form_data['login[pass]'] == '') {
    validation_errors.push('Password for administrator account is required');  
  } // if
  
  return validation_errors.length == 0;
};