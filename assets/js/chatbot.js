jQuery(document).ready(function ($) {

    /* -----------------------------------------------
       Helpers
    ----------------------------------------------- */
    function clearErrors() {
        $('.wlc-error').text('');
        $('.nlc-has-error').removeClass('nlc-has-error');
        $('#wlc-response').removeClass('nlc-success nlc-error-msg').hide();
    }

    function setError(fieldId, errorId, msg) {
        $('#' + fieldId).addClass('nlc-has-error');
        $('#' + errorId).text(msg);
    }

    function validateForm() {
        clearErrors();
        let isValid = true;

        var name    = $('#wlc-name').val().trim();
        var email   = $('#wlc-email').val().trim();
        var phone   = $('#wlc-phone').val().trim();
        var message = $('#wlc-message').val().trim();

        if (!name) {
            setError('wlc-name', 'wlc-name-error', 'Please enter your name.');
            isValid = false;
        }
        if (!email) {
            setError('wlc-email', 'wlc-email-error', 'Please enter your email address.');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError('wlc-email', 'wlc-email-error', 'Please enter a valid email address.');
            isValid = false;
        }
        if (!phone) {
            setError('wlc-phone', 'wlc-phone-error', 'Please enter your phone number.');
            isValid = false;
        }
        if (!message) {
            setError('wlc-message', 'wlc-message-error', 'Please enter a message.');
            isValid = false;
        }

        return isValid;
    }

    function showResponse(msg, type) {
        var $r = $('#wlc-response');
        var icon = type === 'success'
            ? '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0"><circle cx="10" cy="10" r="9" fill="#10b981" opacity=".2"/><path d="M6 10l3 3 5-5" stroke="#065f46" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            : '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0"><circle cx="10" cy="10" r="9" fill="#ef4444" opacity=".2"/><path d="M10 6v5M10 14h.01" stroke="#991b1b" stroke-width="1.8" stroke-linecap="round"/></svg>';

        $r.removeClass('nlc-success nlc-error-msg')
          .addClass(type === 'success' ? 'nlc-success' : 'nlc-error-msg')
          .html(icon + '<span>' + msg + '</span>')
          .css('display', 'flex');
    }

    /* -----------------------------------------------
       Form submission
    ----------------------------------------------- */
    $('#wlc-submit').on('click', function (e) {
        e.preventDefault();

        if (!validateForm()) { return; }

        var $btn = $(this);
        $btn.addClass('is-loading').prop('disabled', true);

        var data = {
            name:    $('#wlc-name').val(),
            email:   $('#wlc-email').val(),
            phone:   $('#wlc-phone').val(),
            message: $('#wlc-message').val()
        };

        $.ajax({
            url:         npleadchat_api.url,
            method:      'POST',
            data:        JSON.stringify(data),
            contentType: 'application/json',
            beforeSend:  function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', npleadchat_api.nonce);
            },
            success: function (res) {
                showResponse(res.message || 'Thanks! We\'ll be in touch soon.', 'success');
                $('#wlc-name, #wlc-email, #wlc-phone, #wlc-message').val('');
            },
            error: function () {
                showResponse('Something went wrong. Please try again.', 'error');
            },
            complete: function () {
                $btn.removeClass('is-loading').prop('disabled', false);
            }
        });
    });

    /* -----------------------------------------------
       Remove error state on input
    ----------------------------------------------- */
    $('#wlc-chatbot input, #wlc-chatbot textarea').on('input', function () {
        $(this).removeClass('nlc-has-error');
        var errorId = $(this).attr('id') + '-error';
        $('#' + errorId).text('');
    });

    /* -----------------------------------------------
       Floating widget toggle — with animation
    ----------------------------------------------- */
    var $popup  = $('#wlc-chat-popup');
    var $trigBtn = $('#wlc-floating-btn');

    $trigBtn.on('click', function () {
        if ($popup.is(':visible')) {
            closePopup();
        } else {
            openPopup();
        }
    });

    $('#wlc-chat-close').on('click', function () {
        closePopup();
    });

    function openPopup() {
        $popup.removeClass('nlc-closing').show().addClass('nlc-open');
        $trigBtn.addClass('is-open');
        // Focus first input for accessibility
        setTimeout(function () { $('#wlc-name').trigger('focus'); }, 320);
    }

    function closePopup() {
        $popup.removeClass('nlc-open').addClass('nlc-closing');
        $trigBtn.removeClass('is-open');
        setTimeout(function () { $popup.hide().removeClass('nlc-closing'); }, 200);
    }

    /* Close on outside click */
    $(document).on('click', function (e) {
        if ($popup.is(':visible') &&
            !$(e.target).closest('#wlc-chat-popup').length &&
            !$(e.target).closest('#wlc-floating-btn').length) {
            closePopup();
        }
    });

    /* Close on Escape key */
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $popup.is(':visible')) {
            closePopup();
        }
    });
});
