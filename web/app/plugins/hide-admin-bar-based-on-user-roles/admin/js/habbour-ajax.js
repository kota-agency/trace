jQuery(document).ready(function($) {
    if( $('.habbour_install').length > 0 ){
        $('.habbour_install').click(function(e) {
            e.preventDefault();
            const loader = $('#habbour-loader');
            $('.habbour_install').hide();
            loader.show();

            // alert('Your request is being processed. Please wait a moment. No need to refresh or go back.');
            $.ajax({
                url: habbourAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'habbour_action',
                    nonce: habbourAjax.habbour_nonce
                },
                success: function(response) {
                    console.log(response);
                    loader.hide();
                    alert('UltimaKit For WP installed and activated successfully.');
                    setTimeout(function(){
                        window.location.reload();
                    },2000);
                },
                error: function() {
                    loader.hide();
                    alert('Error occurred');
                }
            });
        });
    }

    if( $('.habbour_hide_notice').length > 0 ){
        $('.habbour_hide_notice').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: habbourAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'habbour_hide_notice',
                    nonce: habbourAjax.habbour_nonce
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function() {
                    alert('Error occurred');
                }
            });
        });
    }

    
});