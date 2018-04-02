// check if user is authenticated
window.isUserAuthenticated();


(function($) {

    $(document).on('submit', 'form', formHandler)


    function formHandler(e) {
        console.debug('-----------------------------------');
        console.debug('Login form submitted');

        e.preventDefault();

        let $this = $(e.target);

        // let loginRoute  = window.appRoute('api.auth.login');
        let loginRoute = '/auth/login';

        let params = {};

        let fields = $this.find('input')
            .each(function(index, el) {
                params[el.name] = el.value;
            });

        window.api.post(loginRoute, params)
            .then(loginSuccess)
            .catch(loginErrs)
            ;
    }



    function loginSuccess(res) {
        console.debug('-----------------------------------');
        console.debug('Login form success()');
        console.log(res);

        sessionStorage.setItem('token', res.data.data.token);
        window.location.href = '/dashboard';
    }


    function loginErrs(err) {
        console.debug('-----------------------------------');
        console.debug('Login form errs()');
        console.error(err);
    }


})(jQuery);

/*
johndoe@example.com
johndoe
 */
