<div id="nitropack-container" class="wrap">
    <div id="heading">
        <h2>NitroPack.io</h2>
    </div>

    <form method="post" action="options.php" name="form">
        <?php settings_fields( NITROPACK_OPTION_GROUP ); ?>
        <?php do_settings_sections( NITROPACK_OPTION_GROUP ); ?>

        <ul class="nav nav-tabs nav-tab-wrapper">
            <li><a class="nav-tab active" href="#dashboard" data-toggle="tab">Dashboard</a></li>
            <li><a class="nav-tab" href="#help" data-toggle="tab">Help</a></li>
	        <li><a class="nav-tab" href="#diag" data-toggle="tab">Diagnostics</a></li>
        </ul>		
        <div class="tab-content" style="display:block">
            <div id="dashboard" class="tab-pane hidden">
                <?php require_once "dashboard.php"; ?>
            </div>
            <div id="help" class="tab-pane hidden">
                <?php require_once "help.php"; ?>
            </div>
            <div id="diag" class="tab-pane hidden">
                <?php require_once "diag.php"; ?>
            </div>
        </div>
    </form>
</div>
<?php if (NITROPACK_SUPPORT_BUBBLE_VISIBLE) { ?>
<div class="nitropack-support-icon" data-toggle="tooltip" title="Get help">
<a href="<?php echo NITROPACK_SUPPORT_BUBBLE_URL; ?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-life-ring"></i></a>
</div>
<?php } ?>
<script>
(function($) {
    window.Notification = (_ => {
        var timeout;

        var display = (msg, type) => {
            clearTimeout(timeout);
            $('#nitropack-notification').remove();

            $('[name="form"]').prepend('<div id="nitropack-notification" class="notice notice-' + type + '" is-dismissible"><p>' + msg + '</p></div>');

            timeout = setTimeout(_ => {
                $('#nitropack-notification').remove();
            }, 10000);
            loadDismissibleNotices();
        }

        return {
            success: msg => {
                display(msg, 'success');
            },
            error: msg => {
                display(msg, 'error');
            },
            info: msg => {
                display(msg, 'info');
            },
            warning: msg => {
                display(msg, 'warning');
            }
        }
    })();

    const clearCacheHandler = clearCacheAction => {
        return function(success, error) {
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: "nitropack_" + clearCacheAction + "_cache"
                },
                dataType: 'json',
                beforeSend: function() {
                    Notification.info("Loading. Please wait...");
                },
                success: function(data) {
                    Notification[data.type](data.message);

                    cacheEvent = new Event("cache." + clearCacheAction + ".success");
                    window.dispatchEvent(cacheEvent);
                }
            });
        };
    }

    $(window).on("load", _ => {
        //Remove styles from jobcareer and jobhunt plugins since they break our layout. They should not be loaded on our options page anyway.
        $('link[href*="jobcareer"').remove();
        $('link[href*="jobhunt"').remove();

        $("#dashboard").addClass("show active");
        window.addEventListener('cache.invalidate.request', clearCacheHandler("invalidate"));
        window.addEventListener('cache.purge.request', clearCacheHandler("purge"));

        NitroPack.QuickSetup.setChangeHandler(async function(value, success, error) {
            success(value);
        });
    });

    $("#nitro-restore-connection-btn").on("click", function() {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: "nitropack_reconfigure_webhooks"
            },
            dataType: 'json',
            beforeSend: function() {
                $("#nitro-restore-connection-btn").attr("disabled", true).html("<i class='fa fa-refresh fa-spin'></i>");
            },
            success: function(data) {
                if (!data.status || data.status != "success") {
                    if (data.message) {
                        alert("Error: " + data.message);
                    } else {
                        alert("Error: We were unable to restore the connection. Please contact our support team to get this resolved.");
                    }
                } else {
                    $("#nitro-restore-connection-btn").attr("disabled", true).html("<i class='fa fa-check'></i>");
                }
            },
            complete: function() {
                location.reload();
            }
        });
    });
})(jQuery);
</script>
