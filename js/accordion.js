/**
 * @file
 * JavaScript for the accordion functionality.
 */
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.fireworksAccordion = {
    attach: function (context, settings) {
      $('.fireworks-accordion-button', context).on('click', function() {
        $(this).toggleClass('accordion-button-active');

        // Get the content element (next sibling of the parent paragraph)
        var content = $(this).parent().next('.fireworks-accordion-content');

        // Toggle the content visibility
        if (content.css('max-height') !== '0px' && content.css('max-height') !== 'none') {
          content.css('max-height', '0');
        } else {
          content.css('max-height', content.prop('scrollHeight') + 'px');
        }
      });
    }
  };
})(jQuery, Drupal);
