function acceptMap(id, action) {
    var reason = prompt("Reason?", "");
    if (reason == null || reason == "") {
        alert("Aborted");
    }
    else {
        $.post("/ajax/uploader/", { action: action, id: id, reason: reason }).done(function () {
            location.reload();
        });
    }
}
$(document).ready(function () {
    $('tr.hidden').hide();
    $('td.status').click(function () {
        $('#reason-' + $(this).attr('data-id')).slideToggle(400);
    });
});