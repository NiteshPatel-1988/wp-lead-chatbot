jQuery(document).ready(function($){

    function clearErrors() {
        $('.wlc-error').text('');
        $('#wlc-response').text('');
    }

    function validateForm() {
        clearErrors();
        let isValid = true;

        let name = $('#wlc-name').val().trim();
        let email = $('#wlc-email').val().trim();
        let phone = $('#wlc-phone').val().trim();
        let message = $('#wlc-message').val().trim();

        if (name === '') {
            $('#wlc-name-error').text('Please enter your name.');
            isValid = false;
        }
        if (email === '') {
            $('#wlc-email-error').text('Please enter your email.');
            isValid = false;
        } else {
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $('#wlc-email-error').text('Please enter a valid email.');
                isValid = false;
            }
        }
        if (phone === '') {
            $('#wlc-phone-error').text('Please enter your phone number.');
            isValid = false;
        }
        if (message === '') {
            $('#wlc-message-error').text('Please enter your message.');
            isValid = false;
        }

        return isValid;
    }

    // Submit Button Click
    $('#wlc-submit').click(function (e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        let data = {
            name: $('#wlc-name').val(),
            email: $('#wlc-email').val(),
            phone: $('#wlc-phone').val(),
            message: $('#wlc-message').val()
        };

        $.ajax({
            url: npleadchat_api.url,
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            beforeSend: function(xhr){
                xhr.setRequestHeader("X-WP-Nonce", npleadchat_api.nonce);
            },
            success: function(res){
                $('#wlc-response').text(res.message);
            },
            error: function(err){
                console.error(err);
                $('#wlc-response').text("Submission failed.");
            }
        });
    });


    // Floating Widget Toggle
    $('#wlc-floating-btn').click(function(){
        $('#wlc-chat-popup').fadeToggle();
    });

    $('#wlc-chat-close').click(function(){
        $('#wlc-chat-popup').fadeOut();
    });
});
