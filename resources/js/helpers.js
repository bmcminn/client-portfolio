
/**
 * Renders a "header" string in the console to denote when a given process is fired
 * @param  {string} header Header text to render in console
 * @return {void}
 */
window.logHeader = function(header) {
    console.log('-----------------------------------');
    console.log(header);
}


/**
 * [api description]
 * @type {[type]}
 */
api = axios.create({
    baseURL: '/api',
    timeout: 1000,
    headers: {
        'Authorization': 'Bearer ' + sessionStorage.getItem('token')
    }
});

if (sessionStorage.getItem('token')) {
    window.api = api;
}



/**
 * [isUserAuthenticated description]
 * @return {void}
 */
window.isUserAuthenticated = function() {
    if (sessionStorage.getItem('token')) {
        api.get('/auth/user')
            .then(function(res) {
                window.logHeader('Check is user session valid');
                console.log(res);
                // sessionStorage.setItem('token', res.data.token);
            })
            .catch(function(err) {
                window.logHeader('User session invalid');
                console.error(err);
                sessionStorage.clear();
                window.location.href = window.appRoute('app.login');
            });

    } else {
        sessionStorage.clear();

    }
}


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

    document.addEventListener('touchstart', updateLastTouchTime,    true);
    document.addEventListener('touchstart', disableHover,           true);
    document.addEventListener('mousemove',  enableHover,            true);

    enableHover();
}

watchForHover();
