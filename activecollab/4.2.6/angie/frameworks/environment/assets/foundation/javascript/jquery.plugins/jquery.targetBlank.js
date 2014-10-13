/**
 * Open links in new window
 */
jQuery.fn.targetBlank = function() {
  return this.each(function() {
    var link = $(this);
    
    link.click(function() {
      window.open(link.attr('href'));
      return false;
    });
  });
};