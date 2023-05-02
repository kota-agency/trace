import scrollbarWidth from "../utilities/scrollbarWidth";
var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
var is_safari = navigator.userAgent.indexOf("Safari") > -1;

function FullWidth() {

    const fullWidth = document.querySelectorAll('.full-width');

    if (!fullWidth) {
        return;
    }

    const siteMain = document.querySelector('.site-wrapper');


    let safariAgent = userAgentString.indexOf("Safari") > -1;

    fullWidth.forEach( (el) => {
        if(!isSafariBrowser) {
            el.style.width = (window.innerWidth - scrollbarWidth()) + 'px';
            el.style.marginLeft = -siteMain.getBoundingClientRect().left + 'px';
        }
        $(el).addClass('width-calculated');
    });
}

function isSafariBrowser(){
    if (is_safari){
        if (is_chrome)  // Chrome seems to have both Chrome and Safari userAgents
            return false;
        else
            return true;
    }
    return false;
}

export default FullWidth;
