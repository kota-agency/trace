import scrollbarWidth from "../utilities/scrollbarWidth";

function FullWidth() {

    const fullWidth = document.querySelectorAll('.full-width');

    if (!fullWidth) {
        return;
    }

    const siteMain = document.querySelector('.site-wrapper');


    fullWidth.forEach( (el) => {
        el.style.width = (window.innerWidth - scrollbarWidth()) + 'px';
        el.style.marginLeft = -siteMain.getBoundingClientRect().left + 'px';
        $(el).addClass('width-calculated');
    });
}

export default FullWidth;
