$(document).ready(function () {
    // $('#back_redirect').click(function () {
    //     history.go(-1);
    // });

    $(document).on("change", '.js-select2', function () {
        console.log('select change')
        if ($(this).val()) {
            console.log($(this))
            $(this).parent('.form-group').find('label.error').hide()
        } else {
            $(this).parent('.form-group').find('label.error').show()
            // $(this).next('label').show()
            // $(this).next('span').show()
            // $(this).parent().find('label').show()
        }
    });
});