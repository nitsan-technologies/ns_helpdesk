$(document).ready(function () {
        
    let starLi = $('#stars li');
    /* 1. Visualizing things on Hover - See next part for action on click */
    starLi.on('mouseover', function(){
        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star').each(function(e){
            if (e < onStar) {
                $(this).addClass('hover');
            }
            else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function(){
        $(this).parent().children('li.star').each(function(e){
            $(this).removeClass('hover');
        });
    });

    /* 2. Action to perform on click */
    starLi.on('click', function(){
        var onStar = parseInt($(this).data('value'), 10); // The star currently selected
        var stars = $(this).parent().children('li.star');
        for (i = 0; i < stars.length; i++) {
            $(stars[i]).removeClass('selected');
        }
        for (i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }
    });
});
$('.field-info-trigger').on('click', function(){
    $(this).parents('.form-group').find('.field-info-text').slideToggle();
});
//Ticket Create Form
$(document).on('submit', '.ticketGenerationForm', function (event) {
    if (validateHelpdeskFields(this)) {
        formAjax(this);
    }
    event.preventDefault();
});

$(document).on('click','.modal-btn-close', function (e) {
   $.ajax({
       method: 'POST',
       url: $(this).attr('href'),
       data: {rating: $('#stars li.selected').length},
       beforeSend: function () {
           $('.loader-wraper').show();
       },
       success: function (data) {
           data = JSON.parse(data);
       },
       complete: function () {
           $('.loader-wraper').hide();
           window.location.reload();
       },
       error: function (e) {
           alert("There is some internal issue, please contact to Administrator");
       }
   })
    e.preventDefault();
});

/*****
 * All Custom functions
 */

function formAjax(elem) {
    let form_data = new FormData(elem);
    $.ajax({
        method: 'POST',
        url: $(elem).attr('action'),
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('.loader-wraper').show();
        },
        success: function (data) {
            if ($(elem).hasClass('nshelpdesk-popup-form')){
                $(elem).hide();
            } else {
                $(elem).remove();
            }
            $('.nshelpdesk-success-msg').show();
        },
        complete: function () {
            $('.loader-wraper').hide();
        },
        error: function (e) {
            alert("There is some internal issue, please contact to Administrator");
        }
    });
}

function resetFormData(elem) {
    $(elem).show();
    $(elem)[0].reset();
    $('.nshelpdesk-success-msg').hide();
    if ($('.g-recaptcha').length > 0) {
        grecaptcha.reset();
    }
}

function getTicketsList(pageNo) {
    var searchText = $("#sword").val();
    var statusArray = [];
    $('input[name="tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[ticketStatus]"]:checked').each(function () {
        statusArray.push(this.value);
    });
    var param = {
        "tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[sword]": searchText,
        "tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[ticketStatus]": statusArray,
    };
    // $(".custom-model-loader").show();
    var url = $('#ajaxFilter').attr('href');
    $.ajax({
        type: "POST",
        url: url,
        data: param,
        success: function (response) {
            let new_html = $(response).find('.ns_helpdesk--tickets');
            $('.ns_helpdesk--tickets').html(new_html);
        },
    })
}

// Custom Validation
function validateHelpdeskFields(elem) {
    let isValid = true;
    let fileSize = 0;
    $($(elem).find('.validate-this')).each(function () {
        if ($(this).val() === '') {
            $(this).parent().addClass('has-error');
            $(this).next('.error-required').show();
            isValid = false;
        } else {
            $(this).parent().removeClass('has-error');
            $(this).next('.error-required').hide();
            $(this).next().next('.error-valid').hide();
        }
        if ($(this).hasClass('email-validation') && $(this).val()) {
            if (!validateHelpdeskEmail($(this).val())) {
                $(this).parent().addClass('has-error');
                $(this).next().next('.error-valid').show();
                isValid = false;
            } else {
                $(this).parent().removeClass('has-error');
                $(this).next().next('.error-valid').hide();
            }
        }
        if ($(this).hasClass('password_repeat') && $(this).val()) {
            if ($(this).val() !== $('.password').val()) {
                $(this).parent().addClass('has-error');
                $(this).next().next('.error-valid').show();
                isValid = false;
            } else {
                $(this).parent().removeClass('has-error');
                $(this).next().next('.error-valid').hide();
            }
        }
    });
    $($(elem).find('.email-validation')).each(function () {
        if ($(this).hasClass('email-validation')) {
            if (!validateHelpdeskEmail($(this).val())) {
                $(this).parent().addClass('has-error');
                $(this).next().next('.error-valid').show();
                isValid = false;
            } else {
                $(this).parent().removeClass('has-error');
                $(this).next().next('.error-valid').hide();
            }
        }
    })

    if (isValid) {
        if ($(elem).find('.form_userGroup').length > 0) {
            let alert = $(elem).find('.ns-helpdesk-alert');
            if ($(elem).find('.form_userGroup').val() === '' || $(elem).find('.form_userGroup').val() === '0') {
                isValid = false;
                alert.slideDown();
            }
        }
    }
    if (!validateMinChar($('.name-validation'))) {
        isValid = false;
    }
    if (!validateMinChar($('.textarea-validation'))) {
        isValid = false;
    }
    $('.has-error .validate-this:first').focus();
    return isValid;
}

function validateMinChar(elem) {
    if (elem.length > 0 && elem.val()) {
        if (elem.val().length < elem.attr('minchar')) {
            elem.parent().addClass('has-error');
            elem.next().next('.error-valid').show();
            return false;
        } else {
            elem.parent().removeClass('has-error');
            elem.next().next('.error-valid').hide();
        }
    }
    return true;
}

// Validate Email field
function validateHelpdeskEmail($email) {
    var expression = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    // var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return expression.test(String($email).toLowerCase());
}