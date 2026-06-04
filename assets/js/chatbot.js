jQuery(document).ready(function ($) {
    var api = window.npleadchat_api || {};
    var strings = $.extend({
        nameRequired: '',
        emailRequired: '',
        emailInvalid: '',
        phoneRequired: '',
        messageRequired: '',
        fallbackSuccess: '',
        fallbackError: ''
    }, api.i18n || {});

    /* -----------------------------------------------
       Helpers
    ----------------------------------------------- */
    function clearErrors($form) {
        $form.find('.wlc-error').text('');
        $form.find('.nlc-has-error').removeClass('nlc-has-error');
        $form.find('#wlc-response').removeClass('nlc-success nlc-error-msg').hide();
    }

    function setError($form, fieldId, errorId, msg) {
        $form.find('[id="' + fieldId + '"]').addClass('nlc-has-error');
        $form.find('[id="' + errorId + '"]').text(msg);
    }

    function validateForm($form) {
        clearErrors($form);
        let isValid = true;

        var name    = $form.find('[id="wlc-name"]').val().trim();
        var email   = $form.find('[id="wlc-email"]').val().trim();
        var phone   = $form.find('[id="wlc-phone"]').val().trim();
        var message = $form.find('[id="wlc-message"]').val().trim();

        if (!name) {
            setError($form, 'wlc-name', 'wlc-name-error', strings.nameRequired);
            isValid = false;
        }
        if (!email) {
            setError($form, 'wlc-email', 'wlc-email-error', strings.emailRequired);
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError($form, 'wlc-email', 'wlc-email-error', strings.emailInvalid);
            isValid = false;
        }
        if (!phone) {
            setError($form, 'wlc-phone', 'wlc-phone-error', strings.phoneRequired);
            isValid = false;
        }
        if (!message) {
            setError($form, 'wlc-message', 'wlc-message-error', strings.messageRequired);
            isValid = false;
        }

        return isValid;
    }

    function showResponse($form, msg, type) {
        var $r = $form.find('#wlc-response');
        var icon = type === 'success'
            ? '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0"><circle cx="10" cy="10" r="9" fill="#10b981" opacity=".2"/><path d="M6 10l3 3 5-5" stroke="#065f46" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            : '<svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0"><circle cx="10" cy="10" r="9" fill="#ef4444" opacity=".2"/><path d="M10 6v5M10 14h.01" stroke="#991b1b" stroke-width="1.8" stroke-linecap="round"/></svg>';

        $r.removeClass('nlc-success nlc-error-msg')
          .addClass(type === 'success' ? 'nlc-success' : 'nlc-error-msg')
          .empty()
          .append(icon)
          .append($('<span>').text(msg))
          .css('display', 'flex');
    }

    /* -----------------------------------------------
       Form submission
    ----------------------------------------------- */
    $('[id="wlc-submit"]').on('click', function (e) {
        e.preventDefault();

        var $form = $(this).closest('#wlc-chatbot');

        if (!validateForm($form)) { return; }

        var $btn = $(this);
        $btn.addClass('is-loading').prop('disabled', true);

        var data = {
            name:       $form.find('[id="wlc-name"]').val(),
            email:      $form.find('[id="wlc-email"]').val(),
            phone:      $form.find('[id="wlc-phone"]').val(),
            message:    $form.find('[id="wlc-message"]').val(),
            source_url: window.location.href,
            website:    $form.find('[id="wlc-website"]').val()
        };

        $.ajax({
            url:         api.url,
            method:      'POST',
            data:        JSON.stringify(data),
            contentType: 'application/json',
            beforeSend:  function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', api.nonce);
            },
            success: function (res) {
                showResponse($form, res.message || api.successMessage || strings.fallbackSuccess, 'success');
                $form.find('[id="wlc-name"], [id="wlc-email"], [id="wlc-phone"], [id="wlc-message"], [id="wlc-website"]').val('');
            },
            error: function () {
                showResponse($form, strings.fallbackError, 'error');
            },
            complete: function () {
                $btn.removeClass('is-loading').prop('disabled', false);
            }
        });
    });

    /* -----------------------------------------------
       Remove error state on input
    ----------------------------------------------- */
    $(document).on('input', '[id="wlc-chatbot"] input, [id="wlc-chatbot"] textarea', function () {
        $(this).removeClass('nlc-has-error');
        var errorId = $(this).attr('id') + '-error';
        $(this).closest('[id="wlc-chatbot"]').find('[id="' + errorId + '"]').text('');
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
        setTimeout(function () { $popup.find('[id="wlc-name"]').trigger('focus'); }, 320);
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
