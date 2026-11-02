/**
 * Elementor frontend/editor bootstrap for the Logo Showcase widgets.
 *
 * The shortcode markup is initialised on document ready by
 * wplogoshowcase.js. Elementor injects widgets after that point (and
 * re-injects them on every edit), so each widget re-runs the same
 * initialisation for its own container here.
 */
(function ($) {
    'use strict';

    function equalHeight($container) {
        var maxHeight = 0;
        var $items = $container.children('div.rt-row').find('.rt-equal-height');

        $items.height('auto');

        $items.each(function () {
            var height = $.fn.actual ? $(this).actual('outerHeight') : $(this).outerHeight();

            if (height > maxHeight) {
                maxHeight = height;
            }
        });

        $items.css('height', maxHeight + 'px');
    }

    function initWidget($scope) {
        var $container = $scope.find('.rt-container-fluid.rt-wpls');

        if (!$container.length) {
            return;
        }

        $container.each(function () {
            var $this = $(this);
            var $carousel = $this.find('.rt-row.wpls-carousel');

            if ($carousel.length && $.fn.slick) {
                if ($carousel.hasClass('slick-initialized')) {
                    $carousel.slick('unslick');
                }

                // Match slick's direction to the stylesheet's: see wplogoshowcase.js.
                $carousel.each(function () {
                    var data = $(this).data('slick');
                    if (data && $(this).closest('[dir="rtl"]').length) {
                        data.rtl = true;
                    }
                });

                $carousel.slick();
            }

            equalHeight($this);
        });
    }

    $(window).on('elementor/frontend/init', function () {
        if (!window.elementorFrontend || !elementorFrontend.hooks) {
            return;
        }

        elementorFrontend.hooks.addAction('frontend/element_ready/rt-logo-grid.default', initWidget);
        elementorFrontend.hooks.addAction('frontend/element_ready/rt-logo-slider.default', initWidget);
    });
})(jQuery);
