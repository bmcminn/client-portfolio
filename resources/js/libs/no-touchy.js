
/**
 * Debounce: throttle all the things more for performance and profit
 * @sauce: https://davidwalsh.name/javascript-debounce-function
 * @sauce: http://underscorejs.org/#debounce
 * @param  {function}   func        The function to be debounced
 * @param  {integer}    wait        The time in milliseconds to delay execution before checking the event
 * @param  {boolean}    immediate   Boolean flag to trigger the callback before timeout is checked instead of after
 * @return {function}               Returns a new version of the original function to be called on the next timeout cycle
 */

// function debounce(func, wait, immediate) {
//   let timeout;
//   return function() {
//     let context = this, args = arguments;
//     let later = function() {
//       timeout = null;
//       if (!immediate) func.apply(context, args);
//     };
//     let callNow = immediate && !timeout;
//     clearTimeout(timeout);
//     timeout = setTimeout(later, wait);
//     if (callNow) func.apply(context, args);
//   };
// };


(function() {

    let $body = document.querySelector('body');
    let touchClass = 'touchy';

    /**
     * Toggles touch and no-touch states based on pointer input type
     * @param  {event}      The event object
     */
    let noTouchy = _.debounce(function (e) {

        // on touch, disable hover
        if (e.type.toLowerCase() === 'touchstart') {
            $body.classList.add(touchClass);
            $body.classList.remove('no-' + touchClass);
            return;

        // else enable hover
        } else {
            $body.classList.remove(touchClass);
            $body.classList.add('no-' + touchClass);
            return;
        }

    }, 200, true);

    // Register click/touch event handler(s)
    document.addEventListener('mousemove', noTouchy, false);
    document.addEventListener('touchstart', noTouchy, false);
})();

