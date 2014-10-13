/**
 * Object inspector
 */
(function($) {
  
  /**
   * Public methods
   * 
   * @var Object
   */
  var public_methods = {
      
    /**
     * Initialise object inspector
     * 
     * @param Object options
     * @returns jQuery
     */
    init : function(options) {
      return this.each(function () {
        init.apply(this);
      });
    },
    
    /**
     * starts the form processing
     */
    start_processing : function () {
      return this.each(function () {
        start_processing.apply(this);
      });
    },
    
    /**
     * stops the form processing
     */
    stop_processing : function () {
      return this.each(function () {
        stop_processing.apply(this);
      });
    }
  };
  
  /**
   * initialize form utils on form
   */
  var init = function () {
    if (!this.formutils_data) {
      this.formutils_data = {
          'processing' : false
      }; // if
    } // if
  };
  
  /**
   * Start processing
   * 
   * @param void
   */
  var start_processing = function () {    
    // if we are already processing the form, no need for double submission
    if (this.formutils_data.processing) {
      return true;
    } // if
    
    var form = $(this);
    var fields_wrapper = form.find('.fields_wrapper').hide();
    var buttons_wrapper = form.find('.button_holder').hide();
    
    var processing_curtain = $('<div class="form_processing_curtain"></div>').insertAfter(buttons_wrapper).css('background-image', 'url(' + App.Wireframe.Utils.indicatorUrl('big') + ')');
    
    this.formutils_data.processing = true;
  };
  
  /**
   * Stop form processing
   */
  var stop_processing = function () {
    // if we are already not processing the form, no need for canceling the processing
    if (!this.formutils_data.processing) {
      return true;
    } // if
    
    var form = $(this);    
    var fields_wrapper = form.find('.fields_wrapper').show();
    var buttons_wrapper = form.find('.button_holder').show();
    
    var processing_curtain = form.find('.form_processing_curtain').remove();

    this.formutils_data.processing = false;
  };
  
  
  ////////////////////////////////////////////////////////// PLUGIN INITIAL DATA ///////////////////////////////////////////////////////////  
  
  /**
   * Plugin name
   * 
   * @var String
   */
  var plugin_name = 'formUtils';
  
  /**
   * Initial inspector settings
   * 
   * @var Object
   */
  var settings = {
  };
        
  ///////////////////////////////////////////////////////// PLUGIN FRAMEWORK STUFF /////////////////////////////////////////////////////////
  /**
   * Register jQuery plugin and handle public methods
   * 
   * @param mixed method
   * @return null
   */
  $.fn[plugin_name] = function(method) { 
    if (public_methods[method]) {
      public_methods.init.apply(this);
      return public_methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
      return public_methods.init.apply(this, arguments);
    } else {
      $.error('Method ' +  method + ' does not exist in jQuery.' + plugin_name);
    } // if
  };

})(jQuery);