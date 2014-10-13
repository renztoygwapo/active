/**
 * Highlight fade implementation using UI effects
 */
jQuery.fn.highlightFade = function() {
  return this.each(function () {
    $(this).effect("highlight", {}, 1000);
  });
};