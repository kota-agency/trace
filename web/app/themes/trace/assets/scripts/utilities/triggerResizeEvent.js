const resizeEvent = window.document.createEvent('UIEvents');
resizeEvent.initUIEvent('resize', true, false, window, 0);


export default resizeEvent;
