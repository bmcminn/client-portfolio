(function($) {

    let $doc        = $(document)


    // Define HTML state classes to be used
    let errorClass  = 'error';
    let activeClass = 'active';
    let hiddenClass = 'visually-hidden';


    // register REGEX validator strings
    let regex = {};

    // NOTE: we only validate the email follows the format xxx@xxx.xxx, no more complicated than that
    regex.email = /\S+@\S+\.\S{2,}$/i;

    // TODO: phone number regex
    // regex.phone = /

    // ensure form inputs have an error message field on the screen
    $('form').each(function(index, form) {
        $(form).find('input, select, textarea')
            .each(function(index, input) {
                $(input).parent()
                    .append(`<small class="form-error-msg ${hiddenClass}"></small>`);
            });
    });


    // Register event handlers
    $doc.on('click', '[user-form-submit]', userFormHandler);


    /**
     * A generic form handler that uses HTML validation, method and action attributes to validate, serialize, and submit the form data
     * @param  {event}  e The event object that triggered this handler
     * @return {null}
     */
    function userFormHandler(e) {
        e.preventDefault();

        // register the submit handler as a jQuery object
        let $submit = $(this);

        $submit.disable   = function() { this.attr('disabled', true); }
        $submit.enable    = function() { this.attr('disabled', false); }

        // get the parent form element
        let $form = $submit.parents('form');

        let errors = false;
        let loginPostData = {};

        // validate the form has an "action" and "method" attribute
        if (!$form.attr('action') || !$form.attr('method')) {
            console.error(
                '[FATAL] form',
                '#' + $form.attr('id'),
                'is missing an "action" or "method" attribute.'
            );

            $submit.enable();
            return;
        }


        // serialize each form input
        $form.find('input, select, textarea')
            .each(function(e) {
                let $this = $(this);

                // run form validation
                if (!formValidate($this)) {
                    errors = true;
                    return;
                }

                // TODO: run validation stuffs here
                loginPostData[$this.attr('id')] = $this.val();
            });


        // if we had errors, bail on the form submission
        if (errors) { return; }


        console.log(loginPostData);


        // submit form data to action route
        let submission = axios[$form.attr('method')];

        submission($form.attr('action'), loginPostData)
            .then(function(res) {
                console.log('submission success', res);
                $submit.enable();
            });
    }


    /**
     * Validates the given input data based on the html validation attributes
     * @param  {el}     $input  The target form input to be validated
     * @return {bool}           Whether the field validated correctly or not
     */
    function formValidate($input) {

        // Reset form input element and error message
        formError($input, '', true);

        // NOTE: we do not allow user input to use leading/trailing spaces
        let value = $input.val().trim();

        // is the input required?
        if ($input.attr('required')) {
            if (value === '') {
                return formError($input, 'This field is required.');
            }
        }

        switch ($input.attr('type').toLowerCase()) {

            // is the input a valid email?
            case 'email':
                return !regex.email.test(value)
                    ? formError($input, 'Must be a valid email address<br>(ex: name@domain.com)')
                    : true
                    ;
                break;
        }

        return true;
    }


    /**
     * Formats the UI to expose the necessary field
     * @param  {el}     $input  The input field in error
     * @param  {string} msg     The error message displayed to the user
     * @param  {bool}   reset   Boolean that forces us to reset the input error state
     * @return {bool}           Always returns false
     */
    function formError($input, msg, reset) {
        if (reset) {
            $input
                .removeClass(errorClass)
                .next()
                    .text('')
                    .addClass(hiddenClass)
                ;

            return;
        }

        $input
            .addClass(errorClass)
            .next()
                .html(msg)
                .removeClass(hiddenClass)
        ;

        return false;
    }



})(jQuery);
