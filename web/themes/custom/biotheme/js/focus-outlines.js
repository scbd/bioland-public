(function($) {
    'use strict';

    $(function() {
        var $body = $('body');

        // Shows outlines if navigating with keyboard
        document.addEventListener('keydown', function(e) {
            // if (e.keyCode === 9) {
            $body.addClass('show-focus-outlines');
            // }
        });
        ['mousedown', 'touchstart'].forEach(function (event) {
            document.addEventListener(event, function() {
                $body.removeClass('show-focus-outlines');
            });
        });
    });
}(jQuery));
