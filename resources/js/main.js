window.isUserAuthenticated();


// (function($) {

//     let $doc        = $(document)


//     // Define HTML state classes to be used
//     let errorClass  = 'error';
//     let activeClass = 'active';
//     let hiddenClass = 'visually-hidden';


//     // register REGEX validator strings
//     let regex = {};

//     // NOTE: we only validate the email follows the format xxx@xxx.xxx, no more complicated than that
//     regex.email         = /\S+@\S+\.\S{2,}$/i;
//     regex.nonInputTypes = /submit|button|hidden|image/i;


//     // TODO: phone number regex
//     // regex.phone = /

//     // ensure form inputs have an error message field on the screen
//     $('form').each(function(index, form) {
//         $(form)
//             .find('input, select, textarea')
//             .after(`<small class="form-error-msg ${hiddenClass}"></small>`);
//     });


//     // Register event handlers
//     $doc.on('click', '[user-form-submit]', userFormHandler);


//     /**
//      * A generic form handler that uses HTML validation, method and action attributes to validate, serialize, and submit the form data
//      * @param  {event}  e The event object that triggered this handler
//      * @return {null}
//      */
//     function userFormHandler(e) {
//         e.preventDefault();

//         // register the submit handler as a jQuery object
//         let $submit = $(this);

//         $submit.disable   = function() { this.attr('disabled', true); }
//         $submit.enable    = function() { this.attr('disabled', false); }

//         // get the parent form element
//         let $form = $submit.parents('form');

//         let errors = false;
//         let loginPostData = {};

//         // validate the form has an "action" and "method" attribute
//         if (!$form.attr('action') || !$form.attr('method')) {
//             console.error(
//                 '[FATAL] form',
//                 '#' + $form.attr('id'),
//                 'is missing an "action" or "method" attribute.'
//             );

//             $submit.enable();
//             return;
//         }


//         // serialize each form input
//         $form.find('input, select, textarea')
//             .each(function(e) {
//                 let $this = $(this);

//                 // skip submit, hidden, button, and image type inputs
//                 if (regex.nonInputTypes.test($this.attr('type'))) {
//                     return;
//                 }

//                 // run form validation
//                 if (!formValidate($this)) {
//                     errors = true;
//                     return;
//                 }

//                 // TODO: run validation stuffs here
//                 loginPostData[$this.attr('id')] = $this.val();
//             });


//         // if we had errors, bail on the form submission
//         if (errors) { return; }

//         // define ajax handler methods
//         let submitHandler   = axios[$form.attr('method')];

//         // get our success and error handler methods if defined in the form
//         // NOTE: success and error handlers defined in the form should be set on the window object to ensure we have access to it
//         let successHandler  = $form.attr('successHandler') || function(res) { console.log(res); };
//         let errorHandler    = $form.attr('errorHandler')   || function(err) { console.error(err); };

//         // console.info('[HANDLERS]', successHandler, errorHandler);

//         submitHandler($form.attr('action'), loginPostData)
//             .then(function(res) {
//                 // call the success handler
//                 window[successHandler](res);
//                 $submit.enable();
//             })
//             .catch(function (err) {
//                 window[errorHandler](err);
//                 $submit.enable();
//             });
//     }


//     /**
//      * Validates the given input data based on the html validation attributes
//      * @param  {el}     $input  The target form input to be validated
//      * @return {bool}           Whether the field validated correctly or not
//      */
//     function formValidate($input) {

//         // Reset form input element and error message
//         formErrorReset($input);

//         // NOTE: we do not allow user input to use leading/trailing spaces
//         let value = $input.val().trim();

//         // is the input required?
//         if ($input.attr('required')) {
//             if (value === '') {
//                 return formError($input, 'This field is required.');
//             }
//         }

//         // determine the input type and validate against that
//         switch ($input.attr('type').toLowerCase()) {

//             // is the input a valid email?
//             case 'email':
//                 return !regex.email.test(value)
//                     ? formError($input, 'Must be a valid email address<br>(ex: name@domain.com)')
//                     : true
//                     ;
//                 break;

//             case 'tel':
//                 break;

//             case 'checkbox':
//             case 'radio':
//                 break;

//             case 'color':
//                 break;

//             case 'file':
//                 break;

//             case 'password':
//             case 'text':
//             case 'search':
//                 break;

//             case 'url':
//                 break;

//             case 'week':
//             case 'month':
//             case 'time':
//             case 'date':
//             case 'datetime':
//             case 'datetime-local':
//                 break;

//             case 'number':
//             case 'range':
//                 break;
//         }

//         return true;
//     }


//     /**
//      * Formats the UI to expose the necessary field
//      * @param  {el}     $input  The input field in error
//      * @param  {string} msg     The error message displayed to the user
//      * @param  {bool}   reset   Boolean that forces us to reset the input error state
//      * @return {bool}           Always returns false
//      */
//     function formError($input, msg) {
//         $input
//             .addClass(errorClass)
//             .next()
//                 .html(msg)
//                 .removeClass(hiddenClass)
//         ;

//         return false;
//     }


//     /**
//      * Resets the given form input error messaging
//      * @param  {el}     $input  The input field in question to reset
//      * @return {null}
//      */
//     function formErrorReset($input) {
//         $input
//             .removeClass(errorClass)
//             .next()
//                 .html('')
//                 .addClass(hiddenClass)
//             ;
//     }


//     window.loginSuccessHandler = function(res) {
//         console.log('submission success', res);
//         window.location = '/dashboard';
//     }

//     window.resetPasswordSuccessHandler = function(res) {
//         console.log('password reset request submitted');
//     }

//     window.formErrorHandler = function(err) {
//         console.error('submission failed', err);
//     }

// })(jQuery);
