(function($) {
    'use strict';

    $(function() {

        $("body").removeClass("preload");

        $(document).on('click', '[data-dropdown-prevent-closing]', function(event) {
          event.stopPropagation();
        });

        $('#block-biotheme-main-menu').on('click', '[data-toggle=dropdown]', function(event) {
          event.preventDefault();
          event.stopPropagation();
          var $parent = $(this).parent();
          $parent
            .siblings()
                .removeClass('open')
                .find('.dropdown-toggle').attr('aria-expanded', 'false')
                .end()
            .end()
            .toggleClass('open');
        });

        // Only open dropdowns on interaction with toggle
        $('.region-sidebar-first, .region-sidebar-second').on('click', '.dropdown-toggle', function (event) {
            var $this = $(this);
            var $dropdown = $this.parent();

            $dropdown
                // .siblings()
                //     .removeClass('open')
                //     .find('.dropdown-toggle').attr('aria-expanded', 'false')
                //     .end()
                // .end()
                .toggleClass('open');

            $this.attr('aria-expanded',
               $this.attr('aria-expanded') == 'false' ? 'true' : 'false'
            );

        });

        $('.region-sidebar-first, .region-sidebar-second').on('click', function (e) {
            var $this = $(this);
            var $dropdown = $('.dropdown', $this);

            if (!$dropdown.is(e.target)
                && $dropdown.has(e.target).length === 0
                && $('.open').has(e.target).length === 0
            ) {

                $dropdown.removeClass('open');
                $('.dropdown-toggle', $this).attr('aria-expanded', 'false');

            }
        });

        if(typeof Blazy !== 'undefined') {
            var bLazy = new Blazy();
            $('.slick').on('afterChange', function(event, slick, currentSlide, nextSlide) {
                bLazy.revalidate();
            });
        }
    });


}(jQuery));
