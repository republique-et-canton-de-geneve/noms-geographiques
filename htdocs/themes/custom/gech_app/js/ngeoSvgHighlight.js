/**
 * @file
 * It allows to highlight the current commune on the SVG map.
 * We use the path alias set on an HTML data attribute to target the SVG path
 * and then we color it with CSS.
 *
 */

(function ($) {
  var path = $("#commune-map").data("alias");
  var $communeLinks = $('a[href="' + path + '"]');

  if ($communeLinks && $communeLinks.length > 0) {
    $communeLinks.each(function(index) {

      if ($(this).children('polygon') ) {
        $(this).children('polygon').css("fill", '#8BCE7E');
      }

      if ($(this).children('path') ) {
        $(this).children('path').css("fill", '#8BCE7E');
      }
    })
  }
}(jQuery));
