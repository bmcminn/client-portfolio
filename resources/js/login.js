(function($) {


    $(document).on('submit', 'form', formHandler)


    function formHandler(e) {
        console.debug('-----------------------------------');
        console.debug('Login form submitted');

        e.preventDefault();

        let $this = $(e.target);

        let API = window.apiRoutes;

        let loginMethod = API['api.auth.login'].method;
        let loginRoute = API['api.auth.login'].route;

        let params = {};

        let fields = $this.find('input')
            .each(function(index, el) {
                params[el.name] = el.value;
            });


        console.debug(params);

        axios.post(loginRoute, params)
            .then(success)
            .catch(errs)
            ;

    }



    function success(res) {
        console.log(res);
    }


    function errs(err) {
        console.error(err);
    }


})(jQuery);

/*
johndoe@example.com
johndoe
 */
