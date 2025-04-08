function Menu() {
    const $masthead = $(".masthead"),
        $burger = $(".burger"),
        $navigation = $('.masthead__navigation'),
        $mobileNav = $(".mobile-navigation"),
        $closeModal = $('.mobile-navigation__close'),
        $searchTrigger = $('.search-trigger'),
        $mastfoot = $('.mastfoot');

    let animating = false;

    $('a[href="#"]').on('click', (e) => {
        e.preventDefault();
    });

    $burger.on("click", (e) => {
        const $this = $(e.currentTarget);

        $this.toggleClass("active");

        if ($this.hasClass("active")) {
            $mobileNav.addClass("active");
        } else {
            $mobileNav.removeClass('active');
            $mobileNav.removeClass('sub-open');
            $masthead.removeClass("active");
            $mobileNav.find('ul').removeClass('current active');
        }
    });

    // $mobileNav.find('.menu-item-has-children').append('<div class="arrow-nav-open-sub"></div>');

    // $mobileNav.find('.menu-item-has-children .arrow-nav-open-sub').on('click', (e) => {
    //     // e.preventDefault();
    //     e.stopPropagation();
    //     console.log('click arrow');
    //     $mobileNav.addClass('sub-open');
    //     $mobileNav.scrollTop(0);
    //     $mobileNav.find('ul').scrollTop(0);
    //     $mobileNav.find('ul').removeClass('current');
    //     $(e.currentTarget).siblings('ul.sub-menu').addClass('active current');
    // });

    $mobileNav.find('footer .menu-item-has-children').on('click', (e) => {
        // e.preventDefault();
        e.stopPropagation();
        console.log('click arrow');
        $mobileNav.addClass('sub-open');
        $mobileNav.scrollTop(0);
        $mobileNav.find('ul').scrollTop(0);
        $mobileNav.find('ul').removeClass('current');
        $(e.currentTarget).siblings('ul.sub-menu').addClass('active current');
    });

    // $mobileNav.find('.menu-item-has-children').on('click', (e) => {
    //     // e.preventDefault();
    //     e.stopPropagation();
    //     console.log('click arrow');
    //     $mobileNav.addClass('sub-open');
    //     $mobileNav.scrollTop(0);
    //     $mobileNav.find('ul').scrollTop(0);
    //     $mobileNav.find('ul').removeClass('current');
    //     $(e.currentTarget).siblings('ul.sub-menu').addClass('active current');
    // });


    $mobileNav.find('.menu-item-has-children > ul').prepend('<li class="menu-back"><a href="#">Back</a></li>');

    $mobileNav.find('.menu-back').on('click', (e) => {
        e.stopPropagation();

        const $this = $(e.currentTarget);

        if ($this.parent().parent().parent().hasClass('menu')) {
            $mobileNav.removeClass('sub-open');
            $mobileNav.find('ul').removeClass('current');
            $this.parent().removeClass('active');
        } else {
            $this.parent().parent().parent().addClass('current');
            $this.parent().removeClass('active current');
        }
    });

    $closeModal.on('click', (e) => {
        e.preventDefault();
        $burger.removeClass('active');
        $mobileNav.removeClass('active');
        $mobileNav.removeClass('sub-open');
        $masthead.removeClass("active");
        $mobileNav.find('ul').removeClass('current active');
    });

    $searchTrigger.on('click', (e) => {
        e.preventDefault();

        //Search Modal
    });

    $mastfoot.find('.menu-item-has-children').on('click', (e) => {

        if (!animating) {
            animating = true;
            const $this = $(e.currentTarget);

            if (!$this.hasClass('active')) {
                $this.addClass('active');

                $this.children('ul').slideDown(() => {
                    animating = false;
                });
            } else {
                $this.removeClass('active');
                $this.children('ul').slideUp(() => {
                    animating = false;
                });
            }
        }

    });


    function menuItemStates($this, type) {

        if (type === 'leave') {
            $this.removeClass('active');
            $this.find('li').removeClass('active');
        } else {
            if (!$this.closest('li').hasClass('active')) {
                //$this.closest('li').addClass('active');
                $('.menu-item-has-children').removeClass('active');
                $this.parents('li').addClass('active');
                $this.addClass('active');
            } else {
                if (type === 'click') {
                    $this.removeClass('active');
                    $this.find('li').removeClass('active');
                }
            }
        }

        if ($('.masthead .menu-item-has-children.active').length) {
            $masthead.addClass('active-hovered');
        } else {
            $masthead.removeClass('active-hovered');
        }
    }

    $('.menu-item-has-children').on('mouseenter', (e) => {
        //const $this = $(e.currentTarget)
        menuItemStates($(e.currentTarget), 'enter');
    });

    $('.menu-item-has-children').on('mouseleave', (e) => {
        menuItemStates($(e.currentTarget), 'leave');
    });

    $('.masthead .menu-item-has-children .sub-menu li a').on('click', (e) => {
        console.log(e.currentTarget.closest(".menu-item-has-children"));
        menuItemStates($(e.currentTarget.closest(".menu-item-has-children")), 'leave');
    });


    const MOBILE_MAX_WIDTH = 991;
    let lastScrollTop = 0;

    const updateScroll = () => {
        if (updateScroll._tick) {
            cancelAnimationFrame(updateScroll._tick);
        }

        updateScroll._tick = requestAnimationFrame(function () {
            updateScroll._tick = null;

            if (window.innerWidth <= MOBILE_MAX_WIDTH) {
                let st = $(window).scrollTop();

                if (st > 100) {
                    $masthead.addClass('scrolled');

                    if (st > lastScrollTop) {
                        $masthead.removeClass('in-view');
                    } else {
                        $masthead.addClass('in-view');
                    }
                } else {
                    $masthead.removeClass('scrolled');
                }

                lastScrollTop = st;
            }
        });
    };

    // Add scroll listener if screen is <= MOBILE_MAX_WIDTH
    if (window.innerWidth <= MOBILE_MAX_WIDTH) {
        window.addEventListener('scroll', updateScroll);
        updateScroll._enabled = true;
        updateScroll();
    }

    // Optional: handle resize to toggle listener
    window.addEventListener('resize', () => {
        if (window.innerWidth <= MOBILE_MAX_WIDTH && !updateScroll._enabled) {
            window.addEventListener('scroll', updateScroll);
            updateScroll._enabled = true;
            updateScroll();
        } else if (window.innerWidth > MOBILE_MAX_WIDTH && updateScroll._enabled) {
            window.removeEventListener('scroll', updateScroll);
            updateScroll._enabled = false;
            $masthead.removeClass('scrolled in-view'); // Optional: cleanup
        }
    });


}

export default Menu;
