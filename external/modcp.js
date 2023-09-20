$(document).ready(function (e) {
    // Search
    $('#sb').click(function () {
        var args = $('#st').val().split(/\s/);
        var url = "/modcp/" + $(this).attr('data-page') + "/";
        for (var i = 0; i < args.length; i++) {
            url = url + encodeURIComponent(args[i]) + "/";
        }
        window.location = url;
    });
});