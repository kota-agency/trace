// Polyfills
import "core-js/stable/promise";
import "core-js/stable/number";
import "regenerator-runtime/runtime";

// Utilities
import "./utilities/forEachPolyfill";


// Load Vendor
import AOS from 'aos';
import '@fancyapps/fancybox/dist/jquery.fancybox';

// Load App
import Accordion from './app/Accordion';
import Menu from './app/Menu';
import Posts from './app/Posts';
import ScrollTo from './app/ScrollTo';
import Sliders from './app/Sliders';
import General from './app/General';
import FullWidth from "./app/FullWidth";


$(async () => {

    function windowHashChecked() {
        return new Promise(resolve => {
            setTimeout(() => {
                if (window.location.hash) {
                    window.scrollTo(0, 0);
                }
                resolve('resolved');
            }, 1);
        });
    }

    await windowHashChecked();



    const updateDimensions = (e) => {



        if (updateDimensions._tick) {
            cancelAnimationFrame(updateDimensions._tick);
        }

        updateDimensions._tick = requestAnimationFrame(function () {
            updateDimensions._tick = null;

            FullWidth();

        });


        if (e === 'start') {
            return new Promise(resolve => {
                function checkWidths() {
                    if ($(".full-width.width-calculated").length === $(".full-width").length) {
                        resolve('resolved');

                    } else {
                        clearTimeout(t2 /* instead of carousel */);
                        t2 = setTimeout(checkWidths, 10);
                    }
                }

                let t2 = setTimeout(checkWidths, 10);
            });
        }
    };


    window.addEventListener('resize', updateDimensions);
    await updateDimensions('start');


    Sliders();


});

$(window).on('load', function() {

    // Load Imports
    Accordion();
    General();
    Menu();
    Posts();
    ScrollTo();

    AOS.init({
        once: false,
        duration: 1000
    });

});


$(document).on('gform_post_render', function(event, form_id, current_page){

    $('.gform_wrapper').each((index, el) => {

        $(el).find('.gform_fields').append($(el).find('.gform_footer'));
        $(el).find('.gform_footer').wrap('<li class="gfield_footer gfield">');
    });

});
