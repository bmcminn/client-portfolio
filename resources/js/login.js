(function($) {


    $(document).on('submit', 'form', formHandler)


    function formHandler(e) {

        e.preventDefault();

        console.log(e);
        let formData = $(e).serialize();

        console.log(formData);
    }


})(jQuery);
