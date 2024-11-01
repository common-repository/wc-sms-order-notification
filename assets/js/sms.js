jQuery(document).ready(function ($) {
    $('div#get_balance').click(function () {
        $('#result').attr('style', 'display:block');
    });
    $('.close_bl').click(function () {
        $('#result').attr('style', 'display:none');
    });
});