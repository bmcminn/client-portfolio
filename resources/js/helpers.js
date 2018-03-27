/**
 * Triggers hover/touch toggle mixed-touch enabled devices
 * @sauce: https://stackoverflow.com/a/30303898/3708807
 * @return {[type]} [description]
 */
function watchForHover() {
    var hasHoverClass   = false;
    var lastTouchTime   = 0;

    function enableHover() {
        // filter emulated events coming from touch events
        if (new Date() - lastTouchTime < 500) return;
        if (hasHoverClass) return;

        document.documentElement.classList.add('no-touch');
        hasHoverClass = true;

    }

    function disableHover() {
        if (!hasHoverClass) return;

        document.documentElement.classList.remove('no-touch');
        hasHoverClass = false;
    }

    function updateLastTouchTime() {
        lastTouchTime = new Date();
    }

    document.addEventListener('touchstart', updateLastTouchTime, true);
    document.addEventListener('touchstart', disableHover, true);
    document.addEventListener('mousemove', enableHover, true);

    enableHover();
}

watchForHover();
