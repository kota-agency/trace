import resizeEvent from '../utilities/triggerResizeEvent';
import InfiniteScroll from 'infinite-scroll';
import imagesLoaded from 'imagesloaded';
import AOS from 'aos';

const Posts = (() => {

    const elem = document.querySelector('.inf-grid');

    if ($(elem).length && $('.next-posts-link a ').length) {

        const infScroll = new InfiniteScroll(elem, {
            // options
            path: '.next-posts-link a',
            append: '.inf-post',
            history: false,
            button: '.pagination-infinite__button',
            scrollThreshold: false,
            status: ".page-load-status"
        });

        infScroll.imagesLoaded = imagesLoaded;

        infScroll.on("append", (
            event,
            response,
            path,
            items
        ) => {
            $(items).addClass("appended-item");
            AOS.refresh();
            window.dispatchEvent(resizeEvent);
            infScroll.imagesLoaded( () => {
                $(items)
                    .find("img")
                    .each( (index, img) => {
                        img.outerHTML = img.outerHTML;
                    });
            });

        });

    }

});

export default Posts;
