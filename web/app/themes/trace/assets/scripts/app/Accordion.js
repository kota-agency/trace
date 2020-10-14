function Accordion() {

    const $dropdown = $('.dropdown__trigger');

    let animating = false;

    $dropdown.on('click', (e) => {
        e.preventDefault();

        const $this = $(e.currentTarget);

        if (!animating) {
            animating = true;
            if (!$this.parent().hasClass('active')) {
                $this.parent().addClass('active');

                $this.next().slideDown(() => {
                    animating = false;
                });
            } else {
                $this.parent().removeClass('active');

                $this.next().slideUp(() => {
                    animating = false;
                });
            }
        }
    });


}

export default Accordion;
