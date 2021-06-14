loadDismissibleNotices = function() {
  var $ = jQuery;

  $(".notice.is-dismissible").each(function() {
    var b = $(this)
      , c = $('<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>');
      c.on("click.wp-dismiss-notice", function($) {
        $.preventDefault(),
          b.fadeTo(100, 0, function() {
            b.slideUp(100, function() {
              b.remove()
            })
          })
      }),
      b.append(c)
  });
}
