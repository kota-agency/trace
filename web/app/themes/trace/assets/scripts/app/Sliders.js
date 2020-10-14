import $ from 'jquery';
import 'slick-carousel';
import resizeEvent from '../utilities/triggerResizeEvent';
import AOS from 'aos';

const Sliders = (() => {

    const initSlick = ($selector, options) => {

        $selector.each((index, el) => {

            const $elem = $(el);

            if (!$elem.length) {
                return;
            }

            if (!options) {
                options = {
                    infinite: true,
                    slidesToShow: 3,
                    slidesToScroll: 3
                };
            }

            if (options.dataSlidesToShow) {
                options.slidesToShow = parseInt($elem.attr('data-slides'));
            }

            if(options.appendElDots) {
                options.appendDots = $elem.closest('section').find(options.appendElDots);
            }


            if(options.appendElArrows) {
                options.appendArrows = $elem.closest('section').find(options.appendElArrows);
            }

            if(options.appendSlideDots) {
                options.appendDots = $elem.find(options.appendSlideDots);
            }


            $elem.on('init', (event, slick, direction) => {
                if (options.matchHeight) {
                    $elem.find('.slide').matchHeight({
                        byRow: false
                    });
                }


                if (options.onWheel) {
                    $elem.on('wheel', ((e) => {
                        e.preventDefault();

                        if (e.originalEvent.deltaY > 0) {
                            $elem.slick('slickNext');
                        } else {
                            $elem.slick('slickPrev');
                        }
                    }));
                }

                $elem.find('.slick-slide.slick-active').first().addClass('first');
                $elem.find('.slick-slide.slick-active').last().addClass('last');

                $elem.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
                    $elem.find('.slick-slide.slick-slide').removeClass('first last');

                    if(options.appendSlideDots) {
                        $.each(slick.$dots, (i, ul) => {
                            $(ul).find('li').removeClass('active');
                            $(ul).find('li').eq(nextSlide).addClass('active');
                        });
                    }
                });

                $elem.on('afterChange', function (event, slick, currentSlide, nextSlide) {
                    $elem.find('.slick-slide.slick-active').first().addClass('first');
                    $elem.find('.slick-slide.slick-active').last().addClass('last');

                });

                $('.slick-dots').on('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                });

                window.dispatchEvent(resizeEvent);
                AOS.refresh();
            });

            $elem.slick(options);

        });
    };


    initSlick($('.testimonials__items'), {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        arrows: true,
        infinite: true,
        appendElArrows: '.testimonials__arrows',
        autoplay: true,
        autoplaySpeed: 5000
    });


    // const initSwiper = (selector, options) => {
    //
    //     $(selector).each((index, el) => {
    //
    //         const $elem = $(el);
    //
    //         if ($elem.find('.card-product')) {
    //             options.on = {
    //                 init: () =>  {
    //                     /* do something */
    //
    //                 },
    //             }
    //
    //         }
    //
    //
    //         new Swiper($elem.get(0), options);
    //     });
    // }


    // initSwiper('.product-carousel__slider', {
    //     // Optional parameters
    //     slidesPerView: 1,
    //     autoplay: {
    //         delay: 5000
    //     },
    //     watchSlidesVisibility: true,
    //     // And if we need scrollbar
    //     scrollbar: {
    //         el: '.swiper-scrollbar',
    //         draggable: true,
    //     },
    //     breakpoints: {
    //         768: {
    //             slidesPerView: 2,
    //         },
    //         992: {
    //             slidesPerView: 3,
    //         },
    //         1500: {
    //             slidesPerView: 4,
    //         }
    //     }
    // });



});

export default Sliders;
