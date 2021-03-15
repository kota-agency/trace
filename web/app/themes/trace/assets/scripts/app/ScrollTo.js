function scrollTo() {

    if (window.location.hash) {
        const $scrollToEl = $(window.location.hash);

        if(!$scrollToEl.hasClass('modal')) {

            if ($scrollToEl.length) {
                $('html, body').animate({
                    scrollTop: $scrollToEl.offset().top - $('.masthead').outerHeight()
                }, 500);
            }
        } else {
            $scrollToEl.fadeIn();
        }

    }



    $(".scroll-anchor").on('click', (e) => {
        const $this = $(e.currentTarget);

        if ($this.attr('href') !== '#') {
            $('html, body').animate({
                scrollTop: $($this.attr('href')).offset().top - $('.masthead').outerHeight()
            }, 1000);
        }

    });


    $(".scroll-top").on('click', (e) => {
        $('html, body').animate({
            scrollTop: 0
        }, 1500);
    });

    $('a:not(.gated-file)').on('click', (e) => {

        const $this = $(e.currentTarget);

        if($this.attr('href').startsWith('#') && $this.attr('href').length > 1 && $($this.attr('href')).length) {

            if ($($this.attr('href')).hasClass('modal'))
            {
                e.preventDefault();

                $($this.attr('href')).fadeIn();

            }
        }

    });

}

export default scrollTo;
