
$(function(){

    // Keep a mapping of url-to-container for caching purposes.
    var cache = {
        // If url is '' (no fragment), display this div's content.
        '': $('.bbq-default')
    };

    // Bind an event to window.onhashchange that, when the history state changes,
    // gets the url from the hash and displays either our cached content or fetches
    // new content to be displayed.
    $(window).bind('hashchange', function(e) {

        // Get the hash (fragment) as a string, with any leading # removed. Note that
        var hash = window.location.hash.substring(1);

        if (hash !== '') {
            $('ul#main-menu li').each(function(i) {
                $(this).removeClass('active');
            });

            $('#link-'+hash).addClass('active');
        }

    })

    // Since the event is only triggered when the hash changes, we need to trigger
    // the event now, to handle the hash the page may have loaded with.
    $(window).trigger('hashchange');
});
