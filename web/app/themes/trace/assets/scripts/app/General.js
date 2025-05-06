import $ from 'jquery';
import 'jquery-match-height/dist/jquery.matchHeight';
import AOS from "aos";
import Cookies from 'js-cookie';
import inViewport from 'in-viewport';

import { gsap, Linear } from "gsap";
import { MotionPathPlugin } from "gsap/MotionPathPlugin";
import { MotionPathHelper } from "gsap/MotionPathHelper";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(MotionPathPlugin, MotionPathHelper, ScrollTrigger);


function General() {

    // gsap.set(".family__logo2", { autoAlpha: 1});

    const logos = Array.from(document.querySelectorAll('.family__logo'));
    const duration = 30;
    const startPoint = 1 / logos.length;

    gsap.registerPlugin(MotionPathPlugin, ScrollTrigger);

    const tl = gsap.timeline({ defaults: { ease: "none" } });

    // const logos = document.querySelectorAll('[class^="family__logo"]');
    // const totalScrollDuration = 5000; // pixels of scroll for full loop
    const count = logos.length;

    logos.forEach((logo, index) => {
        const offset = index / count;

        gsap.to(logo, {
            scrollTrigger: {
                trigger: document.querySelector('.family__path'),
                start: "top bottom",
                end: "+=5000", // adjust as needed
                scrub: 1,
            },
            motionPath: {
                path: '#path-anim',
                align: '#path-anim',
                alignOrigin: [0.5, 0.5],
                // autoRotate: true,
                start: offset,
                end: offset + 1, // loops along the full path
            },
            ease: "none"
        });
    });


    gsap.to('.family__background-text', {
        y: -200, // Move up as user scrolls down
        ease: 'none',
        scrollTrigger: {
            trigger: '.family__background-text', // or use a wrapper section
            start: 'top bottom',  // when element enters viewport
            end: 'bottom top',    // when it leaves viewport
            scrub: true,          // smooth sync with scroll
        }
    });


    // MotionPathHelper.create(".family__logo2");

    // tl.pause(1)

    let animating = false;

    $('.mh').matchHeight();

    $('.sector-tab').matchHeight({
        byRow: false
    });

    $('.card__content').matchHeight({
        byRow: false
    });

    $.fn.matchHeight._afterUpdate = function (event, groups) {
        AOS.refresh();
    }

    $.get(theme_params.stylesheet_dir + '/dist/images/del.svg', (data) => {
        $('h1').find('del').append('<span class="del-dash"><span data-aos="fade" data-aos-delay="1000" data-aos-duration="200">' + data + '</span></span>');
    }, 'text');


    $('.video').each((index, el) => {

        const iframe = $(el).find('iframe').get(0);

        if (typeof Vimeo !== 'undefined' && typeof Vimeo.Player === 'function') {
            const player = new Vimeo.Player(iframe);
            const $image = $(el).find('.video__image');

            $image.on('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                const $this = $(e.currentTarget);

                $this.addClass('active');

                player.play().then(function () {
                    // the video was played
                }).catch(function (error) {
                    switch (error.name) {
                        case 'PasswordError':
                            break;
                        case 'PrivacyError':
                            break;
                        default:
                            break;
                    }
                });

            });
        }
    });

    $('.milestone').on('click, mouseenter', (e) => {
        $('.milestone').removeClass('active');
        $(e.currentTarget).addClass('active');
    });


    function imageContentHeight() {
        const $inner = $('.image-content__inner')

        $inner.each((index, el) => {

            const $image = $(el).find('.image-content__image--desktop');
            if ($image.length) {
                $(el).css('min-height', $image.height());
            }

        });

    }

    imageContentHeight();

    function verticalHeading() {
        $('.heading-columns__vert-heading').each((index, el) => {
            const width = $(el).find('.vert-heading').width();

            $(el).css('min-height', width + 'px');
        });
    }

    verticalHeading();

    const updateDimensions = (e) => {


        if (updateDimensions._tick) {
            cancelAnimationFrame(updateDimensions._tick);
        }

        updateDimensions._tick = requestAnimationFrame(function () {
            updateDimensions._tick = null;
            imageContentHeight();
        });

    };


    window.addEventListener('resize', updateDimensions);


    const updateScroll = () => {
        if (updateScroll._tick) {
            cancelAnimationFrame(updateScroll._tick);
        }

        updateScroll._tick = requestAnimationFrame(function () {
            updateScroll._tick = null;

            $('.video').each((index, el) => {

                const iframe = $(el).find('iframe').get(0);

                if (typeof Vimeo !== 'undefined' && typeof Vimeo.Player === 'function') {
                    const player = new Vimeo.Player(iframe);


                    player.getPaused().then(function (paused) {
                        if (!paused) {
                            if (!inViewport($(el).get(0))) {
                                player.pause();
                            }
                        }
                    }).catch(function (error) {
                        // an error occurred
                    });
                }

            });
        });
    };

    window.addEventListener('scroll', updateScroll);
    updateScroll();

    $('.page-link:not(.redirect-link)').find('a').on('click', (e) => {
        e.preventDefault();

        if (!animating) {
            animating = true;

            const $this = $(e.currentTarget);
            const index = $(e.currentTarget).closest('.page-links__col').index();

            $('.modal').fadeOut().promise()
                .done(() => {

                    $this.closest('section').next().children().eq(index).fadeIn(() => {
                        animating = false;
                    });
                });
        }

    });

    $('.split-content__content').find('a.btn').on('click', (e) => {

        // Check if is external link
        var comp = new RegExp(location.host);
        if (!comp.test($(e.currentTarget).attr('href'))) {
            return
        }

        const $this = $(e.currentTarget);


        e.preventDefault();


        if (!animating) {
            animating = true;


            const index = $(e.currentTarget).closest('.split-content__item').index();

            $('.modal').fadeOut().promise()
                .done(() => {

                    $this.closest('section').next().children().eq(index).fadeIn(() => {
                        animating = false;
                    });
                });
        }

    });

    $('.content-modals__item').find('.link').on('click', (e) => {
        e.preventDefault();


        if (!animating) {
            animating = true;

            const $this = $(e.currentTarget);
            const index = $(e.currentTarget).closest('.content-modals__item').attr('data-item');

            $('.modal').fadeOut().promise()
                .done(() => {

                    $this.closest('section').next().children().eq(index).fadeIn(() => {
                        animating = false;
                    });
                });
        }

    });

    $('a:not(.gated-file)').on('click', (e) => {
        const $this = $(e.currentTarget);
        const hash = $this.attr('href').substring($this.attr('href').indexOf('#'));
        if (hash.includes('#')) {
            if ($(hash).length) {
                $(hash).fadeIn();
            }
        }


    });

    $('.modal__close').on('click', (e) => {
        $(e.currentTarget).closest('.modal').fadeOut();
    });

    $('.modal').on('click', (e) => {
        $('.modal').fadeOut();
    });

    $('.modal__inner').on('click', (e) => {
        e.stopPropagation();
    });

    $('.sector-tab--mobile').on('click', (e) => {
        const $this = $(e.currentTarget);

        if (!$this.hasClass('active')) {
            $('.sector-tab--mobile').removeClass('active');
            $this.addClass('active');
        } else {
            $this.removeClass('active');
        }

    });

    $('#acceptCookies').on('click', () => {
        $('.cookie-banner').fadeOut();
        Cookies.set('trace_cookie_consent', 'on', { expires: 365 });
    });

    if (Cookies.get('trace_cookie_consent') == 'on') {
        $('.cookie-banner').css('display', 'none');
    }

    $('.gated-file').on('click', function (e) {
        e.preventDefault();
        $('#form_modal').addClass('show')

        var gravityFormId = $(this).attr('data-gravity-form');
        var formContent = $('.form-popup__inner');

        formContent.html('');
        $.ajax({
            type: 'GET',
            url: ajaxurl,
            data: {
                action: 'get_gravity_form',
                gravity_form_id: gravityFormId
            },
            success: function (res) {
                formContent.html(res)
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
                console.log(XMLHttpRequest);
            }
        });

    });

    $('.form-popup .close').click(function (e) {
        e.preventDefault();
        $('#form_modal').removeClass('show')
    })

}

export default General;
