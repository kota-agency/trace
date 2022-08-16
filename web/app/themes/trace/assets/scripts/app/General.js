import $ from 'jquery';
import 'jquery-match-height/dist/jquery.matchHeight';
import AOS from "aos";
import Cookies from 'js-cookie';
import inViewport from 'in-viewport';

import { gsap, Linear } from "gsap";
import { MotionPathPlugin } from "gsap/MotionPathPlugin";
import { MotionPathHelper } from "gsap/MotionPathHelper";

gsap.registerPlugin(MotionPathPlugin, MotionPathHelper);


function General() {
    
    // gsap.set(".family__logo2", { autoAlpha: 1});

    const familyLogoLength =  Array.from(document.querySelectorAll('.family__logo')).length;
    const duration = 30;
    const startPoint = 1/familyLogoLength;

    console.log(startPoint);

    const logoZero = document.querySelector('.family__logo0');
    const logoOne = document.querySelector('.family__logo1');
    const logoTwo = document.querySelector('.family__logo2');
    const logoThree = document.querySelector('.family__logo3');
    const logoFour = document.querySelector('.family__logo4');
    const logoFive = document.querySelector('.family__logo5');
    const logoSix = document.querySelector('.family__logo6');
    const logoSeven = document.querySelector('.family__logo7');
    const logoEight = document.querySelector('.family__logo8');

    const tl = gsap.timeline({defaults: { ease: "none" }})

    if (logoZero) {
        tl.to(logoZero, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoZero.dataset.logoIndex),
              end: 1 + (startPoint*logoZero.dataset.logoIndex),
            },
        });
    }

    if (logoOne) {
        tl.to(logoOne, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoOne.dataset.logoIndex),
              end: 1 + (startPoint*logoOne.dataset.logoIndex),
            },
        },`-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoTwo) {
        tl.to(logoTwo, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoTwo.dataset.logoIndex),
              end: 1 + (startPoint*logoTwo.dataset.logoIndex),
            }
        },`-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoThree) {
        tl.to(logoThree, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoThree.dataset.logoIndex),
              end: 1 + (startPoint*logoThree.dataset.logoIndex),
            }
        }, `-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoFour) {
        tl.to(logoFour, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoFour.dataset.logoIndex),
              end: 1 + (startPoint*logoFour.dataset.logoIndex),
            }
        }, `-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoFive) {        
        tl.to(logoFive, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoFive.dataset.logoIndex),
              end: 1 + (startPoint*logoFive.dataset.logoIndex),
            }
        },`-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoSix) {
        tl.to(logoSix, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: 0 + (startPoint*logoSix.dataset.logoIndex),
              end: 1 + (startPoint*logoSix.dataset.logoIndex),
            }
        },`-=${30*logoOne.dataset.logoIndex}`);
    }

    if (logoSeven) {
        tl.to(logoSeven, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoSeven.dataset.logoIndex),
              end: 1 + (startPoint*logoSeven.dataset.logoIndex),
            }
        }, `-=${30*logoOne.dataset.logoIndex}`);
    }


    if (logoEight) {
        tl.to(logoEight, {
            duration: duration, 
            repeat: -1,
            ease: "none",
            immediateRender: true,
            motionPath: {
              path: '#path-anim-1',
              align: '#path-anim-1',
              alignOrigin: [0.5, 0.5],
              start: (startPoint*logoEight.dataset.logoIndex),
              end: 1 + (startPoint*logoEight.dataset.logoIndex),
            }
        });
    }

    // MotionPathHelper.create(".family__logo2");

    tl.pause(1)

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
    });

    $('.milestone').on('click, mouseenter', (e) => {
        $('.milestone').removeClass('active');
        $(e.currentTarget).addClass('active');
    });


    function imageContentHeight() {
        const $inner = $('.image-content__inner')

        $inner.each((index, el) => {

            const $image = $(el).find('.image-content__image--desktop');
            console.log($image);
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

    $('.split-content__content').find('a').on('click', (e) => {

        // Check if is external link
        var comp = new RegExp(location.host);
        if(!comp.test($(e.currentTarget).attr('href'))){
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

        if ($(hash).length) {
            $(hash).fadeIn();
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
        Cookies.set('trace_cookie_consent', 'on', {expires: 365});
    });

    if(Cookies.get('trace_cookie_consent') == 'on') {
        $('.cookie-banner').css('display', 'none');
    }

    $('.gated-file').on('click', function(e) {
        e.preventDefault();
        $('#form_modal').addClass('show')

        var gravityFormId = $(this).attr('data-gravity-form');
        var formContent = $('.form-popup__inner');
        
        formContent.html('');
        $.ajax({  
            type: 'GET',  
            url: ajaxurl,  
            data: { 
                action : 'get_gravity_form',
                gravity_form_id: gravityFormId
            },  
            success: function(res){  
                formContent.html(res)
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(errorThrown);
                console.log(XMLHttpRequest);
            }
        });  

    });

    $('.form-popup .close').click(function(e) {
      e.preventDefault();
      $('#form_modal').removeClass('show')
    })

}

export default General;
