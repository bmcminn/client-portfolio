(function($) {
    let $form = $('form');

    $form.on('submit', function(e) {
        e.preventDefault();

        $inputs = $form.find('input, select');

        let formData = {};

        $inputs.each(function(index, input) {

            let $self = $(input);

            formData[$self.attr('name')] = $self.val();
        });

        let $notice = $form.find('[name="notice"]');


        $.post(
            $form.attr('action')
        ,   formData
        ,   function(data, res) {

                // reset error fields
                $notice.addClass('is-hidden');
                $form.find('input').removeClass('is-danger');
                $('.help').html('');

                if (data.redirect) {
                    window.location = data.redirect;

                } else {
                    Object.keys(data).forEach(function(field, index) {
                        if (field === 'notice') {
                            return;
                        }
                        $('[name="' + field + '"]')
                            .addClass('is-danger')
                            .next()
                            .html(data[field])
                            ;
                    });

                    if (data.notice) {
                        $notice
                            .removeClass('is-hidden')
                            .addClass('is-' + data.notice.level)
                            .html(data.notice.message)
                            ;

                        if ($notice.attr('data-animate') !== '') {
                            animateNotice($notice);
                        }
                    }
                }
            }
        );

    });


    function animateNotice($target) {
        let classList       = $target.attr('data-animate') + ' animated';
        let eAnimationEnd   = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        $target
            .addClass(classList)
            .one(eAnimationEnd, function() {
                $(this).removeClass(classList);
            })
        ;
    }


})(jQuery);
