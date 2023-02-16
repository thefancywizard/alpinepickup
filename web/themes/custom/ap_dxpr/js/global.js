(function ($, Drupal) {
    $('body.page-node-7 .fx').each(function(index, element) {
        var link = $(".views-field-title h4 a", this);
        $(".views-field-variations-target-id span.field-content", this).wrapInner("<a href=" + link.attr('href') + "></a>");
    });
})(jQuery, Drupal);