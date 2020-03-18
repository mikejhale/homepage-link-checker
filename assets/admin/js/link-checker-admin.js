jQuery(function($) {
    $('#checklinks').on("submit", function(e) {
        $("#submit").attr('disabled', 'disabled');
        $("#link-results").empty();
    return true;
    });
});
