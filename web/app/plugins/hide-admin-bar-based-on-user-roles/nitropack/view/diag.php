<div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <div class="card-overlay-blurrable np-widget" id="diagnostic-widget">
        <div class="card card-d-item">
          <div class="card-body">
            <h5 class="card-title">Diagnostics report</h5>
            <ul class="list-group list-group-flush">                        
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="loading-general-info">
                  Include NitroPack info(version, methods, environment)
                </span>
                <span id="general-info-toggle">
                  <label id="general-info-slider" class="switch">
                    <input type="checkbox" class="diagnostic-option" id="general-info-status" checked>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="loading-plugins-status">
                  Include active plugins list
                </span>
                <span id="active-plugins-toggle">
                  <label id="active-plugins-slider" class="switch">
                    <input type="checkbox" class="diagnostic-option" id="active-plugins-status" checked>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="conflicting-plugins-info">
                  Include conflicting plugins list
                </span>
                <span id="conflicting-plugins-toggle">
                  <label id="conflicting-plugins-slider" class="switch">
                    <input type="checkbox" class="diagnostic-option" id="conflicting-plugins-status" checked>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="loading-user-config">
                  Include user config
                </span>
                <span id="user-config-toggle">
                  <label id="user-config-slider" class="switch">
                    <input type="checkbox" class="diagnostic-option" id="user-config-status" checked>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span id="loading-dir-info">
                  Include directory info(structure,permisions)
                </span>
                <span id="dir-info-toggle">
                  <label id="dir-info-slider" class="switch">
                    <input type="checkbox" class="diagnostic-option" id="dir-info-status" checked>
                    <span class="slider"></span>
                  </label>
                </span>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>
                  <a id="gen-report-btn" href="javascript:void(0);" class="btn btn-light btn-outline-secondary"><i class="fa fa-refresh fa-spin" style="display:none" id="diagnostics-loader"></i>&nbsp;&nbsp;Generate&nbsp;Report</a>
                </span>
              </li>
            </ul>
            <p class="mb-0 mt-2"><i class="fa fa-info-circle text-primary" aria-hidden="true"></i> The generated report will be saved to your computer and can later be attached to your ticket.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  /*
  var toggled01 = {"general-info-status":document.getElementById("general-info-status").checked ,
                            "active-plugins-status":document.getElementById("active-plugins-status").checked ,
                            "user-config-status":document.getElementById("user-config-status").checked ,
                            "dir-info-status":document.getElementById("dir-info-status").checked 
                            };function() {
                Notification.success('Report generated');
                }
   */
($ => {
  let isReportGenerating = false;

  $("#gen-report-btn").on("click", function(e) {
    if (isReportGenerating) return;

    $.ajax({
      url: ajaxurl,
      type: "POST",
      dataType: "text",
      data: {
        action: 'nitropack_generate_report',
        toggled: {
          "general-info-status": $("#general-info-status:checked").length,
          "active-plugins-status": $("#active-plugins-status:checked").length,
          "conflicting-plugins-status": $("#conflicting-plugins-status:checked").length,
          "user-config-status": $("#user-config-status:checked").length,
          "dir-info-status": $("#dir-info-status:checked").length
        }
      },
      beforeSend: function (xhr,sett) {
        if ($(".diagnostic-option:checked").length > 0) {
          $("#diagnostics-loader").show();
          isReportGenerating = true;
          return true;
        } else {
          alert('Please select at least one of the report options');
          return false;
        }
      },
      success: function(response, status, xhr) {
        if (response.length > 1) {
          var filename = "";
          var disposition = xhr.getResponseHeader('Content-Disposition');
          if (disposition && disposition.indexOf('attachment') !== -1) {
            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            var matches = filenameRegex.exec(disposition);
            if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
          }

          var type = xhr.getResponseHeader('Content-Type');
          var blob = new Blob([response], { type: type });

          if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
            window.navigator.msSaveBlob(blob, filename);
          } else {
            var URL = window.URL || window.webkitURL;
            var downloadUrl = URL.createObjectURL(blob);

            if (filename) {
              // use HTML5 a[download] attribute to specify filename
              var a = document.createElement("a");
              // safari doesn't support this yet
              if (typeof a.download === 'undefined') {
                window.location.href = downloadUrl;
              } else {
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
              }
            } else {
              window.location.href = downloadUrl;
            }

            setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100);
          }
          Notification.success('Report generated successfully.');
        } else {
          Notification.error('Response is empty. Report generation failed.');
        }
      },
      error: function() {
        Notification.error('There was an error while generating the report.');
      },
      complete: function() {
        $("#diagnostics-loader").hide();
        isReportGenerating = false;
      }
    });
  });
})(jQuery);
</script>
