/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/ht-blocks-frontend.src.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/ht-blocks-frontend.src.js":
/*!***************************************!*\
  !*** ./src/ht-blocks-frontend.src.js ***!
  \***************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _modules_toggle_toggle_block_frontend_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/toggle/toggle-block-frontend.js */ \"./src/modules/toggle/toggle-block-frontend.js\");\n/* harmony import */ var _modules_toggle_toggle_block_frontend_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_modules_toggle_toggle_block_frontend_js__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _modules_accordion_accordion_block_frontend_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/accordion/accordion-block-frontend.js */ \"./src/modules/accordion/accordion-block-frontend.js\");\n/* harmony import */ var _modules_accordion_accordion_block_frontend_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_modules_accordion_accordion_block_frontend_js__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _modules_tabs_tabs_block_frontend_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/tabs/tabs-block-frontend.js */ \"./src/modules/tabs/tabs-block-frontend.js\");\n/* harmony import */ var _modules_tabs_tabs_block_frontend_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_modules_tabs_tabs_block_frontend_js__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _modules_image_image_block_frontend_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/image/image-block-frontend.js */ \"./src/modules/image/image-block-frontend.js\");\n/* harmony import */ var _modules_image_image_block_frontend_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_modules_image_image_block_frontend_js__WEBPACK_IMPORTED_MODULE_3__);\n/** toggle */\n\n\n/** accordion */\n\n\n/** tabs */\n\n\n/** image */\n\n\n//# sourceURL=webpack:///./src/ht-blocks-frontend.src.js?");

/***/ }),

/***/ "./src/modules/accordion/accordion-block-frontend.js":
/*!***********************************************************!*\
  !*** ./src/modules/accordion/accordion-block-frontend.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery(document).ready(function($){\n\n\t$('.wp-block-hb-accordion__title').click(function() {\n\t\t$(this).parents('.wp-block-hb-accordion').children('.wp-block-hb-accordion__section').removeClass('wp-block-hb-accordion__section--active');\n\t    $(this).parent('.wp-block-hb-accordion__section').addClass('wp-block-hb-accordion__section--active');\n\t});\n\n});\n\n//# sourceURL=webpack:///./src/modules/accordion/accordion-block-frontend.js?");

/***/ }),

/***/ "./src/modules/image/image-block-frontend.js":
/*!***************************************************!*\
  !*** ./src/modules/image/image-block-frontend.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery.fn.htImageZoom = function () {\n\n\n\treturn this.each(function(){\n\n\t\tconsole.log('htImageZoom');\n\n\t\tvar native_width = 0;\n\t\tvar native_height = 0;\n\t  \n\t  \t//suggestion from @Jab2870 on CODEPEN\n\t  \tjQuery(this).children(\".hb-magnify__large\").css(\"background\",\"url('\" + jQuery(this).children(\".hb-magnify__small\").attr(\"src\") + \"') no-repeat\");\n\n\t\t//Now the mousemove function\n\t\tjQuery(this).mousemove(function(e){\n\t\t\t//When the user hovers on the image, the script will first calculate\n\t\t\t//the native dimensions if they don't exist. Only after the native dimensions\n\t\t\t//are available, the script will show the zoomed version.\n\t\t\tif(!native_width && !native_height) {\n\t\t\t\t//This will create a new image object with the same image as that in .small\n\t\t\t\t//We cannot directly get the dimensions from .small because of the \n\t\t\t\t//width specified to 200px in the html. To get the actual dimensions we have\n\t\t\t\t//created this image object.\n\t\t\t\tvar image_object = new Image();\n\t\t\t\timage_object.src = jQuery(this).children(\".hb-magnify__small\").attr(\"src\");\n\t\t\t\t\n\t\t\t\t//This code is wrapped in the .load function which is important.\n\t\t\t\t//width and height of the object would return 0 if accessed before \n\t\t\t\t//the image gets loaded.\n\t\t\t\tnative_width = image_object.width;\n\t\t\t\tnative_height = image_object.height;\t\t\t\n\n\t\t\t} else {\t\n\t\t\t\t//x/y coordinates of the mouse\n\t\t\t\t//This is the position of .magnify with respect to the document.\n\t\t\t\tvar magnify_offset = jQuery(this).offset();\n\t\t\t\t//We will deduct the positions of .magnify from the mouse positions with\n\t\t\t\t//respect to the document to get the mouse positions with respect to the \n\t\t\t\t//container(.magnify)\n\t\t\t\tvar mx = e.pageX - magnify_offset.left;\n\t\t\t\tvar my = e.pageY - magnify_offset.top;\n\t\t\t\t\n\t\t\t\t//Finally the code to fade out the glass if the mouse is outside the container\n\t\t\t\tif(mx < jQuery(this).width() && my < jQuery(this).height() && mx > 0 && my > 0)\n\t\t\t\t{\n\t\t\t\t\tjQuery(this).children(\".hb-magnify__large\").fadeIn(200);\n\t\t\t\t}\n\t\t\t\telse\n\t\t\t\t{\n\t\t\t\t\tjQuery(this).children(\".hb-magnify__large\").fadeOut(200);\n\t\t\t\t}\n\t\t\t\tif(jQuery(this).children(\".hb-magnify__large\").is(\":visible\"))\n\t\t\t\t{\n\t\t\t\t\t//The background position of .large will be changed according to the position\n\t\t\t\t\t//of the mouse over the .small image. So we will get the ratio of the pixel\n\t\t\t\t\t//under the mouse pointer with respect to the image and use that to position the \n\t\t\t\t\t//large image inside the magnifying glass\n\t\t\t\t\tvar rx = Math.round(mx/jQuery(this).children(\".hb-magnify__small\").width()*native_width - jQuery(this).children(\".hb-magnify__large\").width()/2)*-1;\n\t\t\t\t\tvar ry = Math.round(my/jQuery(this).children(\".hb-magnify__small\").height()*native_height - jQuery(this).children(\".hb-magnify__large\").height()/2)*-1;\n\t\t\t\t\tvar bgp = rx + \"px \" + ry + \"px\";\n\t\t\t\t\t\n\t\t\t\t\t//Time to move the magnifying glass with the mouse\n\t\t\t\t\tvar px = mx - jQuery(this).children(\".hb-magnify__large\").width()/2;\n\t\t\t\t\tvar py = my - jQuery(this).children(\".hb-magnify__large\").height()/2;\n\t\t\t\t\t//Now the glass moves with the mouse\n\t\t\t\t\t//The logic is to deduct half of the glass's width and height from the \n\t\t\t\t\t//mouse coordinates to place it with its center at the mouse coordinates\n\t\t\t\t\t\n\t\t\t\t\t//If you hover on the image now, you should see the magnifying glass in action\n\t\t\t\t\tjQuery(this).children(\".hb-magnify__large\").css({left: px, top: py, backgroundPosition: bgp});\n\t\t\t\t}\n\t\t\t}\n\t\t})\n\n\t}); //end this.each\n\n};\n\n\njQuery(document).ready(function($){\n\n\t$('.hb-magnify').each(function(){\n\t\t$(this).htImageZoom();\n\t});\n\n});\n\n\n//# sourceURL=webpack:///./src/modules/image/image-block-frontend.js?");

/***/ }),

/***/ "./src/modules/tabs/tabs-block-frontend.js":
/*!*************************************************!*\
  !*** ./src/modules/tabs/tabs-block-frontend.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery(document).ready(function($){\n\n\n\t$( document ).on( 'click', '.wp-block-hb-tabs__nav li', function(e){\n\t\t\te.stopPropagation();\n\t\t\tvar tab = $(this).attr('data-tab');\n\t\t\tif(typeof tab !== 'undefined'){\n\n\t\t\t\t//deactivate all tabs\n\t\t\t\t$(this).parents('.wp-block-hb-tabs').find('li').attr('data-hb-tabs-tab--state', 'inactive');\n\t\t\t\t$(this).parents('.wp-block-hb-tabs').find('.wp-block-hb-tabs__content').attr('data-hb-tabs-tab--state', 'inactive');\n\t\t\t\n\t\t\t\t//activate this tab\n\t\t\t\tconsole.log('tab clicked->' + tab);\n\t\t\t\t$(this).attr('data-hb-tabs-tab--state', 'active');\n\t\t\t\t$(this).parents('.wp-block-hb-tabs').find('#' + tab).attr('data-hb-tabs-tab--state', 'active');\n\t\t\t\treturn false;\n\n\t\t\t\t\n\t\t\t\t$(this).parents('.wp-block-hb-tabs').find('#' + tab).attr('data-hb-tabs-tab--state', 'active');\n\t\t\t\treturn false;\n\t\t\t}\n\t\t\t\n\t});\n\n\tconsole.log('tabs-block-frontend loaded');\n\n});\n\n//# sourceURL=webpack:///./src/modules/tabs/tabs-block-frontend.js?");

/***/ }),

/***/ "./src/modules/toggle/toggle-block-frontend.js":
/*!*****************************************************!*\
  !*** ./src/modules/toggle/toggle-block-frontend.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery(document).ready(function($){\n\n\t$('.wp-block-hb-toggle__title').click(function() {\n\t    $(this).parent('.wp-block-hb-toggle').toggleClass('wp-block-hb-toggle--active');\n\t});\n\n});\n\n//# sourceURL=webpack:///./src/modules/toggle/toggle-block-frontend.js?");

/***/ })

/******/ });