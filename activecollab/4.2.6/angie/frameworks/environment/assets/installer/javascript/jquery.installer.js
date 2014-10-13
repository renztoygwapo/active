/**
 * Installer behavior
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
        
        wrapper.find('div.installer_section div.body').hide();
        wrapper.installer('show_section', wrapper.find('div.installer_section:first').attr('installer_section'));
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
              var installer_data = wrapper.data('installer_data');
              if(typeof(installer_data) == 'object' && typeof(installer_data)) {
                for(var i in installer_data) {
                  data[i] = installer_data[i];
                } // for
              } // if
              
              // And system data
              data['submitted'] = 'submitted';
              data['installer_section'] = section.attr('installer_section');
              
              // And now submit..
              $.ajax({
                'url' : form.attr('action'), 
                'type' : 'post', 
                'data' : form_data, 
                'success' : function(response) {
                  wrapper.data('installer_data', installer_data);
                  
                  section.removeClass('has_errors');
                  section.find('div.body').empty().append(response);
                  
                  var next_section = section.next();
                  if(next_section.length > 0 && next_section.is('div.installer_section')) {
                    
                    // Remember data from this step
                    var installer_data = wrapper.data('installer_data');
                    
                    if(typeof(installer_data) != 'object') {
                      installer_data = {};
                    } // if
                    
                    for(var i in form_data) {
                      installer_data[i] = form_data[i]
                    } // for
                    
                    wrapper.data('installer_data', installer_data);
                    
                    // And show the next one
                    wrapper.installer('show_section', next_section.attr('installer_section'), true);
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
        
        var section = wrapper.find('div.installer_section[installer_section=' + section_name + ']');
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
  $.fn.installer = function(method) {
    if(methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if(typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    } else {
      throw 'Method ' +  method + ' does not exist on jQuery.installer';
    } // if    
  
  };

})(jQuery);

/**
 * Validate database parameters validator (used by environment framework)
 * 
 * @param Object form_data
 * @param Array validation_errors
 * @return boolean
 */
var validate_database_parameters = function(form_data, validation_errors) {
  var form = $(this);
  
  form_data['database[host]'] = jQuery.trim(form.find('input#database_host_input').val());
  form_data['database[user]'] = jQuery.trim(form.find('input#database_user_input').val());
  form_data['database[pass]'] = form.find('input#database_pass_input').val();
  form_data['database[name]'] = jQuery.trim(form.find('input#database_name_input').val());
  form_data['database[prefix]'] = jQuery.trim(form.find('input#database_prefix_input').val());
  
  if(form_data['database[host]'] == '') {
    validation_errors.push('Database host name is required');
  } // if
  
  if(form_data['database[user]'] == '') {
    validation_errors.push('Database user name is required');  
  } // if
  
  if(form_data['database[name]'] == '') {
    validation_errors.push('Database name is required');
  } // if
  
  return validation_errors.length == 0;
}; // validate_database_parameters

/**
 * Validate admin page
 * 
 * @param Object form_data
 * @param Array validation_errors
 * @return boolean
 */
var validate_admin_parameters = function(form_data, validation_errors) {
  var form = $(this);
  
  form_data['admin[email]'] = jQuery.trim(form.find('input#admin_email_input').val());
  form_data['admin[pass]'] = jQuery.trim(form.find('input#admin_pass_input').val());
  form_data['license[accepted]'] = form.find('input#license_accepeted_input:checked').length > 0;
  form_data['license[help_improve]'] = form.find('input#help_improve_input:checked').length > 0;

  if(form_data['admin[email]']) {
    if(!form_data['admin[email]'].match(/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/)) {
      validation_errors.push('Email address is not valid');
    } // if                                                                 
  } else {
    validation_errors.push('Email address for administrator account is required');
  } // if
  
  if(form_data['admin[pass]'] == '') {
    validation_errors.push('Password for administrator account is required');  
  } // if
  
  if(!form_data['license[accepted]']) {
    validation_errors.push('License agreement not accepted');
  } // if
  
  return validation_errors.length == 0;
};