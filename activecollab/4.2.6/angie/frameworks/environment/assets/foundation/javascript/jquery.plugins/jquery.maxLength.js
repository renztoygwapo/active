(function($) {

    /**
     * Limit max length of the textarea field
     */
    $.fn.maxlength = function() {
  
      this.each(function () {
        var textarea = $(this);
        
        /// if browser is mozilla or webkit then we have native support for maxlength (html5)
        if ($.browser.mozilla || $.browser.webkit) {
          return false;
        } // if
        
        // if element is not a textarea or does not contains maxlength limit we don't need to bind anything to it
        if (!textarea.is('textarea') || !textarea.attr('maxlength')) {
          return false;
        } // if
        
        textarea.keypress(function (event) {
          var key = event.which;
          
          if((key >= 33 || key == 13) && !event.metaKey && !event.ctrlKey) {
            var maxLength = textarea.attr('maxlength');
            var length = textarea.val().length;
            if(length >= maxLength) {
                event.preventDefault();
            } // if
          }
        });
      });
    };
    
})(jQuery);