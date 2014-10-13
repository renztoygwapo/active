/**
 * Async process implementation
 *
 * Settings:
 *
 * - success - Callback that is executed when successfully execute all steps
 * - error - Callback that is executed when we stumble on an error
 */
jQuery.fn.asyncProcess = function(settings) {
  var settings = jQuery.extend({
    'success' : null,
    'error' : null,
    'on_step_success' : null,
    'on_step_error': null
  }, settings);
  
  return this.each(function() {
    var list = $(this);
    var steps = [];
    
    var counter = 1;
    $(this).find('li.step').each(function() {
      var item = $(this);
      
      if(item.is('li.executed')) {
        item.prepend('<img src="' + App.Wireframe.Utils.indicatorUrl('ok') + '">');
      } else {
        item.attr('id', list.attr('id') + '_item_' + counter).prepend('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">');
        
        steps.push({
          'id' : item.attr('id'),
          'url' : item.attr('step_url')
        });
        
        counter++;
      } // if
    });
    
    var last_step_response;
    
    /**
     * Execute process step
     *
     * @param Integer step_num
     */
    var execute_step = function(step_num) {
      
      // Done with the last step!
      if(step_num >= steps.length) {
        if(settings['success']) {
          settings['success'].apply(list[0], [ last_step_response, step_num ]);
        } // if
        
      // We need to execute this step
      } else {
        var item = $('#' + steps[step_num]['id']).addClass('executing');
        
        $.ajax({
          'url' : steps[step_num]['url'],
          'type' : 'post',
          'data' : { 'submitted' : 'submitted' },
          'success' : function(response) {
            last_step_response = response; // Remember last step response
            
            item.find('img').attr('src', App.Wireframe.Utils.indicatorUrl('ok'));
            item.removeClass('executing').addClass('executed').addClass('ok');
            if(settings['on_step_success']) {
            	settings['on_step_success'].apply(response,[response]);
            }
            execute_step(step_num + 1);
          }, 
          'error' : function(response) {
            last_step_response = response; // Remember last step response
            item.find('img').attr('src', App.Wireframe.Utils.indicatorUrl('error'));
            item.removeClass('executing').addClass('executed').addClass('error');
            if(settings['on_step_error']) {
            	settings['on_step_error'].apply(response,[response]);
            }
          }
        });
      } // if
    };
    
    execute_step(0);
  });
};