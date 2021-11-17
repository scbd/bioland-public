;(function ($) {
  'use strict';

  $(function() {
    var facetCollapse = $('.js-facet-collapse');

    if(facetCollapse.length) {
        facetCollapse.each(function () {
            var self = $(this);
            var actives = self.find('.is-active');
            var checked = self.find('input[checked="checked"]');
            if(actives.length || checked.length) {
                self.collapse('show');
            }
        });
    }
  });
})(jQuery);
