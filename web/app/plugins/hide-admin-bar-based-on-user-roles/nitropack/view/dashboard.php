<div>
  <div class="row">
    <div class="col-md-12">
      <?php nitropack_display_admin_notices(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="optimizations-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Optimized Pages<span id="pending-optimizations-section" class="pull-right" style="display:none;font-size: 12px;color: #28a745">Processing <span id="pending-optimizations-count">X</span> page(s) in the background&nbsp;&nbsp;<i class="fa fa-spinner fa-spin"></i></span></h5>
            <div class="row mt-4" data-hideable>
              <div id="optimized-pages"><span data-optimized-pages-total>0</span></div>
              <div id="last-cache-purge" class="text-secondary">Last cache purge: <span data-last-cache-purge>Never</span></div>
              <div id="last-cache-purge-reason" class="text-secondary">Reason: <span data-purge-reason>Unknown</span></div>
            </div>
            <div class="row mt-4 optimizations-hidden" data-hideable>
              <div class="optimizations-subcount"><span data-optimized-pages-mobile>0</span> mobile pages</div>
              <div class="optimizations-subcount"><span data-optimized-pages-tablet>0</span> tablet pages</div>
              <div class="optimizations-subcount"><span data-optimized-pages-desktop>0</span> desktop pages</div>
            </div>
            <div class="row mt-5 justify-content-center">
              <button id="optimizations-purge-cache" class="btn btn-light btn-outline-secondary btn-widget-optimizations">Purge Cache</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="plan-details-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Plan</h5>
            <div class="mt-3">
              <h5 class="font-weight-lighter"><span data-plan-title>Unknown</span> <a target="_blank" href="https://nitropack.io/user/billing" class="btn btn-primary btn-sm ml-3">Manage plan</a></h5>
            </div>
            <ul class="list-group list-group-flush" id="plan-quotas">
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">Next Reset <span data-next-reset>No ETA</span></li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">Next Billing <span data-next-billing>No ETA</span></li>
            </ul>
            <p class="mb-0 mt-2"><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> You will be notified if you approach the plan resource limit</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="quicksetup-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Optimization Mode</h5>
            <p><small>Slide to change your settings. This will update the level of optimization.</small></p>

            <div id="range-container">
              <div id="labels"></div>
              <div id="range-element">
                <div id="divisors"></div>
                <input id="range" type="range" min="1" max="5" step="1" value="0" />
              </div>
            </div>

            <div id="description">
              <div class="text dummy">
                <h6 class="text-success">Dummy</h6>
                <p><small></small></p>
              </div>
              <div class="text standard">
                <h6 class="text-info">Standard</h6>
                <p><small>A pre-defined configuration without much regard for a high score. Enough to get you up and running.</small></p>
              </div>
              <div class="text medium">
                <h6 class="text-success">Medium</h6>
                <p><small>Well-balanced and suitable for most cases.</small></p>
              </div>
              <div class="text strong">
                <h6 class="text-warning">Strong</h6>
                <p><small>Includes advanced features like automatic image lazy loading and font definition modification. May cause issues in some cases.</small></p>
              </div>
              <div class="text ludicrous">
                <h6 class="text-danger">Ludicrous</h6>
                <p><small>A pre-defined configuration aiming to achieve the highest possible score. In some cases, it may cause issues with your site.</small></p>
              </div>
              <div class="text custom">
                <h6>Manual</h6>
                <p><small>Use your own settings. <a id="manual-settings-url" href="javascript:void(0);" target="_blank">Click here</a> to configure them.</small></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="settings-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Settings</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>Cache Warmup</br>
                  <small>Learn more about this feature <a href="https://support.nitropack.io/hc/en-us/articles/1500002555901-Cache-Warmup-WordPress-" target="_blank" rel="noreferrer noopener">here</a></small>
                </span>
                <span id="loading-warmup-status">
                  Loading cache warmup status&nbsp;&nbsp;<i class="fa fa-refresh fa-spin" style="color: var(--blue);"></i>
                </span>
                <span id="warmup-toggle" style="display: none;">
                  <label id="warmup-status-slider" class="switch">
                    <input type="checkbox" id="warmup-status">
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>Safe Mode</br>
                  <small>Learn more about this feature <a href="https://support.nitropack.io/hc/en-us/articles/360060910574-Safe-Mode" target="_blank" rel="noreferrer noopener">here</a></small>
                </span>
                <span id="loading-safemode-status">
                  Loading safe mode status&nbsp;&nbsp;<i class="fa fa-refresh fa-spin" style="color: var(--blue);"></i>
                </span>
                <span id="safemode-toggle" style="display: none;">
                  <label id="safemode-status-slider" class="switch">
                    <input type="checkbox" id="safemode-status">
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="detecting-compression" style="display: none;">Testing current compression status&nbsp;&nbsp;<a href="javascript:void(0);"><i class="fa fa-refresh fa-spin" style="color: var(--blue);"></i></a></span>
                <span id="detected-compression">HTML Compression&nbsp;&nbsp;<a href="javascript:void(0);" id="compression-test-btn" data-toggle="tooltip" data-placement="top" title="Automatically detect whether compression is needed"><i class="fa fa-refresh" style="color: var(--blue);"></i></a></span>
                <span>
                  <label class="switch">
                    <input type="checkbox" id="compression-status" <?php echo (int)$enableCompression === 1 ? "checked" : ""; ?>>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>
                  <a href="javascript:void(0);" class="btn btn-danger text-white" id="disconnect-btn"><i class="fa fa-power-off text-white"></i>&nbsp;&nbsp;Disconnect</a>
                </span>
              </li>
            </ul>
            <p class="mb-0 mt-2"><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> You can further configure how NitroPack's optimization behaves through your account at <a href="https://nitropack.io/" target="_blank">https://nitropack.io/&nbsp;&nbsp;<i class="fa fa-external-link"></i></a>.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="automations-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Automated Behavior</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>Purge affected cache when content is updated or published</span>
                <span id="auto-purge-toggle">
                  <label id="auto-purge-status-slider" class="switch">
                    <input type="checkbox" id="auto-purge-status" <?php if ($autoCachePurge) echo "checked"; ?>>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>Select what post/page types get optimized</span>
                <span id="cacheable-post-types-btn">
                  <a href="javascript:void(0);" class="btn btn-light btn-outline-secondary" data-toggle="modal" data-target="#cacheable-post-types-modal"><i class="fa fa-cog"></i></a>
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Post Types Modal -->
<div class="modal" id="cacheable-post-types-modal" tabindex="-1" role="dialog" aria-labelledby="cacheable-post-types-title" aria-hidden="true" data-backdrop="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cacheable-post-types-title">Configure page types that can be optimized</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body nitropack-scrollable-modal-body">
        <ul class="list-group list-group-flush">
          <?php foreach ($objectTypes as $objectType) {?>
          <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
            <span><?php echo $objectType->label; ?></span>
            <span id="post-type-<?php echo $objectType->name; ?>-toggle">
              <label id="post-type-<?php echo $objectType->name; ?>-status-slider" class="switch">
                <input class="cacheable-post-type" name="<?php echo $objectType->name; ?>" type="checkbox" id="post-type-post-status" <?php if (in_array($objectType->name, $cacheableObjectTypes)) echo 'checked'; ?>>
                <span class="slider"></span>
              </label>
            </span>
          </li>
          <?php if (!empty($objectType->taxonomies)) {?>
            <?php foreach ($objectType->taxonomies as $taxonomyType) {?>
            <li class="list-group-item px-0 d-flex justify-content-between align-items-center list-group-item-indented border-0">
              <span><?php echo $taxonomyType->label; ?></span>
              <span id="post-type-<?php echo $taxonomyType->name; ?>-toggle">
                <label id="post-type-<?php echo $taxonomyType->name; ?>-status-slider" class="switch">
                  <input class="cacheable-post-type" name="<?php echo $taxonomyType->name; ?>" type="checkbox" id="post-type-post-status" <?php if (in_array($taxonomyType->name, $cacheableObjectTypes)) echo 'checked'; ?>>
                  <span class="slider"></span>
                </label>
              </span>
            </li>
            <?php }?>
          <?php }?>
          <?php }?>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-cacheable-post-types">Save changes&nbsp;&nbsp;<i class="fa fa-spinner fa-spin d-none"></i></button>
      </div>
    </div>
  </div>
</div>
<script>
  ($ => {
    var getOptimizationsTimeout = null;

    $(window).on("load",function() {
      $("#optimizations-widget").cardOverlay("loading", {message: "Loading optimizations data"});
      $("#plan-details-widget").cardOverlay("loading", {message: "Loading plan data"});
      $("#quicksetup-widget").cardOverlay("loading", {message: "Loading settings"});
      getOptimizations();
      getPlan();
      getQuickSetup();

      <?php if ($checkedCompression != 1) { ?>
        autoDetectCompression();
      <?php } ?>
    });

    $(document).on('click', '[data-hideable]', function(e) {
      e.preventDefault();

      $('[data-hideable]').removeClass('optimizations-hidden');

      $(this).addClass('optimizations-hidden');
    });

    $(document).on('click', '#optimizations-invalidate-cache', function(e) {
      e.preventDefault();
      //Overlay.loading("Invalidating cache...");

      invalidateEvent = new Event("cache.invalidate.request");
      window.dispatchEvent(invalidateEvent);
    });

    $(document).on('click', '#optimizations-purge-cache', function(e) {
      e.preventDefault();
      //Overlay.loading("Purging cache...");

      purgeEvent = new Event("cache.purge.request");
      window.dispatchEvent(purgeEvent);
    });

    $("#btn-run-warmup").on("click", function(e) {
      runWarmup();
    })

    $("#btn-stop-warmup").on("click", function(e) {
      disableWarmup();
    })

    var estimateWarmup = (id, retry) => {
      id = id || null;
      retry = retry || 0;
      if (!id) {
        $("#settings-widget").cardOverlay("loading", {message: "Estimating optimizations usage"});
        //$("#estimation-spinner").show();
        //$("#warmup-status-slider").hide();

        $.post(ajaxurl, {
          action: 'nitropack_estimate_warmup'
        }, function(response) {
          var resp = JSON.parse(response);
          if (resp.type == "success") {
            setTimeout( (function(id){
              estimateWarmup(id);
            })(resp.res), 1000 );
          } else {
            $("#settings-widget").cardOverlay("error", {message: "Warmup estimation failed", timeout: 3000});
          }
        });
      } else {
        $.post(ajaxurl, {
          action: 'nitropack_estimate_warmup',
          estId: id
        }, function(response) {
          var resp = JSON.parse(response);
          if (resp.type == "success") {
            if (isNaN(resp.res) || resp.res == -1) { // Still calculating
              if (retry >= 10) {
                $("#settings-widget").cardOverlay("error", {message: "Warmup estimation failed. Please try again or contact support if the issue persists.", dismissable: true});
              } else {
                setTimeout( (function(id, retry){
                  estimateWarmup(id, retry);
                })(id, retry+1), 1000 );
              }
            } else {
              if (resp.res == 0) {
                $("#settings-widget").cardOverlay("notify", {message: "We could not find any links for warming up on your home page", timeout: 3000});
              } else {
                var confirmHtml = '<p>Enabling cache warmup will use ' + resp.res + ' page optimizations. Would you like to continue?</p>';
                confirmHtml += '<p><a href="javascript:void(0);" onclick="rejectWarmup()" class="btn btn-default btn-sm">No</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="confirmWarmup()" class="btn btn-success btn-sm">Yes</p></a>';
                $("#settings-widget").cardOverlay("notify", {message: confirmHtml});
              }
            }
          } else {
            $("#settings-widget").cardOverlay("error", {message: "Warmup estimation failed", timeout: 3000});
          }
        });
      }
    }

    window.confirmWarmup = function() {
      $("#settings-widget").cardOverlay("loading", {message: "Enabling warmup"});
      enableWarmup();
    }

    window.rejectWarmup = function() {
      $("#settings-widget").cardOverlay("clear");
    }

    var enableWarmup = () => {
      jQuery.post(ajaxurl, {
        action: 'nitropack_enable_warmup'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.type == "success") {
          $("#settings-widget").cardOverlay("clear");
          $("#warmup-status").attr("checked", true);
        } else {
          setTimeout(enableWarmup, 1000);
        }
      });
    }

    var disableWarmup = () => {
      jQuery.post(ajaxurl, {
        action: 'nitropack_disable_warmup'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.type == "success") {
          // Success notification
        } else {
          // Error notification
        }
      });
    }

    var runWarmup = () => {
      jQuery.post(ajaxurl, {
        action: 'nitropack_run_warmup'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.type == "success") {
          // Success notification
        } else {
          // Error notification
        }
      });
    }

    var enableSafemode = () => {
      jQuery.post(ajaxurl, {
        action: 'nitropack_enable_safemode'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.type == "success") {
            $("#safemode-status").attr("checked", true);
          // Success notification
        } else {
            $("#safemode-status").attr("checked", false);
          // Error notification
        }
      });
    }

    var disableSafemode = () => {
      jQuery.post(ajaxurl, {
        action: 'nitropack_disable_safemode'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.type == "success") {
          // Success notification
        } else {
          // Error notification
        }
      });
    }

    var getOptimizations = _ => {
      $.ajax({
        url: '<?php echo $optimizationDetailsUrl; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          $('[data-last-cache-purge]').text(data.last_cache_purge.timeAgo);
          if (data.last_cache_purge.reason) {
            $('[data-purge-reason]').text(data.last_cache_purge.reason);
            $('#last-cache-purge-reason').show();
          } else {
            $('#last-cache-purge-reason').hide();
          }

          if (data.pending_count) {
              $("#pending-optimizations-count").text(data.pending_count);
              $("#pending-optimizations-section").show();
          } else {
              $("#pending-optimizations-section").hide();
          }

          $('[data-optimized-pages-desktop]').text(data.optimized_pages.desktop);
          $('[data-optimized-pages-mobile]').text(data.optimized_pages.mobile);
          $('[data-optimized-pages-tablet]').text(data.optimized_pages.tablet);
          $('[data-optimized-pages-total]').text(data.optimized_pages.total);

          $("#optimizations-widget").cardOverlay("clear");
        },
        error: function() {
          //Overlay.error("An unexpected error has occurred.", 10000, true);
        },
        complete: function() {
          if (!getOptimizationsTimeout) {
            getOptimizationsTimeout = setTimeout(function() {getOptimizationsTimeout = null; getOptimizations();}, 60000);
          }
        }
      })
    }

    var getPlan = _ => {
      $.ajax({
        url: '<?php echo $planDetailsUrl; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          $('[data-plan-title]').text(data.plan_title);
          $('[data-next-billing]').text(data.next_billing ? data.next_billing : 'N/A');
          $('[data-next-reset]').text(data.next_reset ? data.next_reset : 'N/A');

          for (prop in data) {
              if (prop.indexOf("show_") === 0) continue;
              if (prop.indexOf("label_") === 0) continue;
              if (prop.indexOf("max_") === 0) continue;
              if (
                  typeof data["show_" + prop] != "undefined" &&
                  data["show_" + prop] &&
                  typeof data["label_" + prop] != "undefined" &&
                  typeof data["max_" + prop] != "undefined"
              ) {
                  let propertyLabel = data["label_" + prop];
                  let propertyValue = data[prop];
                  let propertyLimit = data["max_" + prop];
                  $("#plan-quotas").append('<li class="list-group-item px-0 d-flex justify-content-between align-items-center">' + propertyLabel + ' <span><span data-optimizations>' + propertyValue + '</span> out of <span data-max-optimizations>' + propertyLimit + '</span></span></li>');
              }
          }

          $("#plan-details-widget").cardOverlay("clear");
        },
        error: function() {
          $("#plan-details-widget").cardOverlay("error", {message: "Error while fetching plan data"});
        }
      })
    }

    var getQuickSetup = _ => {
      $.ajax({
        url: '<?php echo $quickSetupUrl; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          $('#range').val(data.optimization_level);
          $('#manual-settings-url').attr('href', data.manual_settings_url);

          document.getElementById('range').oninput(false);
          $("#quicksetup-widget").cardOverlay("clear");
        },
        error: function() {
          $("#plan-details-widget").cardOverlay("error", {message: "Error while fetching the optimization level settings"});
        }
      })
    }

    window.addEventListener("cache.invalidate.success", getOptimizations);
    window.addEventListener("cache.purge.success", getOptimizations);


    var autoDetectCompression = function() {
      $("#settings-widget").cardOverlay("loading", {message: "Testing current compression status"});

      $.post(ajaxurl, {
        action: 'nitropack_test_compression_ajax'
      }, function(response) {
        var resp = JSON.parse(response);
        if (resp.status == "success") {
          if (resp.hasCompression) { // compression already enabled
            $("#compression-status").attr("checked", false);
            $("#settings-widget").cardOverlay("success", {message: "Compression is already enabled on your server! There is no need to enable it in NitroPack.", timeout: 3000});
          } else { // no compression - enable ours
            $("#compression-status").attr("checked", true);
            $("#settings-widget").cardOverlay("success", {message: "No compression was detected! We will now enable it in NitroPack.", timeout: 3000});
          }
          Notification.success('Compression settings saved');
        } else {
          $("#settings-widget").cardOverlay("error", {message: "Could not determine compression status automatically. Please configure it manually.", timeout: 3000});
        }
      });
    }

    $("#compression-status").on("click", function(e) {
      $.post(ajaxurl, {
        action: 'nitropack_set_compression_ajax',
        data: {
          compressionStatus: $(this).is(":checked") ? 1 : 0
        }
      }, function(response) {
        Notification.success('Compression settings saved');
      });
    });

    $("#auto-purge-status").on("click", function(e) {
      $.post(ajaxurl, {
        action: 'nitropack_set_auto_cache_purge_ajax',
        autoCachePurgeStatus: $(this).is(":checked") ? 1 : 0
      }, function(response) {
        Notification.success('Automatic cache purge settings saved');
      });
    });

    $("#save-cacheable-post-types").on("click", function(e) {
      $(this).find("i").removeClass("d-none");
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: 'nitropack_set_cacheable_post_types',
          cacheableObjectTypes: $('.cacheable-post-type:checked').map(function(i, el){ return el.name; }).toArray()
        },
        success: function() {
          Notification.success('Changes saved');
        },
        error: function() {
          Notification.error('There was an error while saving the changes. Please try again.');
        },
        complete: function() {
          $("#save-cacheable-post-types i").addClass("d-none");
          $("#cacheable-post-types-modal").modal("hide");
        }
      });
    });

    $(document).on('click', "#compression-test-btn", e => {
      e.preventDefault();

      autoDetectCompression();
    });

    window.confirmDisconnect = function() {
      $("#settings-widget").cardOverlay("loading", {message: "Disconnecting..."});
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "nitropack_disconnect"
        },
        complete: function() {
          location.reload();
        }
      });
    }

    window.rejectDisconnect = function() {
      $("#settings-widget").cardOverlay("clear");
    }

    window.safemodeOnDisconnect = function() {
      enableSafemode();
      $("#settings-widget").cardOverlay("clear");
    }

    $(document).on('click', "#disconnect-btn", e => {
      e.preventDefault();

      var confirmHtml = '<p>Did you know that NitroPack offers a safe mode? We recommend using it instead of disconnecting. Are you sure that you wish to disconnect?</p>';
      confirmHtml += '<p id="disconnectConfirmBtns"><a href="javascript:void(0);" onclick="rejectDisconnect()" class="btn btn-default btn-sm">No</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="safemodeOnDisconnect()" class="btn btn-primary btn-sm">Enable Safe Mode</a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="confirmDisconnect()" class="btn btn-info btn-sm">Disconnect</a></p>';
      $("#settings-widget").cardOverlay("notify", {message: confirmHtml});
    });

    $("#warmup-status-slider").on("click", function(e) {
      e.preventDefault();
      var isEnabled = $("#warmup-status").is(":checked");
      if (isEnabled) {
        disableWarmup();
        $("#warmup-status").attr("checked", false);
      } else {
        estimateWarmup();
      }
    });

    var loadWarmupStatus = function() {
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "nitropack_warmup_stats"
        },
        dataType: "json",
        success: function(resp) {
          if (resp.type == "success") {
            $("#warmup-status").attr("checked", !!resp.stats.status);
            $("#loading-warmup-status").hide();
            $("#warmup-toggle").show();
          } else {
            setTimeout(loadWarmupStatus, 500);
          }
        }
      });
    }
    loadWarmupStatus();

    $("#safemode-status-slider").on("click", function(e) {
      e.preventDefault();
      var isEnabled = $("#safemode-status").is(":checked");
      if (isEnabled) {
        disableSafemode();
        $("#safemode-status").attr("checked", false);
      } else {
        enableSafemode();
        $("#safemode-status").attr("checked", true);
      }
    });

    var loadSafemodeStatus = function() {
      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: "nitropack_safemode_status"
        },
        dataType: "json",
        success: function(resp) {
          if (resp.type == "success") {
            $("#safemode-status").attr("checked", !!resp.isEnabled);
            $("#loading-safemode-status").hide();
            $("#safemode-toggle").show();
          } else {
            setTimeout(loadSafemodeStatus, 500);
          }
        }
      });
    }
    loadSafemodeStatus();
  })(jQuery);

  (_ => {
    const classIndex = {
      1: 'range-success',
      2: 'range-warning',
      3: 'range-danger',
      4: 'range-ludicrous',
      5: 'range-manual',
    }

    const atTimeout = (_ => {
      var timeout;

      return (callback, time) => {
        clearTimeout(timeout);
        timeout = setTimeout(callback, time)
      };
    })();

    const saveSetting = function(value) {
      return new Promise((resolve, reject) => {
        var xhr = new XMLHttpRequest();

        xhr.open("POST", '<?php echo $quickSetupSaveUrl; ?>', true);

        //Send the proper header information along with the request
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() { // Call a function when the state changes.
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                resolve();
            }
        }

        xhr.send("setting=" + value);
      });
    }

    document.getElementById('range').oninput = function() {
      var children = document.getElementById('description').children;
      var shown = parseInt(this.value);

      for (var i = 0; i < children.length; i++) {
        children.item(i).classList.toggle('hidden', i != shown);
      }

      document.getElementById('range').classList.remove('range-success', 'range-warning', 'range-danger', 'range-ludicrous', 'range-manual');

      if (classIndex[shown]) {
        document.getElementById('range').classList.add(classIndex[shown]);
      }

      atTimeout(async function() {
        await saveSetting(shown);
      }, 500);
    };

    var min = parseInt(document.getElementById('range').min);
    var max = parseInt(document.getElementById('range').max);
    for (var i = min; i <= max; i++) {
      let divisor = document.createElement('div');
      divisor.classList.add("divisor");
      document.getElementById('divisors').appendChild(divisor);

      let label = document.createElement('div');
      label.textContent = document.getElementById('description').children.item(i).getElementsByTagName('h6').item(0).textContent;
      label.classList.add("label");
      document.getElementById('labels').appendChild(label);
    }
  })();
</script>
