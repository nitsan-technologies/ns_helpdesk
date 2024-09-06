import $ from "jquery";

$(document).on('click','.ns-bitbucket',function(){
        $('.modal-backdrop').hide();
        $('.category-changed').css('background-color',"rgba(0,0,0,0.4)");
    });

    $('.field-info-trigger').on('click', function(){
        $(this).parents('.form-group').find('.field-info-text').slideToggle();
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('#TypoScriptTemplateModuleController').on('submit',function(e){
        e.preventDefault();
        let url;
        url = $(this).attr('action');
        $.ajax({
            url:url,
            method:'post',
            data:$(this).serializeArray(),
            beforeSend: function () {
                $('.loader').show();
            },
            success:function() {
                window.location.reload();
            },
            complete: function (){
                $('.alert').css({"display":"block", "opacity":"1"});
            }
        })
    });
    $('#saveAssignment').on('submit',function(e){
        e.preventDefault();
        $('#TypoScriptTemplateModuleController').submit();
        url = $(this).attr('action');
        $.ajax({
            url:url,
            method:'post',
            data:$(this).serializeArray(),
            beforeSend: function () {
                $('.loader').show();
            },
            success:function() {
                window.location.reload();
            }
        })
    });

    $(document).on('click','.modal-btn-close', function (e) {
        $.ajax({
            method: 'POST',
            url: $(this).attr('href'),
            beforeSend: function () {
                $('.loader-wraper').show();
            },
            success: function (data) {
                window.location.reload();
            },
            complete: function () {

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
function getTicketsList(pageNo){
    var searchText = $("#sword").val();
    var statusArray = [];
    $('input[name="tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[ticketTypes]"]:checked').each(function() {
        typeArray.push(this.value);
    });
    $('input[name="tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[ticketStatus]"]:checked').each(function() {
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
        success: function(response){
            let new_html = $(response).find('.ns_helpdesk--tickets');
            $('.ns_helpdesk--tickets').html(new_html);
        },
    })
}
