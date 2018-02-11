(function($) {

    let $doc        = $(document)
    let $loginForm  = $('[login-form]');


    window.routes = {
        login: '/auth/login'
    };


    function loginSubmit(e) {
        e.preventDefault();

        console.log('sdfjksdlfsjd');

        let $this = $(this);

        $this.disabled = true;

        let errors = false;
        let loginPostData = {};

        // serialize inputs
        $loginForm.find('input')
            .each(function(e) {
                let $this = $(this);
                // TODO: run validation stuffs here
                loginPostData[$this.attr('id')] = $this.val();
            });

        console.log(loginPostData);

        axios.post(window.routes.login, loginPostData)
            .then(function(res) {
                console.log('submission success', res);
            })
            ;

        // // submit request

        //     // on success, redirect to user dashbaord

        //     // on fail, render error messaging on form

        // console.log('login submitted');
    }


    $doc.on('click', '[login-submit]', loginSubmit);


})(jQuery);



// (function($){

//     // let $mainNav            = $('#main-nav');
//     // let $mainView           = $('.app-main-container');
//     let $doc                = $(document);
//     let sidebarOpenClass    = 'open';
//     let activeClass         = 'active';

//     // const toggleMainNav = function (e) {
//     //     e.preventDefault();
//     //     $mainNav.toggleClass(sidebarOpenClass);
//     //     $mainView.toggleClass(sidebarOpenClass);

//     //     var clickAway = function (e) {
//     //         if ($mainNav[0] !== e.target
//     //         &&  !$.contains($mainNav[0], e.target)
//     //         ) {
//     //             $mainNav.removeClass(sidebarOpenClass);
//     //             $mainView.removeClass(sidebarOpenClass);
//     //             $(document.body).off('click', clickAway);
//     //         }
//     //     };

//     //     $(document.body).on('click', clickAway);
//     // }

//     // $doc.on('click', '#main-nav-toggle', toggleMainNav);

//     // $doc.on('click', '.app-nav-link a', function(e) {
//     //     e.preventDefault(); // stop the site from navigating away from demo
//     //     $('.app-nav-link a').removeClass(activeClass);
//     //     $(this).toggleClass(activeClass);
//     // });


//     // // HIGHLIGHT CODE SAMPLES
//     // $('pre code').each(function() {
//     //     let $this   = $(this);
//     //     let lang    = $this.prop('lang');
//     //     let code    = $this.html();

//     //     code = code
//     //         .replace(/<br>/g, 'BRBRBR')
//     //         .replace(/\&nbsp;/g, ' ')
//     //         .replace(/\&lt;/g, '<')
//     //         .replace(/\&gt;/g, '>')
//     //         ;

//     //     var html = Prism.highlight(code, Prism.languages[lang]);

//     //     html = html
//     //         .replace(/BRBRBR/g, '<br>')
//     //         ;

//     //     $this.html(html);
//     // });


//     // trigger focus when clicking on non-form elements in .input-groups
//     $doc.on('click', '.input-group *', function(e) {
//         let targetTag = e.target.tagName.toLowerCase();

//         if (targetTag !== 'input'
//         ||  targetTag !== 'select'
//         ||  targetTag !== 'button'
//         ) {
//             $(this).siblings('input, select').trigger('focus');
//         }
//     });


//     $doc.on('focus', 'input, textarea', function() {
//         let $this = $(this);

//         if ($this.parent().hasClass('input-group')) {
//             $this.parent().addClass('focus');
//         }
//     });


//     $doc.on('blur', 'input, textarea', function() {
//         let $this = $(this);

//         if ($this.parent().hasClass('input-group')) {
//             $this.parent().removeClass('focus');
//         }
//     });


// })(jQuery);
