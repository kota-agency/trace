!function(n){var e={};function t(i){if(e[i])return e[i].exports;var o=e[i]={i:i,l:!1,exports:{}};return n[i].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=n,t.c=e,t.d=function(n,e,i){t.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:i})},t.r=function(n){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},t.t=function(n,e){if(1&e&&(n=t(n)),8&e)return n;if(4&e&&"object"==typeof n&&n&&n.__esModule)return n;var i=Object.create(null);if(t.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var o in n)t.d(i,o,function(e){return n[e]}.bind(null,o));return i},t.n=function(n){var e=n&&n.__esModule?function(){return n.default}:function(){return n};return t.d(e,"a",e),e},t.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},t.p="",t(t.s=373)}({12:function(n,e){n.exports=jQuery},373:function(n,e,t){"use strict";t.r(e);var i=t(12),o=t.n(i);o()((function(){if(o()("#csv-panel-export").on("submit",(function(){var n=o()(this);n.find('button[type="submit"]').prop("disabled",!0).siblings(".input-loading").css("visibility","visible"),setTimeout((function(){n.find('button[type="submit"]').prop("disabled",!1).siblings(".input-loading").css("visibility","hidden")}),5e3)})),o()("#csv-import-me").on("change",(function(){o()("#csv-import-warning").removeClass("hidden")})),o()("#csv-panel-import").on("submit",(function(){return confirm(rankMath.confirmCsvImport)})),o()("#csv-import-cancel").on("click",(function(){return confirm(rankMath.confirmCsvCancel)})),o()("#csv-import-progress-details").length){setTimeout((function n(){o.a.ajax({url:ajaxurl,type:"GET",dataType:"html",data:{action:"csv_import_progress",_ajax_nonce:rankMath.csvProgressNonce}}).done((function(e){o()("#csv-import-progress-details").html(e),o()(e).find("#csv-import-progress-value").length?setTimeout(n,3e3):o()("#csv-import-cancel").addClass("disabled hidden").prop("disabled",!0).siblings(".input-loading").hide()}))}),3e3)}o()("#csv-advanced-options-toggle").on("change",(function(){o()(".csv-advanced-options").stop().slideToggle(400),o()(this).prop("checked")||o()(".csv-advanced-options").find("input").prop("checked",!0)}))}))}});